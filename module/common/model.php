<?php
/**
 * The model file of common module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     common
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class commonModel extends model
{
    static public $requestErrors = array();

    /**
     * The construc method, to do some auto things.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if(!defined('FIRST_RUN'))
        {
            define('FIRST_RUN', true);
            $this->sendHeader();
            $this->setCompany();
            $this->setUser();
            $this->setApproval();
            $this->loadConfigFromDB();
            $this->app->setTimezone();
            $this->loadCustomFromDB();
            if(!$this->checkIP()) return print($this->lang->ipLimited);
            $this->app->loadLang('company');
        }
    }

    /**
     * Set the status of execution, project, and program to doing.
     *
     * @param  int    $objectID
     * @access public
     * @return void
     */
    public function syncPPEStatus($objectID)
    {
        global $app;
        $rawModule = $app->rawModule;

        if($rawModule == 'task' or $rawModule == 'effort')
        {
            $taskID    = $objectID;
            $execution = $this->syncExecutionStatus($taskID);
            $project   = $this->syncProjectStatus($execution);
            $this->syncProgramStatus($project);
        }
        if($rawModule == 'execution')
        {
            $executionID = $objectID;
            $execution   = $this->dao->select('id, project, grade, parent, status, deleted')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->fetch();
            $this->syncExecutionByChild($execution);
            $project     = $this->syncProjectStatus($execution);
            $this->syncProgramStatus($project);
        }
        if($rawModule == 'project')
        {
            $projectID = $objectID;
            $project   = $this->dao->select('id, parent, path')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();
            $this->syncProgramStatus($project);
        }
        if($rawModule == 'program' and $this->config->systemMode == 'ALM')
        {
            $programID = $objectID;
            $program   = $this->dao->select('id, parent, path')->from(TABLE_PROGRAM)->where('id')->eq($programID)->fetch();
            $this->syncProgramStatus($program);
        }
    }

   /**
     * Set the status of the program to which theproject is linked as Ongoing.
     *
     * @param  object   $project
     * @access public
     * @return void
     */
    public function syncProgramStatus($project)
    {
        if($project->parent == 0) return;

        $parentPath = str_replace(",{$project->id},", '', $project->path);
        $parentPath = explode(',', trim($parentPath, ','));
        $waitList   = $this->dao->select('id')->from(TABLE_PROGRAM)
            ->where('id')->in($parentPath)
            ->andWhere('status')->eq('wait')
            ->orderBy('id_desc')
            ->fetchPairs();
        $now = helper::now();
        $this->dao->update(TABLE_PROGRAM)->set('status')->eq('doing')->set('realBegan')->eq($now)->where('id')->in($waitList)->exec();
        foreach($waitList as $programID)
        {
            $this->loadModel('action')->create('program', $programID, 'syncprogram');
        }
    }

    /**
     * Set the status of the project to which the execution is linked as Ongoing.
     *
     * @param  object  $execution
     * @access public
     * @return object  $project
     */
    public function syncProjectStatus($execution)
    {
        $projectID = $execution->project;
        $project   = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();

        $today = helper::today();
        if($project->status == 'wait')
        {
            $this->dao->update(TABLE_PROJECT)
                 ->set('status')->eq('doing')
                 ->beginIf(helper::isZeroDate($project->realBegan))->set('realBegan')->eq($today)->fi()
                 ->where('id')->eq($projectID)
                 ->exec();

            $this->loadModel('action')->create('project', $projectID, 'syncproject');
        }
        return $project;
    }

    /**
     * Set the status of the execution to which the sub execution is linked as Ongoing.
     *
     * @param  object $execution
     * @access public
     * @return object $parentExecution
     */
    public function syncExecutionByChild($execution)
    {
        if($execution->grade == 1) return false;

        $parentExecutionID = $execution->parent;
        $today = helper::today();
        $parentExecution = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($parentExecutionID)->fetch();

        if($execution->deleted == '0' and $execution->status == 'doing' and in_array($parentExecution->status, array('wait', 'closed')))
        {
            $this->dao->update(TABLE_EXECUTION)
                 ->set('status')->eq('doing')
                 ->beginIf(helper::isZeroDate($parentExecution->realBegan))->set('realBegan')->eq($today)->fi()
                 ->where('id')->eq($parentExecutionID)
                 ->exec();
            $this->loadModel('action')->create('execution', $parentExecutionID, 'syncexecutionbychild');
        }

        if($parentExecution->type == 'stage')
        {
            $childExecutions = $this->dao->select('*')->from(TABLE_EXECUTION)->where('parent')->eq($parentExecutionID)->andWhere('deleted')->eq('0')->fetchAll('id');
            if($execution->deleted == '1' and count($childExecutions) > 0)
            {
                $childWait   = true;
                $childClosed = true;
                foreach($childExecutions as $childExecution)
                {
                    if($childExecution->status != 'wait')   $childWait = false;
                    if($childExecution->status != 'closed') $childClosed = false;
                }

                if($childWait and $parentExecution->status != 'wait')
                {
                    $this->dao->update(TABLE_EXECUTION)->set('status')->eq('wait')->where('id')->eq($parentExecutionID)->exec();
                    $this->loadModel('action')->create('execution', $parentExecutionID, 'waitbychilddelete');
                }
                if($childClosed and $parentExecution->status != 'closed')
                {
                    $this->dao->update(TABLE_EXECUTION)->set('status')->eq('closed')->where('id')->eq($parentExecutionID)->exec();
                    $this->loadModel('action')->create('execution', $parentExecutionID, 'closebychilddelete');
                }
            }
        }

        return $parentExecution;
    }

    /**
     * Set the status of the execution to which the task is linked as Ongoing.
     *
     * @param  int    $taskID
     * @access public
     * @return object $execution
     */
    public function syncExecutionStatus($taskID)
    {
        $execution = $this->dao->select('t1.*')->from(TABLE_EXECUTION)->alias('t1')
            ->leftJoin(TABLE_TASK)->alias('t2')->on('t1.id=t2.execution')
            ->where('t2.id')->eq($taskID)
            ->fetch();

        $today = helper::today();
        if($execution->status == 'wait')
        {
            $this->dao->update(TABLE_EXECUTION)->set('status')->eq('doing')->set('realBegan')->eq($today)->where('id')->eq($execution->id)->exec();
            $this->loadModel('action')->create('execution', $execution->id, 'syncexecution');
            if($execution->parent)
            {
                $execution = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($execution->id)->fetch(); // Get updated execution.
                $this->syncExecutionByChild($execution);
            }
        }
        return $execution;
    }

    /**
     * Set the header info.
     *
     * @access public
     * @return void
     */
    public function sendHeader()
    {
        header("Content-Type: text/html; Language={$this->config->charset}");
        header("Cache-control: private");

        /* Send HTTP header. */
        if($this->config->framework->sendXCTO)  header("X-Content-Type-Options: nosniff");
        if($this->config->framework->sendXXP)   header("X-XSS-Protection: 1; mode=block");
        if($this->config->framework->sendHSTS)  header("Strict-Transport-Security: max-age=3600; includeSubDomains");
        if($this->config->framework->sendRP)    header("Referrer-Policy: no-referrer-when-downgrade");
        if($this->config->framework->sendXPCDP) header("X-Permitted-Cross-Domain-Policies: master-only");
        if($this->config->framework->sendXDO)   header("X-Download-Options: noopen");

        /* Set Content-Security-Policy header. */
        if($this->config->CSPs)
        {
            foreach($this->config->CSPs as $CSP) header("Content-Security-Policy: $CSP;");
        }

        if($this->loadModel('setting')->getItem('owner=system&module=sso&key=turnon'))
        {
            if(isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == 'on')
            {
                $session = $this->config->sessionVar . '=' . session_id();
                header("Set-Cookie: $session; SameSite=None; Secure=true", false);
            }
        }
        else
        {
            if(!empty($this->config->xFrameOptions)) header("X-Frame-Options: {$this->config->xFrameOptions}");
        }
    }

    /**
     * Set the commpany.
     *
     * First, search company by the http host. If not found, search by the default domain. Last, use the first as the default.
     * After get the company, save it to session.
     * @access public
     * @return void
     */
    public function setCompany()
    {
        $httpHost = $this->server->http_host;

        if($this->session->company)
        {
            $this->app->company = $this->session->company;
        }
        else
        {
            $company = $this->loadModel('company')->getFirst();
            if(!$company) $this->app->triggerError(sprintf($this->lang->error->companyNotFound, $httpHost), __FILE__, __LINE__, $exit = true);
            $this->session->set('company', $company);
            $this->app->company  = $company;
        }
    }

    /**
     * Set the user info.
     *
     * @access public
     * @return void
     */
    public function setUser()
    {
        if($this->session->user)
        {
            if(!defined('IN_UPGRADE')) $this->session->user->view = $this->loadModel('user')->grantUserView();
            $this->app->user = $this->session->user;
        }
        elseif($this->app->company->guest or PHP_SAPI == 'cli')
        {
            $user             = new stdClass();
            $user->id         = 0;
            $user->account    = 'guest';
            $user->realname   = 'guest';
            $user->dept       = 0;
            $user->avatar     = '';
            $user->role       = 'guest';
            $user->admin      = false;
            $user->rights     = $this->loadModel('user')->authorize('guest');
            $user->groups     = array('group');
            $user->visions    = $this->config->vision;
            if(!defined('IN_UPGRADE')) $user->view = $this->user->grantUserView($user->account, $user->rights['acls']);
            $this->session->set('user', $user);
            $this->app->user = $this->session->user;
        }
    }

    /**
     * Set approval config.
     *
     * @access public
     * @return void
     */
    public function setApproval()
    {
        $this->config->openedApproval = false;
        if($this->config->edition == 'max' && $this->config->vision == 'rnd') $this->config->openedApproval = true;
    }

    /**
     * Load configs from database and save it to config->system and config->personal.
     *
     * @access public
     * @return void
     */
    public function loadConfigFromDB()
    {
        /* Get configs of system and current user. */
        $account = isset($this->app->user->account) ? $this->app->user->account : '';
        if($this->config->db->name) $config = $this->loadModel('setting')->getSysAndPersonalConfig($account);
        $this->config->system   = isset($config['system']) ? $config['system'] : array();
        $this->config->personal = isset($config[$account]) ? $config[$account] : array();

        /* Overide the items defined in config/config.php and config/my.php. */
        if(isset($this->config->system->common))   $this->app->mergeConfig($this->config->system->common, 'common');
        if(isset($this->config->personal->common)) $this->app->mergeConfig($this->config->personal->common, 'common');

        $this->config->disabledFeatures = $this->config->disabledFeatures . ',' . $this->config->closedFeatures;
    }

    /**
     * Load custom lang from db.
     *
     * @access public
     * @return void
     */
    public function loadCustomFromDB()
    {
        $this->loadModel('custom');

        if(defined('IN_UPGRADE')) return;
        if(!$this->config->db->name) return;

        $records = $this->custom->getAllLang();
        if(!$records) return;

        $this->lang->db = new stdclass();
        $this->lang->db->custom = $records;
    }

    /**
     * Juage a method of one module is open or not?
     *
     * @param  string $module
     * @param  string $method
     * @access public
     * @return bool
     */
    public function isOpenMethod($module, $method)
    {
        if(in_array("$module.$method", $this->config->openMethods)) return true;

        if($module == 'block' and $method == 'main' and isset($_GET['hash'])) return true;

        if($this->loadModel('user')->isLogon() or ($this->app->company->guest and $this->app->user->account == 'guest'))
        {
            if(stripos($method, 'ajax') !== false) return true;
            if($module == 'block') return true;
            if($module == 'my' and $method == 'guidechangetheme') return true;
            if($module == 'misc' and $method == 'downloadclient') return true;
            if($module == 'misc' and $method == 'changelog')  return true;
            if($module == 'tutorial' and $method == 'start')  return true;
            if($module == 'tutorial' and $method == 'index')  return true;
            if($module == 'tutorial' and $method == 'quit')   return true;
            if($module == 'tutorial' and $method == 'wizard') return true;
            if($module == 'product' and $method == 'showerrornone') return true;
        }
        return false;
    }

    /**
     * Deny access.
     *
     * @param  varchar  $module
     * @param  varchar  $method
     * @param  bool     $reload
     * @access public
     * @return mixed
     */
    public function deny($module, $method, $reload = true)
    {
        if($reload)
        {
            /* Get authorize again. */
            $user = $this->app->user;
            $user->rights = $this->loadModel('user')->authorize($user->account);
            $user->groups = $this->user->getGroups($user->account);
            $user->admin  = strpos($this->app->company->admins, ",{$user->account},") !== false;
            $this->session->set('user', $user);
            $this->app->user = $this->session->user;
            if(commonModel::hasPriv($module, $method)) return true;
        }

        $vars = "module=$module&method=$method";
        if(isset($this->server->http_referer))
        {
            $referer = helper::safe64Encode($this->server->http_referer);
            $vars   .= "&referer=$referer";
        }
        $denyLink = helper::createLink('user', 'deny', $vars);

        /* Fix the bug of IE: use js locate, can't get the referer. */
        if(strpos($this->server->http_user_agent, 'Trident') !== false)
        {
            echo "<a href='$denyLink' id='denylink' style='display:none'>deny</a>";
            echo "<script>document.getElementById('denylink').click();</script>";
        }
        else
        {
            echo js::locate($denyLink);
        }
        helper::end();
    }

    /**
     * Print the run info.
     *
     * @param mixed $startTime  the start time.
     * @access public
     * @return array    the run info array.
     */
    public function printRunInfo($startTime)
    {
        $info['timeUsed'] = round(getTime() - $startTime, 4) * 1000;
        $info['memory']   = round(memory_get_peak_usage() / 1024, 1);
        $info['querys']   = count(dao::$querys);
        vprintf($this->lang->runInfo, $info);
        return $info;
    }

    /**
     * Print top bar.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printUserBar()
    {
        global $lang, $app;

        if(isset($app->user))
        {
            $isGuest = $app->user->account == 'guest';

            echo "<ul class='dropdown-menu pull-right'>";
            if(!$isGuest)
            {
                $noRole = (!empty($app->user->role) and isset($lang->user->roleList[$app->user->role])) ? '' : ' no-role';
                echo '<li class="user-profile-item">';
                echo "<a href='" . helper::createLink('my', 'profile', '', '', true) . "' data-width='700' class='iframe $noRole'" . '>';
                echo html::avatar($app->user, '', 'avatar-circle', 'id="menu-avatar"');
                echo '<div class="user-profile-name">' . (empty($app->user->realname) ? $app->user->account : $app->user->realname) . '</div>';
                if(isset($lang->user->roleList[$app->user->role])) echo '<div class="user-profile-role">' . $lang->user->roleList[$app->user->role] . '</div>';
                echo '</a></li><li class="divider"></li>';

                $vision = $app->config->vision == 'lite' ? 'rnd' : 'lite';

                echo '<li>' . html::a(helper::createLink('my', 'profile', '', '', true), "<i class='icon icon-account'></i> " . $lang->profile, '', "class='iframe' data-width='700'") . '</li>';

                if($app->config->vision === 'rnd')
                {
                    if(!commonModel::isTutorialMode())
                    {
                        echo '<li class="user-tutorial">' . html::a(helper::createLink('tutorial', 'start'), "<i class='icon icon-guide'></i> " . $lang->tutorialAB, '', "class='iframe' data-class-name='modal-inverse' data-width='800' data-headerless='true' data-backdrop='true' data-keyboard='true'") . '</li>';
                    }

                    echo '<li>' . html::a(helper::createLink('my', 'preference', 'showTip=false', '', true), "<i class='icon icon-controls'></i> " . $lang->preference, '', "class='iframe' data-width='700'") . '</li>';
                }

                if(common::hasPriv('my', 'changePassword')) echo '<li>' . html::a(helper::createLink('my', 'changepassword', '', '', true), "<i class='icon icon-cog-outline'></i> " . $lang->changePassword, '', "class='iframe' data-width='600'") . '</li>';

                echo "<li class='divider'></li>";
            }

            echo "<li class='dropdown-submenu top'>";
            echo "<a href='javascript:;'>" . "<i class='icon icon-theme'></i> " . $lang->theme . "</a><ul class='dropdown-menu pull-left'>";
            foreach($app->lang->themes as $key => $value)
            {
                echo "<li " . ($app->cookie->theme == $key ? "class='selected'" : '') . "><a href='javascript:selectTheme(\"$key\");' data-value='" . $key . "'>" . $value . "</a></li>";
            }
            echo '</ul></li>';

            echo "<li class='dropdown-submenu top'>";
            echo "<a href='javascript:;'>" . "<i class='icon icon-lang'></i> " . $lang->lang . "</a><ul class='dropdown-menu pull-left'>";
            foreach ($app->config->langs as $key => $value)
            {
                echo "<li " . ($app->cookie->lang == $key ? "class='selected'" : '') . "><a href='javascript:selectLang(\"$key\");'>" . $value . "</a></li>";
            }
            echo '</ul></li>';

            //if(!$isGuest and !commonModel::isTutorialMode() and $app->viewType != 'mhtml')
            //{
            //    $customLink = helper::createLink('custom', 'ajaxMenu', "module={$app->getModuleName()}&method={$app->getMethodName()}", '', true);
            //    echo "<li class='custom-item'><a href='$customLink' data-toggle='modal' data-type='iframe' data-icon='cog' data-width='80%'>$lang->customMenu</a></li>";
            //}

            commonModel::printAboutBar();
            echo '<li class="divider"></li>';
            echo '<li>';
            if($isGuest)
            {
                echo html::a(helper::createLink('user', 'login'), $lang->login, '_top');
            }
            else
            {
                echo html::a(helper::createLink('user', 'logout'), "<i class='icon icon-exit'></i> " . $lang->logout, '_top');
            }
            echo '</li></ul>';

            echo "<a class='dropdown-toggle' data-toggle='dropdown'>";
            echo html::avatar($app->user);
            echo '</a>';
        }
    }

    /**
     * Print vision switcher.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printVisionSwitcher()
    {
        global $lang, $app, $config;

        if(isset($app->user))
        {
            if(!isset($app->user->visions)) $app->user->visions = trim($config->visions, ',');
            $currentVision = $app->config->vision;
            $userVisions   = array_filter(explode(',', $app->user->visions));
            $configVisions = array_filter(explode(',', trim($config->visions, ',')));

            /* The standalone lite version removes the lite interface button */
            if(trim($config->visions, ',') == 'lite') return true;

            if(count($userVisions) < 2)   return print("<div>{$lang->visionList[$currentVision]}</div>");
            if(count($configVisions) < 2) return print("<div>{$lang->visionList[$currentVision]}</div>");

            echo "<ul class='dropdown-menu pull-right'>";
            echo "<li class='text-gray switchTo'>{$lang->switchTo}</li>";
            foreach($userVisions as $vision)
            {
                echo ($currentVision == $vision ? '<li class="active">' : '<li>') . html::a(helper::createLink('my', 'ajaxSwitchVision', "vision=$vision"), $lang->visionList[$vision], '', "data-type='ajax'") . '</li>';
            }
            echo '</ul>';

            echo "<a class='dropdown-toggle' data-toggle='dropdown'>";
            echo "<div>{$lang->visionList[$currentVision]}</div>";
            echo '</a>';
        }
    }

    /**
     * Print create button list.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printCreateList()
    {
        global $app, $config, $lang;

        $html = "<ul class='dropdown-menu pull-right create-list'>";

        /* Initialize the default values. */
        $showCreateList = $needPrintDivider = false;

        /* Get default product id. */
        $productID = isset($_SESSION['product']) ? $_SESSION['product'] : 0;
        if($productID)
        {
            $product = $app->dbh->query("SELECT id  FROM " . TABLE_PRODUCT . " WHERE `deleted` = '0' and vision = '{$config->vision}' and id = '{$productID}'")->fetch();
            if(empty($product)) $productID = 0;
        }
        if(!$productID and $app->user->view->products)
        {
            $product = $app->dbh->query("SELECT id FROM " . TABLE_PRODUCT . " WHERE `deleted` = '0' and vision = '{$config->vision}' and id " . helper::dbIN($app->user->view->products) . " order by `order` desc limit 1")->fetch();
            if($product) $productID = $product->id;
        }

        if($config->vision == 'lite')
        {
            $condition  = " WHERE `deleted` = '0' AND `vision` = 'lite' AND `model` = 'kanban'";
            if(!$app->user->admin) $condition .= " AND `id` " . helper::dbIN($app->user->view->projects);

            $object = $app->dbh->query("select id from " . TABLE_PROJECT . $condition . ' LIMIT 1')->fetch();
            if(empty($object)) unset($lang->createIcons['story'], $lang->createIcons['task'], $lang->createIcons['execution']);
        }

        if($config->edition == 'open')     unset($lang->createIcons['effort']);
        if($config->systemMode == 'light') unset($lang->createIcons['program']);

        /* Check whether the creation permission is available, and print create buttons. */

        foreach($lang->createIcons as $objectType => $objectIcon)
        {
            $createMethod = 'create';
            $module       = $objectType == 'kanbanspace' ? 'kanban' : $objectType;
            if($objectType == 'effort') $createMethod = 'batchCreate';
            if($objectType == 'kanbanspace') $createMethod = 'createSpace';
            if(strpos('|bug|execution|kanbanspace|', "|$objectType|") !== false) $needPrintDivider = true;

            if(common::hasPriv($module, $createMethod))
            {
                if($objectType == 'doc' and !common::hasPriv('doc', 'tableContents')) continue;

                /* Determines whether to print a divider. */
                if($needPrintDivider and $showCreateList)
                {
                    $html            .= '<li class="divider"></li>';
                    $needPrintDivider = false;
                }

                $showCreateList = true;
                $isOnlyBody     = false;
                $attr           = '';

                $params = '';
                switch($objectType)
                {
                    case 'doc':
                        $params       = "objectType=&objectID=0&libID=0";
                        $createMethod = 'selectLibType';
                        $isOnlyBody   = true;
                        $attr         = "class='iframe' data-width='700px'";
                        break;
                    case 'project':
                        if($config->vision == 'lite')
                        {
                            $params = "model=kanban";
                        }
                        else if(!defined('TUTORIAL'))
                        {
                            $params       = "programID=0&from=global";
                            $createMethod = 'createGuide';
                            $attr         = 'data-toggle="modal"';
                        }
                        else
                        {
                            $params = "model=scrum&programID=0&copyProjectID=0&extra=from=global";
                        }

                        break;
                    case 'bug':
                        $params = "productID=$productID&branch=&extras=from=global";
                        break;
                    case 'story':
                        if(!$productID and $config->vision == 'lite')
                        {
                            $module = 'project';
                            $params = "model=kanban";
                        }
                        else
                        {
                            $params = "productID=$productID&branch=0&moduleID=0&storyID=0&objectID=0&bugID=0&planID=0&todoID=0&extra=from=global";
                            if($config->vision == 'lite')
                            {
                                $projectID = isset($_SESSION['project']) ? $_SESSION['project'] : 0;
                                $projects  = $app->dbh->query("SELECT t2.id FROM " . TABLE_PROJECTPRODUCT . " AS t1 LEFT JOIN " . TABLE_PROJECT . " AS t2 ON t1.project = t2.id WHERE t1.`product` = '{$productID}' and t2.`type` = 'project' and t2.id " . helper::dbIN($app->user->view->projects) . " ORDER BY `order` desc")->fetchAll();

                                $projectIdList = array();
                                foreach($projects as $project) $projectIdList[$project->id] = $project->id;
                                if($projectID and !isset($projectIdList[$projectID])) $projectID = 0;
                                if(empty($projectID)) $projectID = key($projectIdList);

                                $params = "productID={$productID}&branch=0&moduleID=0&storyID=0&objectID={$projectID}&bugID=0&planID=0&todoID=0&extra=from=global";
                            }
                        }

                        break;
                    case 'task':
                        $params = "executionID=0&storyID=0&moduleID=0&taskID=0&todoID=0&extra=from=global";
                        break;
                    case 'testcase':
                        $params = "productID=$productID&branch=&moduleID=0&from=&param=0&storyID=0&extras=from=global";
                        break;
                    case 'execution':
                        $projectID = isset($_SESSION['project']) ? $_SESSION['project'] : 0;
                        $params = "projectID={$projectID}&executionID=0&copyExecutionID=0&planID=0&confirm=no&productID=0&extra=from=global";
                        break;
                    case 'product':
                        $params = "programID=&extra=from=global";
                        break;
                    case 'program':
                        $params = "parentProgramID=0&extra=from=global";
                        break;
                    case 'kanbanspace':
                        $isOnlyBody = true;
                        $attr       = "class='iframe' data-width='75%'";
                        break;
                    case 'kanban':
                        $isOnlyBody = true;
                        $attr       = "class='iframe' data-width='75%'";
                        break;
                }

                $html .= '<li>' . html::a(helper::createLink($module, $createMethod, $params, '', $isOnlyBody), "<i class='icon icon-$objectIcon'></i> " . $lang->createObjects[$objectType], '', $attr) . '</li>';
            }
        }

        if(!$showCreateList) return '';

        $html .= "</ul>";
        $html .= "<a class='dropdown-toggle' data-toggle='dropdown'>";
        $html .= "<i class='icon icon-plus-solid-circle text-secondary'></i>";
        $html .= "</a>";

        echo $html;
    }

    /**
     * Print about bar.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printAboutBar()
    {
        global $app, $config, $lang;
        echo "<li class='dropdown-submenu'>";
        echo "<a data-toggle='dropdown'>" . "<i class='icon icon-help'></i> " . $lang->help . "</a>";
        echo "<ul class='dropdown-menu pull-left'>";

        $manualUrl = ((!empty($config->isINT)) ? $config->manualUrl['int'] : $config->manualUrl['home']) . '&theme=' . $_COOKIE['theme'];
        echo '<li>' . html::a($manualUrl, $lang->manual, '', "class='show-in-app' id='helpLink' data-app='help'") . '</li>';

        echo '<li>' . html::a(helper::createLink('misc', 'changeLog'), $lang->changeLog, '', "class='iframe' data-width='800' data-headerless='true' data-backdrop='true' data-keyboard='true'") . '</li>';
        echo "</ul></li>\n";

        self::printClientLink();

        echo '<li>' . html::a(helper::createLink('misc', 'about'), "<i class='icon icon-about'></i> " . $lang->aboutZenTao, '', "class='about iframe' data-width='1050' data-headerless='true' data-backdrop='true' data-keyboard='true' data-class='modal-about'") . '</li>';
        echo '<li>' . $lang->designedByAIUX . '</li>';
    }

    /**
     * Create menu item link
     *
     * @param object $menuItem
     *
     * @static
     * @access public
     * @return string
     */
    public static function createMenuLink($menuItem, $group)
    {
        global $app;
        $link = $menuItem->link;
        if(is_array($menuItem->link))
        {
            $vars = isset($menuItem->link['vars']) ? $menuItem->link['vars'] : '';
            if(isset($menuItem->tutorial) and $menuItem->tutorial)
            {
                if(!empty($vars)) $vars = helper::safe64Encode($vars);
                $link = helper::createLink('tutorial', 'wizard', "module={$menuItem->link['module']}&method={$menuItem->link['method']}&params=$vars");
            }
            else
            {
                $link = helper::createLink($menuItem->link['module'], $menuItem->link['method'], $vars);
            }
        }
        return $link;
    }

    /**
     * Create sub menu by settings in lang files.
     *
     * @param  array    $items
     * @param  mixed    $replace
     * @static
     * @access public
     * @return array
     */
    public static function createDropMenu($items, $replace)
    {
        $dropMenu = array();
        foreach($items as $dropMenuKey => $dropMenuLink)
        {
            if(is_array($dropMenuLink) and isset($dropMenuLink['link'])) $dropMenuLink = $dropMenuLink['link'];
            if(is_array($replace))
            {
                $dropMenuLink = vsprintf($dropMenuLink, $replace);
            }
            else
            {
                $dropMenuLink = sprintf($dropMenuLink, $replace);
            }
            list($dropMenuName, $dropMenuModule, $dropMenuMethod, $dropMenuParams) = explode('|', $dropMenuLink);

            $link = array();
            $link['module'] = $dropMenuModule;
            $link['method'] = $dropMenuMethod;
            $link['vars']   = $dropMenuParams;

            $dropMenuItem     = isset($items->$dropMenuKey) ? $items->$dropMenuKey : array();
            $menu            = new stdclass();
            $menu->name      = $dropMenuKey;
            $menu->link      = $link;
            $menu->text      = $dropMenuName;
            $menu->subModule = isset($dropMenuItem['subModule']) ? $dropMenuItem['subModule'] : '';
            $menu->alias     = isset($dropMenuItem['alias'])     ? $dropMenuItem['alias'] : '';
            $menu->hidden    = false;
            $dropMenu[$dropMenuKey] = $menu;
        }

        return $dropMenu;
    }

    /**
     * Print admin dropMenu.
     *
     * @param  string    $dropMenu
     * @static
     * @access public
     * @return void
     */
    public static function printAdminDropMenu($dropMenu)
    {
        global $app, $lang;
        $currentModule = $app->getModuleName();
        $currentMethod = $app->getMethodName();
        if(isset($lang->admin->dropMenuOrder->$dropMenu))
        {
            ksort($lang->admin->dropMenuOrder->$dropMenu);
            foreach($lang->admin->dropMenuOrder->$dropMenu as $type)
            {
                if(isset($lang->admin->dropMenu->$dropMenu->$type))
                {
                    $subModule = '';
                    $alias     = '';
                    $link      = $lang->admin->dropMenu->$dropMenu->$type;
                    if(is_array($lang->admin->dropMenu->$dropMenu->$type))
                    {
                        $dropMenuType = $lang->admin->dropMenu->$dropMenu->$type;
                        if(isset($dropMenuType['subModule'])) $subModule = $dropMenuType['subModule'];
                        if(isset($dropMenuType['alias']))     $alias     = $dropMenuType['alias'];
                        if(isset($dropMenuType['link']))      $link      = $dropMenuType['link'];
                    }

                    list($text, $moduleName, $methodName)= explode('|', $link);
                    if(!common::hasPriv($moduleName, $methodName)) continue;

                    $active = ($currentModule == $moduleName and $currentMethod == $methodName) ? 'btn-active-text' : '';
                    if($subModule and strpos(",{$subModule}," , ",{$currentModule},") !== false) $active = 'btn-active-text';
                    if($alias and $currentModule == $moduleName and strpos(",$alias,", ",$currentMethod,") !== false) $active = 'btn-active-text';
                    echo html::a(helper::createLink($moduleName, $methodName), "<span class='text'>$text</span>", '', "class='btn btn-link {$active}' id='{$type}Tab'");
                }
            }
        }
    }

    /**
     * Print the main nav.
     *
     * @param  string $moduleName
     *
     * @static
     * @access public
     * @return void
     */
    public static function printMainNav($moduleName)
    {
        $items = common::getMainNavList($moduleName);
        foreach($items as $item)
        {
            if($item == 'divider')
            {
                echo "<li class='divider'></li>";
            }
            else
            {
                $active = $item->active ? ' class="active"' : '';
                echo "<li$active>" . html::a($item->url, $item->title) . '</li>';
            }
        }
    }

    /**
     * Print upper left corner home button.
     *
     * @param  string $tab
     * @static
     * @access public
     * @return void
     */
    public static function printHomeButton($tab)
    {
        global $lang;
        global $config;

        if(!$tab) return;
        $icon = zget($lang->navIcons, $tab, '');

        if(!in_array($tab, array('program', 'product', 'project')))
        {
            $nav = $lang->mainNav->$tab;
            list($title, $currentModule, $currentMethod, $vars) = explode('|', $nav);
            if($tab == 'execution') $currentMethod = 'all';
        }
        else
        {
            $currentModule = $tab;
            if($tab == 'program' or $tab == 'project') $currentMethod = 'browse';
            if($tab == 'product') $currentMethod = 'all';
        }

        $link = helper::createLink($currentModule, $currentMethod);
        $className = $tab == 'devops' ? 'btn num' : 'btn';
        $html = $link ? html::a($link, "$icon {$lang->$tab->common}", '', "class='$className' style='padding-top: 2px'") : "$icon {$lang->$tab->common}";

        echo "<div class='btn-group header-btn'>" . $html . '</div>';
    }

    /**
     * Get main nav items list
     *
     * @param  string $moduleName
     *
     * @static
     * @access public
     * @return array
     */
    public static function getMainNavList($moduleName)
    {
        global $lang;
        global $app;

        $app->loadLang('my');

        $menuOrder = $lang->mainNav->menuOrder;
        ksort($menuOrder);

        $items        = array();
        $lastItem     = end($menuOrder);
        $printDivider = false;

        foreach($menuOrder as $key => $group)
        {
            $nav = $lang->mainNav->$group;
            list($title, $currentModule, $currentMethod, $vars) = explode('|', $nav);

            /* When last divider is not used in mainNav, use it next menu. */
            $printDivider = ($printDivider or ($lastItem != $key) and strpos($lang->dividerMenu, ",{$group},") !== false) ? true : false;
            if($printDivider and !empty($items))
            {
                $items[]      = 'divider';
                $printDivider = false;
            }

            /**
             * Judget the module display or not.
             *
             */
            $display = false;

            /* 1. The default rule. */
            if(common::hasPriv($currentModule, $currentMethod)) $display = true;

            /* 2. If the module is assetLib, need judge more methods. */
            if($currentModule == 'assetlib' and $display == false)
            {
                $methodList = array('caselib', 'issuelib', 'risklib', 'opportunitylib', 'practicelib', 'componentlib');
                foreach($methodList as $method)
                {
                    if(common::hasPriv($currentModule, $method))
                    {
                        $display       = true;
                        $currentMethod = $method;
                        break;
                    }
                }
            }

            /* Check whether other preference item under the module have permissions. If yes, point to other methods. */
            $moduleLinkList = $currentModule . 'LinkList';
            if(!$display and isset($lang->my->$moduleLinkList))
            {
                foreach($lang->my->$moduleLinkList as $key => $linkList)
                {
                    $method = explode('-', $key)[1];
                    if(common::hasPriv($currentModule, $method))
                    {
                        $display       = true;
                        $currentMethod = $method;
                        break;
                    }
                }
            }

            /* Check whether other methods under the module have permissions. If yes, point to other methods. */
            if($display == false and isset($lang->$currentModule->menu) and !in_array($currentModule, array('program', 'product', 'project', 'execution')))
            {
                foreach($lang->$currentModule->menu as $menu)
                {
                    if(!isset($menu['link'])) continue;

                    $linkPart = explode('|', $menu['link']);
                    if(!isset($linkPart[2])) continue;
                    $method = $linkPart[2];

                    /* Skip some pages that do not require permissions.*/
                    if($currentModule == 'report' and $method == 'annualData') continue;
                    if($currentModule == 'my' and $currentMethod == 'team') continue;

                    if(common::hasPriv($currentModule, $method))
                    {
                        $display       = true;
                        $currentMethod = $method;
                        if(!isset($menu['target'])) break; // Try to jump to the method without opening a new window.
                    }
                }
            }

            if(!$display) continue;

            /* Assign vars. */
            $item = new stdClass();
            $item->group      = $group;
            $item->code       = $group;
            $item->active     = zget($lang->navGroup, $moduleName, '') == $group or $moduleName != 'program' and $moduleName == $group;
            $item->title      = $title;
            $item->moduleName = $currentModule;
            $item->methodName = $currentMethod;
            $item->vars       = $vars;

            $isTutorialMode = commonModel::isTutorialMode();
            if($isTutorialMode and $currentModule == 'project')
            {
                if(!empty($vars)) $vars = helper::safe64Encode($vars);
                $item->url = helper::createLink('tutorial', 'wizard', "module={$currentModule}&method={$currentMethod}&params=$vars", '', 0, 0, 1);
            }
            else
            {
                $item->url = helper::createLink($currentModule, $currentMethod, $vars, '', 0, 0, 1);
            }

            $items[] = $item;
        }

        /* Fix bug 14574. */
        if(end($items) == 'divider') array_pop($items);

        return $items;
    }

    /**
     * Print the main menu.
     *
     * @static
     * @access public
     * @return string
     */
    public static function printMainMenu()
    {
        global $app, $lang, $config;

        /* Set main menu by app tab and module. */
        self::setMainMenu();
        self::checkMenuVarsReplaced();

        $activeMenu = '';
        $tab = $app->tab;

        $isTutorialMode = commonModel::isTutorialMode();
        $currentModule = $app->rawModule;
        $currentMethod = $app->rawMethod;

        if($isTutorialMode and defined('WIZARD_MODULE')) $currentModule  = WIZARD_MODULE;
        if($isTutorialMode and defined('WIZARD_METHOD')) $currentMethod  = WIZARD_METHOD;

        /* Print all main menus. */
        $menu     = customModel::getMainMenu();
        $lastMenu = end($menu);

        echo "<ul class='nav nav-default'>\n";
        foreach($menu as $menuItem)
        {
            if(isset($menuItem->hidden) and $menuItem->hidden and (!isset($menuItem->tutorial) or !$menuItem->tutorial)) continue;
            if(empty($menuItem->link)) continue;
            if($menuItem->divider) echo "<li class='divider'></li>";

            /* Init the these vars. */
            $alias     = isset($menuItem->alias) ? $menuItem->alias : '';
            $subModule = isset($menuItem->subModule) ? explode(',', $menuItem->subModule) : array();
            $class     = isset($menuItem->class) ? $menuItem->class : '';
            $exclude   = isset($menuItem->exclude) ? $menuItem->exclude : '';

            $active = '';
            if($menuItem->name == $currentModule and strpos(",$exclude,", ",$currentModule-$currentMethod,") === false)
            {
                $activeMenu = $menuItem->name;
                $active = 'active';
            }
            if($subModule and in_array($currentModule, $subModule) and strpos(",$exclude,", ",$currentModule-$currentMethod,") === false)
            {
                $activeMenu = $menuItem->name;
                $active = 'active';
            }

            if($menuItem->link['module'] == 'execution' and $menuItem->link['method'] == 'more')
            {
                $executionID = $menuItem->link['vars'];
                commonModel::buildMoreButton($executionID);
            }
            elseif($menuItem->link['module'] == 'app' and $menuItem->link['method'] == 'serverlink')
            {
                commonModel::buildAppButton();
            }
            else
            {
                if($menuItem->link)
                {
                    $target = '';
                    $module = '';
                    $method = '';
                    $link   = commonModel::createMenuLink($menuItem, $tab);

                    if($menuItem->link['module'] == 'project' and $menuItem->link['method'] == 'other') $link = 'javascript:void(0);';

                    if(is_array($menuItem->link))
                    {
                        if(isset($menuItem->link['target'])) $target = $menuItem->link['target'];
                        if(isset($menuItem->link['module'])) $module = $menuItem->link['module'];
                        if(isset($menuItem->link['method'])) $method = $menuItem->link['method'];
                    }
                    if($module == $currentModule and ($method == $currentMethod or strpos(",$alias,", ",$currentMethod,") !== false) and strpos(",$exclude,", ",$currentMethod,") === false)
                    {
                        $activeMenu = $menuItem->name;
                        $active = 'active';
                    }

                    $label    = $menuItem->text;
                    $dropMenu = '';
                    $misc     = (isset($lang->navGroup->$module) and $tab != $lang->navGroup->$module) ? "data-app='$tab'" : '';

                    /* Print drop menus. */
                    if(isset($menuItem->dropMenu))
                    {
                        foreach($menuItem->dropMenu as $dropMenuName => $dropMenuItem)
                        {
                            if(empty($dropMenuItem)) continue;
                            if(isset($dropMenuItem->hidden) and $dropMenuItem->hidden) continue;

                            /* Parse drop menu link. */
                            $dropMenuLink = zget($dropMenuItem, 'link', $dropMenuItem);

                            list($subLabel, $subModule, $subMethod, $subParams) = explode('|', $dropMenuLink);
                            if(!common::hasPriv($subModule, $subMethod)) continue;

                            $subLink = helper::createLink($subModule, $subMethod, $subParams);

                            $subActive = '';
                            $activeMainMenu = false;
                            if($currentModule == strtolower($subModule) and $currentMethod == strtolower($subMethod))
                            {
                                $activeMainMenu = true;
                            }
                            else
                            {
                                $subModule = isset($dropMenuItem['subModule']) ? explode(',', $dropMenuItem['subModule']) : array();
                                if($subModule and in_array($currentModule, $subModule) and strpos(",$exclude,", ",$currentModule-$currentMethod,") === false) $activeMainMenu = true;
                            }

                            if($activeMainMenu)
                            {
                                $activeMenu = $dropMenuName;
                                $active     = 'active';
                                $subActive  = 'active';
                                $label      = $subLabel;
                            }
                            $dropMenu .= "<li class='$subActive' data-id='$dropMenuName'>" . html::a($subLink, $subLabel, '', "data-app='$tab'") . '</li>';
                        }

                        if(empty($dropMenu)) continue;

                        $label    .= "<span class='caret'></span>";
                        $dropMenu  = "<ul class='dropdown-menu'>{$dropMenu}</ul>";

                        echo "<li class='$class $active' data-id='$menuItem->name'>" . html::a($link, $label, $target, $misc) . $dropMenu . "</li>\n";
                    }
                    else
                    {
                        echo "<li class='$class $active' data-id='$menuItem->name'>" . html::a($link, $label, $target, $misc) . "</li>\n";
                    }
                }
                else
                {
                    echo "<li class='$class $active' data-id='$menuItem->name'>$menuItem->text</li>\n";
                }
            }
        }

        echo "</ul>\n";

        return $activeMenu;
    }

    /**
     * Print the search box.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printSearchBox()
    {
        global $lang;
        global $config;

        $searchObject = 'bug';
        echo "<div class='input-group-btn'>";
        echo html::hidden('searchType', $searchObject);
        echo "<ul id='searchTypeMenu' class='dropdown-menu'>";

        $searchObjects = $lang->searchObjects;
        if($config->systemMode == 'light') unset($searchObjects['program']);

        foreach($searchObjects as $key => $value)
        {
            $class = $key == $searchObject ? "class='selected'" : '';
            if($key == 'program')    $key = 'program-product';
            if($key == 'deploystep') $key = 'deploy-viewstep';

            echo "<li $class><a href='javascript:$.setSearchType(\"$key\");' data-value='{$key}'>{$value}</a></li>";
        }
        echo '</ul></div>';
    }

    /**
     * Print the module menu.
     *
     * @param  string $activeMenu
     * @static
     * @access public
     * @return void
     */
    public static function printModuleMenu($activeMenu)
    {
        global $app, $lang;
        $moduleName = $app->rawModule;
        $methodName = $app->rawMethod;

        $tab = $app->tab;

        if(!isset($lang->$tab->menu))
        {
            echo "<ul></ul>";
            return;
        }

        /* get current module and method. */
        $isTutorialMode = commonModel::isTutorialMode();
        $currentModule  = $app->getModuleName();
        $currentMethod  = $app->getMethodName();
        $isMobile       = $app->viewType === 'mhtml';

        /* When use workflow then set rawModule to moduleName. */
        if($moduleName == 'flow') $activeMenu = $app->rawModule;
        $menu = customModel::getModuleMenu($activeMenu);

        /* If this is not workflow then use rawModule and rawMethod to judge highlight. */
        if($app->isFlow)
        {
            $currentModule = $app->rawModule;
            $currentMethod = $app->rawMethod;
        }

        if($isTutorialMode and defined('WIZARD_MODULE')) $currentModule = WIZARD_MODULE;
        if($isTutorialMode and defined('WIZARD_METHOD')) $currentMethod = WIZARD_METHOD;

        /* The beginning of the menu. */
        echo $isMobile ? '' : "<ul class='nav nav-default'>\n";

        /* Cycling to print every sub menu. */
        foreach($menu as $menuItem)
        {
            if(isset($menuItem->hidden) and $menuItem->hidden) continue;
            if($isMobile and empty($menuItem->link)) continue;
            if($menuItem->divider) echo "<li class='divider'></li>";

            /* Init the these vars. */
            $alias     = isset($menuItem->alias) ? $menuItem->alias : '';
            $subModule = isset($menuItem->subModule) ? explode(',', $menuItem->subModule) : array();
            $class     = isset($menuItem->class) ? $menuItem->class : '';
            $active    = '';
            if($subModule and in_array($currentModule, $subModule)) $active = 'active';
            // if($alias and $moduleName == $currentModule and strpos(",$alias,", ",$currentMethod,") !== false) $active = 'active';
            if($menuItem->link)
            {
                $target = '';
                $module = '';
                $method = '';
                $link   = commonModel::createMenuLink($menuItem, $tab);
                if(is_array($menuItem->link))
                {
                    if(isset($menuItem->link['target'])) $target = $menuItem->link['target'];
                    if(isset($menuItem->link['module'])) $module = $menuItem->link['module'];
                    if(isset($menuItem->link['method'])) $method = $menuItem->link['method'];
                }
                if($module == $currentModule and ($method == $currentMethod or strpos(",$alias,", ",$currentMethod,") !== false)) $active = 'active';

                $label    = $menuItem->text;
                $dropMenu = '';

                /* Print sub menus. */
                if(isset($menuItem->dropMenu))
                {
                    foreach($menuItem->dropMenu as $dropMenuKey => $dropMenuItem)
                    {
                        if(isset($dropMenuItem->hidden) and $dropMenuItem->hidden) continue;

                        $subActive = '';
                        $subModule = '';
                        $subMethod = '';
                        $subParams = '';
                        $subLabel  = '';
                        list($dropMenuName, $dropMenuModule, $dropMenuMethod, $dropMenuParams) = explode('|', $dropMenuItem['link']);
                        if(isset($dropMenuModule)) $subModule = $dropMenuModule;
                        if(isset($dropMenuMethod)) $subMethod = $dropMenuMethod;
                        if(isset($dropMenuParams)) $subParams = $dropMenuParams;
                        if(isset($dropMenuName))   $subLabel  = $dropMenuName;

                        $subLink = helper::createLink($subModule, $subMethod, $subParams);

                        if($currentModule == strtolower($subModule) and $currentMethod == strtolower($subMethod)) $subActive = 'active';

                        $misc = (isset($lang->navGroup->$subModule) and $tab != $lang->navGroup->$subModule) ? "data-app='$tab'" : '';
                        $dropMenu .= "<li class='$subActive' data-id='$dropMenuKey'>" . html::a($subLink, $subLabel, '', $misc) . '</li>';
                    }

                    if(empty($dropMenu)) continue;

                    $label   .= "<span class='caret'></span>";
                    $dropMenu  = "<ul class='dropdown-menu'>{$dropMenu}</ul>";
                }

                $misc = (isset($lang->navGroup->$module) and $tab != $lang->navGroup->$module) ? "data-app='$tab'" : '';
                $menuItemHtml = "<li class='$class $active' data-id='$menuItem->name'>" . html::a($link, $label, $target, $misc) . $dropMenu . "</li>\n";

                if($isMobile) $menuItemHtml = html::a($link, $menuItem->text, $target, $misc . " class='$class $active'") . "\n";
                echo $menuItemHtml;
            }
            else
            {
                echo $isMobile ? $menuItem->text : "<li class='$class $active' data-id='$menuItem->name'>$menuItem->text</li>\n";
            }
        }
        echo $isMobile ? '' : "</ul>\n";
    }

    /**
     * Print the bread menu.
     *
     * @param  string $moduleName
     * @param  string $position
     * @static
     * @access public
     * @return void
     */
    public static function printBreadMenu($moduleName, $position)
    {
        global $lang;
        $mainMenu = $moduleName;
        echo "<ul class='breadcrumb'>";
        echo '<li>' . html::a(helper::createLink('my', 'index'), $lang->zentaoPMS) . '</li>';
        if($moduleName != 'index')
        {
            if(isset($lang->menu->$mainMenu))
            {
                $menuLink = $lang->menu->$mainMenu;
                if(is_array($menuLink)) $menuLink = $menuLink['link'];
                list($menuLabel, $module, $method) = explode('|', $menuLink);
                echo '<li>' . html::a(helper::createLink($module, $method), $menuLabel) . '</li>';
            }
        }
        else
        {
            echo '<li>' . $lang->index->common . '</li>';
        }

        if(empty($position))
        {
            echo '</ul>';
            return;
        }

        if(is_array($position))
        {
            foreach($position as $key => $link) echo "<li class='active'>" . $link . '</li>';
        }
        echo '</ul>';
    }

    /**
     * Print the link for notify file.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printNotifyLink()
    {
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        {
            global $lang;
            echo html::a(helper::createLink('misc', 'downNotify'), "<i class='icon-bell'></i>", '', "title='$lang->downNotify' class='text-primary'") . ' &nbsp; ';
        }
    }

    /**
     * Print the link for zentao client.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printClientLink()
    {
        global $config, $lang;
        if(isset($config->xxserver->installed) and $config->xuanxuan->turnon)
        {
            echo "<li class='dropdown-submenu'>";
            echo "<a href='javascript:;'>" . "<i class='icon icon-download'></i> " . $lang->clientName . "</a><ul class='dropdown-menu pull-left'>";
            echo '<li>' . html::a(helper::createLink('misc', 'downloadClient', '', '', true), $lang->downloadClient, '', "title='$lang->downloadClient' class='iframe text-ellipsis' data-width='600'") . '</li>';
            echo "<li class='dropdown-submenu' id='downloadMobile'><a href='javascript:;'>" . $lang->downloadMobile . "</a><ul class='dropdown-menu pull-left''>";

            /* Intranet users use local pictures. */
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://www.zentao.net/page/appqrcode.json');
            curl_setopt($curl, CURLOPT_TIMEOUT_MS, 200);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 200);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            $connected = curl_exec($curl);
            curl_close($curl);
            if($connected)
            {
                echo "<li><div class='mobile-qrcode'><iframe src='https://www.zentao.net/page/appqrcode.html?v={$config->version}' frameborder='0' scrolling='no' seamless></iframe></div></li>";
            }
            else
            {
                echo "<li><div class='mobile-qrcode local-img'><img src='{$config->webRoot}theme/default/images/main/mobile_qrcode.png' /><div class='mobile-version'><span>v1.2</span></div></li>";
            }

            echo "</ul></li>";
            echo '<li>' . html::a($lang->clientHelpLink, $lang->clientHelp, '', "title='$lang->clientHelp' target='_blank'") . '</li>';
            echo '</ul></li>';
        }
    }

    /**
     * Print QR code Link.
     *
     * @param string $color
     *
     * @static
     * @access public
     * @return void
     */
    public static function printQRCodeLink($color = '')
    {
        global $lang;
        echo html::a('javascript:;', "<i class='icon-qrcode'></i>", '', "class='qrCode $color' id='qrcodeBtn' title='{$lang->user->mobileLogin}'");
        echo "<div class='popover top' id='qrcodePopover'><div class='arrow'></div><h3 class='popover-title'>{$lang->user->mobileLogin}</h3><div class='popover-content'><img src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'></div></div>";
        echo '<script>$(function(){$("#qrcodeBtn").click(function(){$("#qrcodePopover").toggleClass("show");}); $("#wrap").click(function(){$("#qrcodePopover").removeClass("show");});});</script>';
        echo '<script>$(function(){$("#qrcodeBtn").hover(function(){$(".popover-content img").attr("src", "' . helper::createLink('misc', 'qrCode') . '");});});</script>';
    }

    /**
     * Print the link contains orderBy field.
     *
     * This method will auto set the orderby param according the params. Fox example, if the order by is desc,
     * will be changed to asc.
     *
     * @param  string $fieldName    the field name to sort by
     * @param  string $orderBy      the order by string
     * @param  string $vars         the vars to be passed
     * @param  string $label        the label of the link
     * @param  string $module       the module name
     * @param  string $method       the method name
     *
     * @access public
     * @return void
     */
    public static function printOrderLink($fieldName, $orderBy, $vars, $label, $module = '', $method = '')
    {
        global $lang, $app;
        if(empty($module)) $module = isset($app->rawModule) ? $app->rawModule : $app->getModuleName();
        if(empty($method)) $method = isset($app->rawMethod) ? $app->rawMethod : $app->getMethodName();
        $className = 'header';
        $isMobile  = $app->viewType === 'mhtml';

        $order = explode('_', $orderBy);
        $order[0] = trim($order[0], '`');
        if($order[0] == $fieldName)
        {
            if(isset($order[1]) and $order[1] == 'asc')
            {
                $orderBy   = "{$order[0]}_desc";
                $className = $isMobile ? 'SortUp' : 'sort-up';
            }
            else
            {
                $orderBy = "{$order[0]}_asc";
                $className = $isMobile ? 'SortDown' : 'sort-down';
            }
        }
        else
        {
            $orderBy   = "" . trim($fieldName, '`') . "" . '_' . 'asc';
            $className = 'header';
        }

        $params = sprintf($vars, $orderBy);
        if($app->getModuleName() == 'my' and $app->rawMethod == 'work') $params = "mode={$app->getMethodName()}&" . $params;

        $link = helper::createLink($module, $method, $params);
        echo $isMobile ? html::a($link, $label, '', "class='$className' data-app={$app->tab}") : html::a($link, $label, '', "class='$className' data-app={$app->tab}");
    }

    /**
     *
     * Print link to an modules' methd.
     *
     * Before printing, check the privilege first. If no privilege, return fasle. Else, print the link, return true.
     *
     * @param string $module    the module name
     * @param string $method    the method
     * @param string $vars      vars to be passed
     * @param string $label     the label of the link
     * @param string $target    the target of the link
     * @param string $misc      others
     * @param bool   $newline
     * @param bool   $onlyBody
     * @param        $object
     *
     * @static
     * @access public
     * @return bool
     */
    public static function printLink($module, $method, $vars = '', $label = '', $target = '', $misc = '', $newline = true, $onlyBody = false, $object = null)
    {
        /* Add data-app attribute. */
        global $app, $config;
        $currentModule = strtolower($module);
        $currentMethod = strtolower($method);
        if(strpos($misc, 'data-app') === false) $misc .= ' data-app="' . $app->tab . '"';

        if(!commonModel::hasPriv($module, $method, $object, $vars) and !in_array("$currentModule.$currentMethod", $config->openMethods)) return false;
        echo html::a(helper::createLink($module, $method, $vars, '', $onlyBody), $label, $target, $misc, $newline);
        return true;
    }

    /**
     * Print icon of split line.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printDivider()
    {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    /**
     * Print icon of comment.
     *
     * @param string $commentFormLink
     * @param object $object
     *
     * @static
     * @access public
     * @return mixed
     */
    public static function printCommentIcon($commentFormLink, $object = null)
    {
        global $lang;

        if(!commonModel::hasPriv('action', 'comment', $object)) return false;
        echo html::commonButton('<i class="icon icon-chat-line"></i> ' . $lang->action->create, '', 'btn btn-link pull-right btn-comment');
        echo <<<EOD
<div class="modal fade modal-comment">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="icon icon-close"></i></button>
        <h4 class="modal-title">{$lang->action->create}</h4>
      </div>
      <div class="modal-body">
        <form class="load-indicator not-watch" action="{$commentFormLink}" target='hiddenwin' method='post'>
          <div class="form-group">
            <textarea id='comment' name='comment' class="form-control" rows="8" autofocus="autofocus"></textarea>
          </div>
          <div class="form-group form-actions text-center">
            <button type="submit" class="btn btn-primary btn-wide">{$lang->save}</button>
            <button type="button" class="btn btn-wide" data-dismiss="modal">{$lang->close}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
$(function()
{
    \$body = $('body', window.parent.document);
    if(\$body.hasClass('hide-modal-close')) \$body.removeClass('hide-modal-close');
});
</script>
EOD;
    }

    /**
     * Build icon button.
     *
     * @param  string $module
     * @param  string $method
     * @param  string $vars
     * @param  object $object
     * @param  string $type button|list
     * @param  string $icon
     * @param  string $target
     * @param  string $extraClass
     * @param  bool   $onlyBody
     * @param  string $misc
     * @param  bool   $extraEnabled
     * @static
     * @access public
     * @return void
     */
    public static function buildIconButton($module, $method, $vars = '', $object = '', $type = 'button', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = '', $title = '', $programID = 0, $extraEnabled = '')
    {
        if(isonlybody() and strpos($extraClass, 'showinonlybody') === false) return false;

        /* Remove iframe for operation button in modal. Prevent pop up in modal. */
        if(isonlybody() and strpos($extraClass, 'showinonlybody') !== false) $extraClass = str_replace('iframe', '', $extraClass);

        global $app, $lang, $config;

        /* Add data-app attribute. */
        if(strpos($misc, 'data-app') === false) $misc .= ' data-app="' . $app->tab . '"';

        /* Judge the $method of $module clickable or not, default is clickable. */
        $clickable = true;
        if(is_bool($extraEnabled))
        {
            $clickable = $extraEnabled;
        }
        else if(is_object($object))
        {
            if($app->getModuleName() != $module) $app->control->loadModel($module);
            $modelClass = class_exists("ext{$module}Model") ? "ext{$module}Model" : $module . "Model";
            if(class_exists($modelClass) and is_callable(array($modelClass, 'isClickable')))
            {
                //$clickable = call_user_func_array(array($modelClass, 'isClickable'), array('object' => $object, 'method' => $method));
                // fix bug on php  8.0 link: https://www.php.net/manual/zh/function.call-user-func-array.php#125953
                $clickable = call_user_func_array(array($modelClass, 'isClickable'), array($object, $method));
            }
        }

        /* Set module and method, then create link to it. */
        if(strtolower($module) == 'story'    and strtolower($method) == 'createcase') ($module = 'testcase') and ($method = 'create');
        if(strtolower($module) == 'bug'      and strtolower($method) == 'tostory')    ($module = 'story') and ($method = 'create');
        if(strtolower($module) == 'bug'      and strtolower($method) == 'createcase') ($module = 'testcase') and ($method = 'create');
        if(!commonModel::hasPriv($module, $method, $object, $vars)) return false;

        $link = helper::createLink($module, $method, $vars, '', $onlyBody, $programID);

        /* Set the icon title, try search the $method defination in $module's lang or $common's lang. */
        if(empty($title))
        {
            $title = $method;
            if($method == 'create' and $icon == 'copy') $method = 'copy';
            if(isset($lang->$method) and is_string($lang->$method)) $title = $lang->$method;
            if((isset($lang->$module->$method) or $app->loadLang($module)) and isset($lang->$module->$method))
            {
                $title = $method == 'report' ? $lang->$module->$method->common : $lang->$module->$method;
            }
            if($icon == 'toStory')   $title  = $lang->bug->toStory;
            if($icon == 'createBug') $title  = $lang->testtask->createBug;
        }

        /* set the class. */
        if(!$icon)
        {
            $icon = isset($lang->icons[$method]) ? $lang->icons[$method] : $method;
        }
        if(strpos(',edit,copy,report,export,delete,', ",$method,") !== false) $module = 'common';
        $class = "icon-$module-$method";

        if(!$clickable) $class .= ' disabled';
        if($icon)       $class .= ' icon-' . $icon;

        /* Create the icon link. */
        if($clickable)
        {
            if($app->getViewType() == 'mhtml')
            {
                return "<a data-remote='$link' class='$extraClass' $misc>$title</a>";
            }
            if($type == 'button')
            {
                if($method != 'edit' and $method != 'copy' and $method != 'delete')
                {
                    return html::a($link, "<i class='$class'></i> " . "<span class='text'>{$title}</span>", $target, "class='btn btn-link $extraClass' $misc", true);
                }
                else
                {
                    return html::a($link, "<i class='$class'></i>", $target, "class='btn btn-link $extraClass' title=\"$title\" $misc", false);
                }
            }
            else
            {
                return html::a($link, "<i class='$class'></i>", $target, "class='btn $extraClass' title=\"$title\" $misc", false) . "\n";
            }
        }
        else
        {
            if($type == 'list')
            {
                return "<button type='button' class='disabled btn $extraClass'><i class='$class' title=\"$title\" $misc></i></button>\n";
            }
        }
    }

    /**
     * Build more executions button.
     *
     * @param  int    $executionID
     * @static
     * @access public
     * @return void
     */
    public static function buildMoreButton($executionID)
    {
        if(defined('TUTORIAL')) return;

        global $lang, $app;

        $object = $app->dbh->query('SELECT project,type FROM ' . TABLE_EXECUTION . " WHERE `id` = '$executionID'")->fetch();
        if(empty($object)) return;

        $executionPairs = array();
        $userCondition  = !$app->user->admin ? " AND `id` " . helper::dbIN($app->user->view->sprints) : '';
        $orderBy        = $object->type == 'stage' ? 'ORDER BY `id` ASC' : 'ORDER BY `id` DESC';
        $executionList  = $app->dbh->query("SELECT id,name,parent FROM " . TABLE_EXECUTION . " WHERE `project` = '{$object->project}' AND `deleted` = '0' $userCondition $orderBy")->fetchAll();
        foreach($executionList as $execution)
        {
            if(isset($executionPairs[$execution->parent])) unset($executionPairs[$execution->parent]);
            if($execution->id == $executionID) continue;
            $executionPairs[$execution->id] = $execution->name;
        }

        if(empty($executionPairs)) return;

        $html  = "<li class='divider'></li><li class='dropdown dropdown-hover'><a href='javascript:;' data-toggle='dropdown'>{$lang->more}<span class='caret'></span></a>";
        $html .= "<ul class='dropdown-menu'>";

        $showCount = 0;
        foreach($executionPairs as $executionID => $executionName)
        {
            $html .= "<li style='max-width: 300px;'>" . html::a(helper::createLink('execution', 'task', "executionID=$executionID"), $executionName, '', "title='{$executionName}' class='text-ellipsis' style='padding: 2px 10px'") . '</li>';

            $showCount ++;
            if($showCount == 10) break;
        }

        if(count($executionPairs) > 10) $html .= '<li>' . html::a(helper::createLink('project', 'execution', "status=all&projectID={$object->project}"), $lang->preview . $lang->more, '', "data-app='project' style='padding: 2px 10px'") . '</li>';

        $html .= "</ul></li>\n";

        echo $html;
    }

    /**
     * Build devops app button.
     *
     * @static
     * @access public
     * @return void
     */
    public static function buildAppButton()
    {
        if(defined('TUTORIAL')) return;
        global $app, $config, $lang;

        $pipelinePairs = array();
        $condition     = '';
        if(!$app->user->admin)
        {
            $types = '';
            foreach($config->pipelineTypeList as $pipelineType)
            {
                if(commonModel::hasPriv($pipelineType, 'browse')) $types .= "'$pipelineType',";
            }
            if(empty($types)) return;
            $condition .= ' AND `type` in (' . trim($types, ',') . ')';
        }
        $pipelineList = $app->dbh->query("SELECT type,name,url FROM " . TABLE_PIPELINE . " WHERE `deleted` = '0' $condition order by type")->fetchAll();
        if(empty($pipelineList)) return;

        $html  = "<li class='dropdown dropdown-hover'><a href='javascript:;' data-toggle='dropdown'>{$lang->app->common}<span class='caret'></span></a>";
        $html .= "<ul class='dropdown-menu'>";

        foreach($pipelineList as $pipeline)
        {
            $html .= "<li style='max-width: 300px;'>" . html::a($pipeline->url, "[{$pipeline->type}] {$pipeline->name}", '', "title='{$pipeline->name}' class='text-ellipsis' style='padding: 2px 10px' target='_blank'") . '</li>';
        }
        $html .= "</ul></li>\n";

        echo $html;
    }

    /**
     * Print link icon.
     *
     * @param  string $module
     * @param  string $method
     * @param  string $vars
     * @param  object $object
     * @param  string $type button|list
     * @param  string $icon
     * @param  string $target
     * @param  string $extraClass
     * @param  bool   $onlyBody
     * @param  string $misc
     * @param  string $extraEnabled
     * @static
     * @access public
     * @return void
     */
    public static function printIcon($module, $method, $vars = '', $object = '', $type = 'button', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = '', $title = '', $programID = 0, $extraEnabled = '')
    {
        echo common::buildIconButton($module, $method, $vars, $object, $type, $icon, $target, $extraClass, $onlyBody, $misc, $title, $programID, $extraEnabled);
    }

    /**
     * Print backLink and preLink and nextLink.
     *
     * @param string $backLink
     * @param object $preAndNext
     * @param string $linkTemplate
     *
     * @static
     * @access public
     * @return void
     */
    static public function printRPN($backLink, $preAndNext = '', $linkTemplate = '')
    {
        global $lang, $app;
        if(isonlybody()) return false;

        $title = $lang->goback . $lang->backShortcutKey;
        echo html::a($backLink, '<i class="icon-goback icon-back icon-large"></i>', '', "id='back' class='btn' title={$title}");

        if(isset($preAndNext->pre) and $preAndNext->pre)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->pre->case)) ? 'case' : 'id';
            $title = isset($preAndNext->pre->title) ? $preAndNext->pre->title : $preAndNext->pre->name;
            $title = '#' . $preAndNext->pre->$id . ' ' . $title . ' ' . $lang->preShortcutKey;
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->pre->$id) : inLink('view', "ID={$preAndNext->pre->$id}");
            echo html::a($link, '<i class="icon-pre icon-chevron-left"></i>', '', "id='pre' class='btn' title='{$title}'");
        }
        if(isset($preAndNext->next) and $preAndNext->next)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->next->case)) ? 'case' : 'id';
            $title = isset($preAndNext->next->title) ? $preAndNext->next->title : $preAndNext->next->name;
            $title = '#' . $preAndNext->next->$id . ' ' . $title . ' ' . $lang->nextShortcutKey;
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->next->$id) : inLink('view', "ID={$preAndNext->next->$id}");
            echo html::a($link, '<i class="icon-pre icon-chevron-right"></i>', '', "id='next' class='btn' title='$title'");
        }
    }

    /**
     * Print back link
     *
     * @param  string $backLink
     * @param  string $class
     * @param  string $misc
     * @static
     * @access public
     * @return void
     */
    static public function printBack($backLink, $class = '', $misc = '')
    {
        global $lang, $app;
        if(isonlybody()) return false;

        if(empty($class)) $class = 'btn';
        $title = $lang->goback . $lang->backShortcutKey;
        echo html::a($backLink, '<i class="icon-goback icon-back"></i> ' . $lang->goback, '', "id='back' class='{$class}' title={$title} $misc");
    }

    /**
     * Print pre and next link
     *
     * @param  string $preAndNext
     * @param  string $linkTemplate
     * @static
     * @access public
     * @return void
     */
    public static function printPreAndNext($preAndNext = '', $linkTemplate = '')
    {
        global $lang, $app;
        if(isonlybody()) return false;

        $moduleName = ($app->getModuleName() == 'story' and $app->tab == 'project') ? 'projectstory' : $app->getModuleName();
        echo "<nav class='container'>";
        if(isset($preAndNext->pre) and $preAndNext->pre)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->pre->case)) ? 'case' : 'id';
            $title = isset($preAndNext->pre->title) ? $preAndNext->pre->title : $preAndNext->pre->name;
            $title = '#' . $preAndNext->pre->$id . ' ' . $title . ' ' . $lang->preShortcutKey;

            $params = $moduleName == 'story' ? "&version=0&param=0&storyType={$preAndNext->pre->type}" : '';
            $link   = $linkTemplate ? sprintf($linkTemplate, $preAndNext->pre->$id) : helper::createLink($moduleName, 'view', "ID={$preAndNext->pre->$id}" . $params);
            $link  .= '#app=' . $app->tab;
            if(isset($preAndNext->pre->objectType) and $preAndNext->pre->objectType == 'doc')
            {
                echo html::a('javascript:void(0)', '<i class="icon-pre icon-chevron-left"></i>', '', "id='prevPage' class='btn' title='{$title}' data-url='{$link}'");
            }
            else
            {
                echo html::a($link, '<i class="icon-pre icon-chevron-left"></i>', '', "id='prevPage' class='btn' title='{$title}'");
            }
        }
        if(isset($preAndNext->next) and $preAndNext->next)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->next->case)) ? 'case' : 'id';
            $title = isset($preAndNext->next->title) ? $preAndNext->next->title : $preAndNext->next->name;
            $title = '#' . $preAndNext->next->$id . ' ' . $title . ' ' . $lang->nextShortcutKey;
            $params = $moduleName == 'story' ? "&version=0&param=0&storyType={$preAndNext->next->type}" : '';
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->next->$id) : helper::createLink($moduleName, 'view', "ID={$preAndNext->next->$id}" . $params);
            $link .= '#app=' . $app->tab;
            if(isset($preAndNext->next->objectType) and $preAndNext->next->objectType == 'doc')
            {
                echo html::a('javascript:void(0)', '<i class="icon-pre icon-chevron-right"></i>', '', "id='nextPage' class='btn' title='$title' data-url='{$link}'");
            }
            else
            {
                echo html::a($link, '<i class="icon-pre icon-chevron-right"></i>', '', "id='nextPage' class='btn' title='$title'");
            }
        }
        echo '</nav>';
    }

    /**
     * Create changes of one object.
     *
     * @param mixed  $old        the old object
     * @param mixed  $new        the new object
     * @param string $moduleName
     * @static
     * @access public
     * @return array
     */
    public static function createChanges($old, $new, $moduleName = '')
    {
        global $app, $config;

        /**
         * 当主状态改变并且未设置子状态的值时把子状态的值设置为默认值并记录日志。
         * Change sub status when status is changed and sub status is not set, and record the changes.
         */
        if($config->edition != 'open')
        {
            $oldID        = zget($old, 'id', '');
            $oldStatus    = zget($old, 'status', '');
            $newStatus    = zget($new, 'status', '');
            $newSubStatus = zget($new, 'subStatus', '');
            if(empty($moduleName)) $moduleName = $app->getModuleName();

            if($oldID and $oldStatus and $newStatus and !$newSubStatus and $oldStatus != $newStatus)
            {
                $field = $app->dbh->query('SELECT options FROM ' . TABLE_WORKFLOWFIELD . " WHERE `module` = '$moduleName' AND `field` = 'subStatus'")->fetch();
                if(!empty($field->options)) $field->options = json_decode($field->options, true);

                if(!empty($field->options[$newStatus]['default']))
                {
                    $flow    = $app->dbh->query('SELECT `table` FROM ' . TABLE_WORKFLOW . " WHERE `module`='$moduleName'")->fetch();
                    $default = $field->options[$newStatus]['default'];

                    $app->dbh->exec("UPDATE `$flow->table` SET `subStatus` = '$default' WHERE `id` = '$oldID'");

                    $new->subStatus = $default;
                }
            }

            $dateFields = array();
            $sql        = "SELECT `field` FROM " . TABLE_WORKFLOWFIELD . " WHERE `module` = '{$moduleName}' and `control` in ('date', 'datetime')";
            $stmt       = $app->dbh->query($sql);
            while($row = $stmt->fetch()) $dateFields[$row->field] = $row->field;
        }

        $changes = array();
        foreach($new as $key => $value)
        {
            if(is_object($value) or is_array($value)) continue;
            if(strtolower($key) == 'lastediteddate')  continue;
            if(strtolower($key) == 'lasteditedby')    continue;
            if(strtolower($key) == 'assigneddate')    continue;
            if(strtolower($key) == 'editedby')        continue;
            if(strtolower($key) == 'editeddate')      continue;
            if(strtolower($key) == 'uid')             continue;
            if(strtolower($key) == 'finisheddate'     and $value == '') continue;
            if(strtolower($key) == 'canceleddate'     and $value == '') continue;
            if(strtolower($key) == 'hangupeddate'     and $value == '') continue;
            if(strtolower($key) == 'lastcheckeddate'  and $value == '') continue;
            if(strtolower($key) == 'activateddate'    and $value == '') continue;
            if(strtolower($key) == 'closeddate'       and $value == '') continue;
            if(strtolower($key) == 'actualcloseddate' and $value == '') continue;

            if(isset($old->$key))
            {
                if($config->edition != 'open' && isset($dateFields[$key])) $old->$key = formatTime($old->$key);

                if($value != stripslashes($old->$key))
                {
                    $diff = '';
                    if(substr_count($value, "\n") > 1     or
                        substr_count($old->$key, "\n") > 1 or
                        strpos('name,title,desc,spec,steps,content,digest,verify,report,definition,analysis,summary,prevention,resolution,outline,schedule,minutes', strtolower($key)) !== false)
                    {
                        $diff = commonModel::diff($old->$key, $value);
                    }
                    $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
                }
            }
        }
        return $changes;
    }

    /**
     * Diff two string. (see phpt)
     *
     * @param string $text1
     * @param string $text2
     * @static
     * @access public
     * @return string
     */
    public static function diff($text1, $text2)
    {
        $text1 = str_replace('&nbsp;', '', trim($text1));
        $text2 = str_replace('&nbsp;', '', trim($text2));
        $w  = explode("\n", $text1);
        $o  = explode("\n", $text2);
        $w1 = array_diff_assoc($w,$o);
        $o1 = array_diff_assoc($o,$w);
        $w2 = array();
        $o2 = array();
        foreach($w1 as $idx => $val) $w2[sprintf("%03d<",$idx)] = sprintf("%03d- ", $idx+1) . "<del>" . trim($val) . "</del>";
        foreach($o1 as $idx => $val) $o2[sprintf("%03d>",$idx)] = sprintf("%03d+ ", $idx+1) . "<ins>" . trim($val) . "</ins>";
        $diff = array_merge($w2, $o2);
        ksort($diff);
        return implode("\n", $diff);
    }

    /**
     * Judge Suhosin Setting whether the actual size of post data is large than the setting size.
     *
     * @param  int    $countInputVars
     * @static
     * @access public
     * @return bool
     */
    public static function judgeSuhosinSetting($countInputVars)
    {
        if(extension_loaded('suhosin'))
        {
            $maxPostVars    = ini_get('suhosin.post.max_vars');
            $maxRequestVars = ini_get('suhosin.request.max_vars');
            if($countInputVars > $maxPostVars or $countInputVars > $maxRequestVars) return true;
        }
        else
        {
            $maxInputVars = ini_get('max_input_vars');
            if($maxInputVars and $countInputVars > (int)$maxInputVars) return true;
        }

        return false;
    }

    /**
     * Get the previous and next object.
     *
     * @param  string $type story|task|bug|case
     * @param  string $objectID
     * @access public
     * @return void
     */
    public function getPreAndNextObject($type, $objectID)
    {
        /* Get SQL. */
        $queryCondition    = $type . 'QueryCondition';
        $typeOnlyCondition = $type . 'OnlyCondition';
        $queryCondition    = $this->session->$queryCondition;

        $preAndNextObject       = new stdClass();
        $preAndNextObject->pre  = '';
        $preAndNextObject->next = '';
        if(empty($queryCondition)) return $preAndNextObject;

        $table   = $this->config->objectTables[$type];
        $orderBy = $type . 'OrderBy';
        $orderBy = $this->session->$orderBy;
        $select  = '';
        if($this->session->$typeOnlyCondition)
        {
            if(strpos($orderBy, 'priOrder') !== false) $select .= ", IF(`pri` = 0, {$this->config->maxPriValue}, `pri`) as priOrder";
            if(strpos($orderBy, 'severityOrder') !== false) $select .= ", IF(`severity` = 0, {$this->config->maxPriValue}, `severity`) as severityOrder";
            $queryCondition = str_replace('t4.status', 'status', $queryCondition);

            $sql = $this->dao->select("*$select")->from($table)
                ->where($queryCondition)
                ->beginIF($orderBy != false)->orderBy($orderBy)->fi()
                ->get();
        }
        else
        {
            $sql = $queryCondition . (empty($orderBy) ? '' : " ORDER BY $orderBy");
        }

        /* Get objectIDList. */
        $objectIdListKey  = $type . 'BrowseList';
        $existsObjectList = $this->session->$objectIdListKey;
        if(empty($existsObjectList) or $existsObjectList['sql'] != $sql)
        {
            $queryObjects = $this->dao->query($sql);
            $objectList   = array();
            $key          = 'id';
            while($object = $queryObjects->fetch())
            {
                if(!$this->session->$typeOnlyCondition and $type == 'testcase' and isset($object->case)) $key = 'case';
                $id  = $object->$key;
                $objectList[$id] = $id;
            }

            $this->session->set($objectIdListKey, array('sql' => $sql, 'idkey' => $key, 'objectList' => $objectList), $this->app->tab);
            $existsObjectList = $this->session->$objectIdListKey;
        }

        $preObj = false;
        if(isset($existsObjectList['objectList']))
        {
            foreach($existsObjectList['objectList'] as $id)
            {
                /* Get next object. */
                if($preObj === true)
                {
                    $preAndNextObject->next = $id;
                    break;
                }

                /* Get pre object. */
                if($id == $objectID)
                {
                    if($preObj) $preAndNextObject->pre = $preObj;
                    $preObj = true;
                }
                if($preObj !== true) $preObj = $id;
            }

            if(empty($queryCondition) or $this->session->$typeOnlyCondition)
            {
                if(!empty($preAndNextObject->pre))  $preAndNextObject->pre  = $this->dao->select('*')->from($table)->where('id')->eq($preAndNextObject->pre)->fetch();
                if(!empty($preAndNextObject->next)) $preAndNextObject->next = $this->dao->select('*')->from($table)->where('id')->eq($preAndNextObject->next)->fetch();
            }
            else
            {
                $isObject     = false;
                $objects      = array();
                $key          = $existsObjectList['idkey'];
                $queryObjects = $this->dao->query($existsObjectList['sql']);
                while($object = $queryObjects->fetch())
                {
                    $objects[$object->$key] = $object;
                    if(!empty($preAndNextObject->pre)  and is_numeric($preAndNextObject->pre)  and $object->$key == $preAndNextObject->pre)  $preAndNextObject->pre  = $object;
                    if(!empty($preAndNextObject->next) and is_numeric($preAndNextObject->next) and $object->$key == $preAndNextObject->next) $preAndNextObject->next = $object;
                    if((empty($preAndNextObject->pre) or is_object($preAndNextObject->pre)) and (empty($preAndNextObject->next) or is_object($preAndNextObject->next)))
                    {
                        $isObject = true;
                        break;
                    }
                }

                /* If the pre object or next object is number type, then continue to find the pre or next. */
                if(!$isObject)
                {
                    $objectIdList  = array_keys($objects);
                    $objectIdIndex = array_search($objectID, $objectIdList);
                    if(is_numeric($preAndNextObject->pre))
                    {
                        $preAndNextObject->pre = $objectIdIndex - 1 >= 0 ? $objects[$objectIdList[$objectIdIndex - 1]] : '';
                    }
                    if(is_numeric($preAndNextObject->next))
                    {
                        $preAndNextObject->next = $objectIdIndex + 1 < count($objectIdList) ? $objects[$objectIdList[$objectIdIndex + 1]] : '';
                    }
                }
            }
        }

        return $preAndNextObject;
    }

    /**
     * Save one executed query.
     *
     * @param  string    $sql
     * @param  string    $objectType story|task|bug|testcase
     * @access public
     * @return void
     */
    public function saveQueryCondition($sql, $objectType, $onlyCondition = true)
    {
        /* Set the query condition session. */
        if($onlyCondition)
        {
            $queryCondition = explode(' WHERE ', $sql);
            $queryCondition = isset($queryCondition[1]) ? $queryCondition[1] : '';
            if($queryCondition)
            {
                $queryCondition = explode(' ORDER BY ', $queryCondition);
                $queryCondition = str_replace('t1.', '', $queryCondition[0]);
            }
        }
        else
        {
            $queryCondition = explode(' ORDER BY ', $sql);
            $queryCondition = $queryCondition[0];
        }
        $queryCondition = trim($queryCondition);
        if(empty($queryCondition)) $queryCondition = "1=1";

        $this->session->set($objectType . 'QueryCondition', $queryCondition, $this->app->tab);
        $this->session->set($objectType . 'OnlyCondition', $onlyCondition, $this->app->tab);

        /* Set the query condition session. */
        $orderBy = explode(' ORDER BY ', $sql);
        $orderBy = isset($orderBy[1]) ? $orderBy[1] : '';
        if($orderBy)
        {
            $orderBy = explode(' LIMIT ', $orderBy);
            $orderBy = $orderBy[0];
            if($onlyCondition) $orderBy = str_replace('t1.', '', $orderBy);
        }
        $this->session->set($objectType . 'OrderBy', $orderBy, $this->app->tab);
        $this->session->set($objectType . 'BrowseList', array(), $this->app->tab);
    }

    /**
     * Remove duplicate for story, task, bug, case, doc.
     *
     * @param  string       $type  e.g. story task bug case doc.
     * @param  array|object $data
     * @param  string       $condition
     * @access public
     * @return array
     */
    public function removeDuplicate($type, $data = '', $condition = '')
    {
        $table      = $this->config->objectTables[$type];
        $titleField = $type == 'task' ? 'name' : 'title';
        $date       = date(DT_DATETIME1, time() - $this->config->duplicateTime);
        $dateField  = $type == 'doc' ? 'addedDate' : 'openedDate';
        $titles     = $data->$titleField;

        if(empty($titles)) return false;
        $duplicate = $this->dao->select("id,$titleField")->from($table)
            ->where('deleted')->eq(0)
            ->andWhere($titleField)->in($titles)
            ->andWhere($dateField)->ge($date)->fi()
            ->beginIF($condition)->andWhere($condition)->fi()
            ->fetchPairs();

        if($duplicate and is_string($titles)) return array('stop' => true, 'duplicate' => key($duplicate));
        if($duplicate and is_array($titles))
        {
            foreach($titles as $i => $title)
            {
                if(in_array($title, $duplicate)) unset($titles[$i]);
            }
            $data->$titleField = $titles;
        }
        return array('stop' => false, 'data' => $data);
    }

    /**
     * Append order by.
     *
     * @param  string $orderBy
     * @param  string $append
     * @access public
     * @return string
     */
    public static function appendOrder($orderBy, $append = 'id')
    {
        if(empty($orderBy)) return $append;

        list($firstOrder) = explode(',', $orderBy);
        $sort = strpos($firstOrder, '_') === false ? '_asc' : strstr($firstOrder, '_');
        return strpos($orderBy, $append) === false ? $orderBy . ',' . $append . $sort : $orderBy;
    }

    /**
     * Check field exists
     *
     * @param  string    $table
     * @param  string    $field
     * @access public
     * @return bool
     */
    public function checkField($table, $field)
    {
        $fields   = $this->dao->query("DESC $table")->fetchAll();
        $hasField = false;
        foreach($fields as $fieldObj)
        {
            if($field == $fieldObj->Field)
            {
                $hasField = true;
                break;
            }
        }
        return $hasField;
    }

    /**
     * Check safe file.
     *
     * @access public
     * @return string|false
     */
    public function checkSafeFile()
    {
        if($this->app->getModuleName() == 'upgrade' and $this->session->upgrading) return false;

        $statusFile = $this->app->getAppRoot() . 'www' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'ok.txt';
        return (!is_file($statusFile) or (time() - filemtime($statusFile)) > 3600) ? $statusFile : false;
    }

    /**
     * Check upgrade's status file is ok or not.
     *
     * @access public
     * @return bool
     */
    public function checkUpgradeStatus()
    {
        $statusFile = $this->checkSafeFile();
        if($statusFile)
        {
            $this->app->loadLang('upgrade');
            $cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? $this->lang->upgrade->createFileWinCMD : $this->lang->upgrade->createFileLinuxCMD;
            $cmd = sprintf($cmd, $statusFile);

            echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body>";
            echo "<table align='center' style='margin-top:100px; border:1px solid gray; font-size:14px;padding:8px;'><tr><td>";
            printf($this->lang->upgrade->setStatusFile, $cmd, $statusFile);
            echo '</td></tr></table></body></html>';

            return false;
        }

        return true;
    }

    /**
     * Check the user has permission to access this method, if not, locate to the login page or deny page.
     *
     * @access public
     * @return void
     */
    public function checkPriv()
    {
        try
        {
            $module = $this->app->getModuleName();
            $method = $this->app->getMethodName();
            if($this->app->isFlow)
            {
                $module = $this->app->rawModule;
                $method = $this->app->rawMethod;
            }

            $beforeValidMethods = array(
                'user'    => array('deny', 'logout'),
                'my'      => array('changepassword'),
                'message' => array('ajaxgetmessage'),
            );
            if(!empty($this->app->user->modifyPassword) and (!isset($beforeValidMethods[$module]) or !in_array($method, $beforeValidMethods[$module]))) return print(js::locate(helper::createLink('my', 'changepassword')));
            if($this->isOpenMethod($module, $method)) return true;
            if(!$this->loadModel('user')->isLogon() and $this->server->php_auth_user) $this->user->identifyByPhpAuth();
            if(!$this->loadModel('user')->isLogon() and $this->cookie->za) $this->user->identifyByCookie();

            if(isset($this->app->user))
            {
                $this->app->user = $this->session->user;
                if(!commonModel::hasPriv($module, $method))
                {
                    if($module == 'story' and !empty($this->app->params['storyType']) and strpos(",story,requirement,", ",{$this->app->params['storyType']},") !== false) $module = $this->app->params['storyType'];
                    $this->deny($module, $method);
                }
            }
            else
            {
                $uri = $this->app->getURI(true);
                if($module == 'message' and $method == 'ajaxgetmessage')
                {
                    $uri = helper::createLink('my');
                }
                elseif(helper::isAjaxRequest())
                {
                    die(json_encode(array('result' => false, 'message' => $this->lang->error->loginTimeout))); // Fix bug #14478.
                }

                $referer = helper::safe64Encode($uri);
                die(js::locate(helper::createLink('user', 'login', "referer=$referer")));
            }
        }
        catch(EndResponseException $endResponseException)
        {
            echo $endResponseException->getContent();
        }
    }

    /**
     * Check current page whether is in iframe. If it is not iframe and not allowed to open independently, then redirect to index to open it in iframe
     *
     * @access public
     * @return void
     */
    public function checkIframe()
    {
        if($this->app->getViewType() != 'html' or helper::isAjaxRequest() or isset($_GET['_single'])) return;

        if(isset($_SERVER['HTTP_SEC_FETCH_DEST']) and $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe')
        {
            return;
        }
        elseif((isset($_SERVER['HTTP_REFERER']) and !empty($_SERVER['HTTP_REFERER'])) or strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
        {
            return;
        }

        $module = $this->app->getModuleName();
        $method = $this->app->getMethodName();
        if($module == 'index' or
           $module == 'tutorial' or
           $module == 'install' or
           $module == 'upgrade' or
           $module == 'sso' or
          ($module == 'user' and strpos('|login|deny|logout|reset|forgetpassword|resetpassword|', "|{$method}|") !== false) or
          ($module == 'my' and strpos('|changepassword|preference|', "|{$method}|") !== false) or
          ($module == 'file' and strpos('|read|download|uploadimages|ajaxwopifiles|', "|{$method}|") !== false) or
          ($module == 'report' && $method == 'annualdata') or
          ($module == 'misc' && $method == 'captcha') or
          ($module == 'execution' and $method == 'printkanban') or
          ($module == 'traincourse' and $method == 'ajaxuploadlargefile') or
          ($module == 'traincourse' and $method == 'playvideo'))
        {
            return;
        }

        $url = helper::safe64Encode($_SERVER['REQUEST_URI']);
        $redirectUrl  = helper::createLink('index', 'index');
        $redirectUrl .= strpos($redirectUrl, '?') === false ? "?open=$url" : "&open=$url";
        die(header("location: $redirectUrl"));
    }

    /**
     * Check the user has permisson of one method of one module.
     *
     * @param  string $module
     * @param  string $method
     * @param  object $object
     * @param  string $vars
     * @static
     * @access public
     * @return bool
     */
    public static function hasPriv($module, $method, $object = null, $vars = '')
    {
        global $app, $lang;
        $module = strtolower($module);
        $method = strtolower($method);
        parse_str($vars, $params);

        if(empty($params['storyType']) and $module == 'story' and !empty($app->params['storyType']) and strpos(",story,requirement,", ",{$app->params['storyType']},") !== false) $module = $app->params['storyType'];
        if($module == 'story' and !empty($params['storyType']) and strpos(",story,requirement,", ",{$params['storyType']},") !== false) $module = $params['storyType'];
        if($module == 'product' and $method == 'browse' and !empty($app->params['storyType']) and $app->params['storyType'] == 'requirement') $method = 'requirement';
        if($module == 'product' and $method == 'browse' and !empty($params['storyType']) and $params['storyType'] == 'requirement') $method = 'requirement';
        if($module == 'story' and $method == 'linkrequirements') $module = 'requirement';

        /* If the user is doing a tutorial, have all tutorial privs. */
        if(defined('TUTORIAL'))
        {
            $app->loadLang('tutorial');
            foreach($lang->tutorial->tasks as $task)
            {
                if($task['nav']['module'] == $module and $task['nav']['method'] = $method) return true;
            }
        }

        /* Check the parent object is closed. */
        if(!empty($method) and strpos('close|batchclose', $method) === false and !commonModel::canBeChanged($module, $object)) return false;

        /* Check is the super admin or not. */
        if(!empty($app->user->admin) or strpos($app->company->admins, ",{$app->user->account},") !== false) return true;

        /* If is the program/project/product/execution admin, have all program privs. */
        if($app->config->vision != 'lite')
        {
            $inProject = (isset($lang->navGroup->$module) and $lang->navGroup->$module == 'project');
            if($inProject and $app->session->project and (strpos(",{$app->user->rights['projects']},", ",{$app->session->project},") !== false or strpos(",{$app->user->rights['projects']},", ',all,') !== false)) return true;

            $inProduct = (isset($lang->navGroup->$module) and $lang->navGroup->$module == 'product');
            if($inProduct and $app->session->product and (strpos(",{$app->user->rights['products']},", ",{$app->session->product},") !== false or strpos(",{$app->user->rights['products']},", ',all,') !== false)) return true;

            $inProgram = (isset($lang->navGroup->$module) and $lang->navGroup->$module == 'program');
            if($inProgram and $app->session->program and (strpos(",{$app->user->rights['programs']},", ",{$app->session->program},") !== false or strpos(",{$app->user->rights['programs']},", ',all,') !== false)) return true;

            $inExecution = (isset($lang->navGroup->$module) and $lang->navGroup->$module == 'execution');
            if($inExecution and $app->session->execution and (strpos(",{$app->user->rights['executions']},", ",{$app->session->execution},") !== false or strpos(",{$app->user->rights['executions']},", ',all,') !== false)) return true;
        }

        /* If not super admin, check the rights. */
        $rights = $app->user->rights['rights'];
        $acls   = $app->user->rights['acls'];

        /* White list of import method. */
        if(in_array($module, $app->config->importWhiteList) and $method == 'showimport')
        {
            if(isset($rights[$module]['import']) and commonModel::hasDBPriv($object, $module, 'import')) return true;
        }

        if(isset($rights[$module][$method]))
        {
            if(!commonModel::hasDBPriv($object, $module, $method)) return false;

            if(empty($acls['views'])) return true;
            $menu = isset($lang->navGroup->$module) ? $lang->navGroup->$module : $module;
            if($module == 'my' and $method == 'team') $menu = 'system'; // Fix bug #18642.
            $menu = strtolower($menu);
            if($menu != 'qa' and !isset($lang->$menu->menu)) return true;
            if(($menu == 'my' and $method != 'team')or $menu == 'index' or $module == 'tree') return true;
            if($module == 'company' and $method == 'dynamic') return true;
            if($module == 'action' and $method == 'editcomment') return true;
            if($module == 'action' and $method == 'comment') return true;
            if($module == 'report' and $method == 'export') return true;
            if(!isset($acls['views'][$menu])) return false;

            return true;
        }

        return false;
    }

    /**
     * Reset project priv.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function resetProjectPriv($projectID = 0)
    {
        /* Get user program priv. */
        if(empty($projectID) and $this->session->project) $projectID = $this->session->project;
        if(empty($projectID)) return;

        $program = $this->dao->findByID($projectID)->from(TABLE_PROJECT)->fetch();
        if(empty($program)) return;

        $programRights = $this->dao->select('t3.module, t3.method')->from(TABLE_GROUP)->alias('t1')
            ->leftJoin(TABLE_USERGROUP)->alias('t2')->on('t1.id = t2.`group`')
            ->leftJoin(TABLE_GROUPPRIV)->alias('t3')->on('t2.`group`=t3.`group`')
            ->where('t1.project')->eq($program->id)
            ->andWhere('t2.account')->eq($this->app->user->account)
            ->fetchAll();

        /* Group priv by module the same as rights. */
        $programRightGroup = array();
        foreach($programRights as $programRight) $programRightGroup[strtolower($programRight->module)][strtolower($programRight->method)] = 1;

        /* Reset priv by program privway. */
        $this->app->user = clone $_SESSION['user'];
        $rights = $this->app->user->rights['rights'];

        if($this->app->user->account == $program->openedBy or $this->app->user->account == $program->PM) $program->auth = 'extend';

        if($program->auth == 'extend') $this->app->user->rights['rights'] = array_merge_recursive($programRightGroup, $rights);
        if($program->auth == 'reset')
        {
            /* If priv way is reset, unset common program priv, and cover by program priv. */
            $projectPrivs = $this->loadModel('project')->processProjectPrivs($program->multiple ? $program->model : 'noSprint');
            foreach($projectPrivs as $module => $methods)
            {
                foreach($methods as $method => $label)
                {
                    $module = strtolower($module);
                    $method = strtolower($method);
                    if(isset($rights[$module][$method])) unset($rights[$module][$method]);
                }
            }

            $recomputedRights = array_merge($rights, $programRightGroup);

            /* Set base priv for project. */
            $projectRights = zget($this->app->user->rights['rights'], 'project', array());
            if(isset($projectRights['browse']) and !isset($recomputedRights['project']['browse'])) $recomputedRights['project']['browse'] = 1;
            if(isset($projectRights['kanban']) and !isset($recomputedRights['project']['kanban'])) $recomputedRights['project']['kanban'] = 1;
            if(isset($projectRights['index'])  and !isset($recomputedRights['project']['index']))  $recomputedRights['project']['index']  = 1;

            $this->app->user->rights['rights'] = $recomputedRights;
            $this->session->set('user', $this->app->user);
        }
    }

    /**
     * Check db priv.
     *
     * @param  object $object
     * @param  string $module
     * @param  string $method
     * @static
     * @access public
     * @return void
     */
    public static function hasDBPriv($object, $module = '', $method = '')
    {
        global $app;

        if(!empty($app->user->admin)) return true;
        if($module == 'todo' and ($method == 'create' or $method == 'batchcreate')) return true;
        if($module == 'effort' and ($method == 'batchcreate' or $method == 'createforobject')) return true;

        /* Limited execution. */
        $limitedExecution = false;
        if(!empty($module) and in_array($module, array('task', 'story')) and !empty($object->execution) or
           !empty($module) and $module == 'execution' and !empty($object->id)
        )
        {
            $objectID = '';
            if($module == 'execution' and !empty($object->id)) $objectID = $object->id;
            if(in_array($module, array('task', 'story')) and !empty($object->execution)) $objectID = $object->execution;

            $limitedExecutions = !empty($_SESSION['limitedExecutions']) ? $_SESSION['limitedExecutions'] : '';
            if($objectID and strpos(",{$limitedExecutions},", ",$objectID,") !== false) $limitedExecution = true;
        }
        if(empty($app->user->rights['rights']['my']['limited']) and !$limitedExecution) return true;

        if(!empty($method) and strpos($method, 'batch')  === 0) return false;
        if(!empty($method) and strpos($method, 'link')   === 0) return false;
        if(!empty($method) and strpos($method, 'create') === 0) return false;
        if(!empty($method) and strpos($method, 'import') === 0) return false;

        if(empty($object)) return true;

        if(!empty($object->openedBy)      and $object->openedBy     == $app->user->account or
            !empty($object->addedBy)      and $object->addedBy      == $app->user->account or
            !empty($object->account)      and $object->account      == $app->user->account or
            !empty($object->assignedTo)   and $object->assignedTo   == $app->user->account or
            !empty($object->finishedBy)   and $object->finishedBy   == $app->user->account or
            !empty($object->canceledBy)   and $object->canceledBy   == $app->user->account or
            !empty($object->closedBy)     and $object->closedBy     == $app->user->account or
            !empty($object->lastEditedBy) and $object->lastEditedBy == $app->user->account)
        {
            return true;
        }

        return false;
    }

    /**
     * Check whether IP in white list.
     *
     * @param  string $ipWhiteList
     * @access public
     * @return bool
     */
    public function checkIP($ipWhiteList = '')
    {
        $ip = helper::getRemoteIp();

        if(!$ipWhiteList) $ipWhiteList = $this->config->ipWhiteList;

        /* If the ip white list is '*'. */
        if($ipWhiteList == '*') return true;

        /* The ip is same as ip in white list. */
        if($ip == $ipWhiteList) return true;

        /* If the ip in white list is like 192.168.1.1,192.168.1.10. */
        if(strpos($ipWhiteList, ',') !== false)
        {
            $ipArr = explode(',', $ipWhiteList);
            foreach($ipArr as $ipRule)
            {
                if($this->checkIP($ipRule)) return true;
            }
            return false;
        }

        /* If the ip in white list is like 192.168.1.1-192.168.1.10. */
        if(strpos($ipWhiteList, '-') !== false)
        {
            list($min, $max) = explode('-', $ipWhiteList);
            $min = ip2long(trim($min));
            $max = ip2long(trim($max));
            $ip  = ip2long(trim($ip));

            return $ip >= $min and $ip <= $max;
        }

        /* If the ip in white list is like 192.168.1.*. */
        if(strpos($ipWhiteList, '*') !== false)
        {
            $regCount = substr_count($ipWhiteList, '.');
            if($regCount == 3)
            {
                $min = str_replace('*', '0', $ipWhiteList);
                $max = str_replace('*', '255', $ipWhiteList);
            }
            elseif($regCount == 2)
            {
                $min = str_replace('*', '0.0', $ipWhiteList);
                $max = str_replace('*', '255.255', $ipWhiteList);
            }
            elseif($regCount == 1)
            {
                $min = str_replace('*', '0.0.0', $ipWhiteList);
                $max = str_replace('*', '255.255.255', $ipWhiteList);
            }
            $min = ip2long(trim($min));
            $max = ip2long(trim($max));
            $ip  = ip2long(trim($ip));

            return ($ip >= $min and $ip <= $max);
        }

        /* If the ip in white list is in IP/CIDR format eg 127.0.0.1/24. Thanks to zcat. */
        if(strpos($ipWhiteList, '/') == false) $ipWhiteList .= '/32';
        list($ipWhiteList, $netmask) = explode('/', $ipWhiteList, 2);

        $ip          = ip2long($ip);
        $ipWhiteList = ip2long($ipWhiteList);
        $wildcard    = pow(2, (32 - $netmask)) - 1;
        $netmask     = ~ $wildcard;

        return (($ip & $netmask) == ($ipWhiteList & $netmask));
    }

    /**
     * Get the full url of the system.
     *
     * @access public
     * @return string
     */
    public static function getSysURL()
    {
        $httpType = (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
        if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') $httpType = 'https';
        if(isset($_SERVER['REQUEST_SCHEME']) and strtolower($_SERVER['REQUEST_SCHEME']) == 'https') $httpType = 'https';
        $httpHost = $_SERVER['HTTP_HOST'];
        return "$httpType://$httpHost";
    }

    /**
     * Check whether view type is tutorial
     * @access public
     * @return boolean
     */
    public static function isTutorialMode()
    {
        return (isset($_SESSION['tutorialMode']) and $_SESSION['tutorialMode']);
    }

    /**
     * Convert items to Pinyin.
     *
     * @param  array    $items
     * @static
     * @access public
     * @return array
     */
    public static function convert2Pinyin($items)
    {
        global $app;
        static $allConverted = array();
        static $pinyin;
        if(empty($pinyin)) $pinyin = $app->loadClass('pinyin');

        $sign = ' aNdAnD ';
        $notConvertedItems = array_diff($items, array_keys($allConverted));

        if($notConvertedItems)
        {
            $convertedPinYin = $pinyin->romanize(join($sign, $notConvertedItems));
            $itemsPinYin     = explode(trim($sign), $convertedPinYin);
            foreach($notConvertedItems as $item)
            {
                $itemPinYin  = array_shift($itemsPinYin);
                $wordsPinYin = explode("\t", trim($itemPinYin));

                $abbr = '';
                foreach($wordsPinYin as $i => $wordPinyin)
                {
                    if($wordPinyin)
                    {
                        $letter = $wordPinyin[0];
                        if(preg_match('/\w/', $letter)) $abbr .= $letter;
                    }
                }

                $allConverted[$item] = mb_strtolower(join($wordsPinYin) . ' ' . $abbr);
            }
        }

        $convertedItems = array();
        foreach($items as $item) $convertedItems[$item] = zget($allConverted, $item, null);

        return $convertedItems;
    }

    /**
     * Check an entry of new API.
     *
     * @access public
     * @return void
     */
    private function checkNewEntry()
    {
        $entry = $this->loadModel('entry')->getByKey(session_id());
        if(!$entry or !$entry->account or !$this->checkIP($entry->ip)) return false;

        $user = $this->dao->findByAccount($entry->account)->from(TABLE_USER)->andWhere('deleted')->eq(0)->fetch();
        if(!$user) return false;

        $user->last   = time();
        $user->rights = $this->loadModel('user')->authorize($user->account);
        $user->groups = $this->user->getGroups($user->account);
        $user->view   = $this->user->grantUserView($user->account, $user->rights['acls']);
        $user->admin  = strpos($this->app->company->admins, ",{$user->account},") !== false;
        $this->session->set('user', $user);
        $this->app->user = $user;
    }

    /**
     * Check an entry.
     *
     * @access public
     * @return void
     */
    public function checkEntry()
    {
        /* if the API is new version, goto checkNewEntry. */
        if($this->app->version) return $this->checkNewEntry();

        /* Old version. */
        if(!isset($_GET[$this->config->moduleVar]) or !isset($_GET[$this->config->methodVar])) $this->response('EMPTY_ENTRY');
        if($this->isOpenMethod($_GET[$this->config->moduleVar], $_GET[$this->config->methodVar])) return true;

        if(!$this->get->code)  $this->response('PARAM_CODE_MISSING');
        if(!$this->get->token) $this->response('PARAM_TOKEN_MISSING');

        $entry = $this->loadModel('entry')->getByCode($this->get->code);

        if(!$entry)                         $this->response('EMPTY_ENTRY');
        if(!$entry->key)                    $this->response('EMPTY_KEY');
        if(!$this->checkIP($entry->ip))     $this->response('IP_DENIED');
        if(!$this->checkEntryToken($entry)) $this->response('INVALID_TOKEN');
        if($entry->freePasswd == 0 and empty($entry->account)) $this->response('ACCOUNT_UNBOUND');

        $isFreepasswd = ($_GET['m'] == 'user' and strtolower($_GET['f']) == 'apilogin' and $_GET['account'] and $entry->freePasswd);
        if($isFreepasswd) $entry->account = $_GET['account'];

        $user = $this->dao->findByAccount($entry->account)->from(TABLE_USER)->andWhere('deleted')->eq(0)->fetch();
        if(!$user) $this->response('INVALID_ACCOUNT');

        $this->loadModel('user');
        $user->last   = time();
        $user->rights = $this->user->authorize($user->account);
        $user->groups = $this->user->getGroups($user->account);
        $user->view   = $this->user->grantUserView($user->account, $user->rights['acls']);
        $user->admin  = strpos($this->app->company->admins, ",{$user->account},") !== false;
        $this->session->set('user', $user);
        $this->app->user = $user;

        $this->dao->update(TABLE_USER)->set('last')->eq($user->last)->where('account')->eq($user->account)->exec();
        $this->loadModel('action')->create('user', $user->id, 'login');
        $this->loadModel('score')->create('user', 'login');

        if($isFreepasswd) die(js::locate($this->config->webRoot));

        $this->session->set('ENTRY_CODE', $this->get->code);
        $this->session->set('VALID_ENTRY', md5(md5($this->get->code) . helper::getRemoteIp()));
        $this->loadModel('entry')->saveLog($entry->id, $this->server->request_uri);

        /* Add for task #5384. */
        if($_SERVER['REQUEST_METHOD'] == 'POST' and empty($_POST))
        {
            $post = file_get_contents("php://input");
            if(!empty($post)) $post  = json_decode($post, true);
            if(!empty($post)) $_POST = $post;
        }

        unset($_GET['code']);
        unset($_GET['token']);
    }

    /**
     * Check token of an entry.
     *
     * @param  object $entry
     * @access public
     * @return bool
     */
    public function checkEntryToken($entry)
    {
        parse_str($this->server->query_String, $queryString);
        unset($queryString['token']);

        /* Change for task #5384. */
        if(isset($queryString['time']))
        {
            $timestamp = $queryString['time'];
            if(strlen($timestamp) > 10) $timestamp = substr($timestamp, 0, 10);
            if(strlen($timestamp) != 10 or $timestamp[0] >= '4') $this->response('ERROR_TIMESTAMP');

            $result = $this->get->token == md5($entry->code . $entry->key . $queryString['time']);
            if($result)
            {
                if($timestamp <= $entry->calledTime) $this->response('CALLED_TIME');
                $this->loadModel('entry')->updateCalledTime($entry->code, $timestamp);
                unset($_GET['time']);
                return $result;
            }
        }

        $queryString = http_build_query($queryString);
        return $this->get->token == md5(md5($queryString) . $entry->key);
    }

    /**
     * Check Not CN Lang.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function checkNotCN()
    {
        global $app;
        return strpos('|zh-cn|zh-tw|', '|' . $app->getClientLang() . '|') === false;
    }

    /**
     * Check the object can be changed.
     *
     * @param  string $module
     * @param  object $object
     * @static
     * @access public
     * @return bool
     */
    public static function canBeChanged($module, $object = null)
    {
        global $app, $config;
        static $productsStatus   = array();
        static $executionsStatus = array();

        /* Check the product is closed. */
        if(!empty($object->product) and is_numeric($object->product) and empty($config->CRProduct))
        {
            if(!isset($productsStatus[$object->product]))
            {
                $product = $app->control->loadModel('product')->getByID($object->product);
                $productsStatus[$object->product] = $product ? $product->status : '';
            }
            if($productsStatus[$object->product] == 'closed') return false;
        }

        /* Check the execution is closed. */
        $productModuleList = array('story', 'bug', 'testtask');
        if(!in_array($module, $productModuleList) and !empty($object->execution) and is_numeric($object->execution) and empty($config->CRExecution))
        {
            if(!isset($executionsStatus[$object->execution]))
            {
                $execution = $app->control->loadModel('execution')->getByID($object->execution);
                $executionsStatus[$object->execution] = $execution ? $execution->status : '';
            }
            if($executionsStatus[$object->execution] == 'closed') return false;
        }

        return true;
    }

    /**
     * Check object can modify.
     *
     * @param  string $type    product|Execution
     * @param  object $object
     * @static
     * @access public
     * @return bool
     */
    public static function canModify($type, $object)
    {
        global $config;

        if(empty($object)) return true;

        if($type == 'product'   and empty($config->CRProduct)   and $object->status == 'closed') return false;
        if($type == 'execution' and empty($config->CRExecution) and $object->status == 'closed') return false;

        return true;
    }

    /**
     * Response.
     *
     * @param  string $code
     * @access public
     * @return void
     */
    public function response($code)
    {
        $response = new stdclass();
        if(isset($this->config->entry->errcode))
        {
            $response->errcode = $this->config->entry->errcode[$code];
            $response->errmsg  = urlencode($this->lang->entry->errmsg[$code]);

            die(urldecode(json_encode($response)));
        }
        else
        {
            $response->error = $code;
            die(urldecode(json_encode($response)));
        }
    }


    /**
     * Http response with header.
     *
     * @param  string       $url
     * @param  string|array $data
     * @param  array        $options   This is option and value pair, like CURLOPT_HEADER => true. Use curl_setopt function to set options.
     * @param  array        $headers   Set request headers.
     * @static
     * @access public
     * @return string
     */
    public static function httpWithHeader($url, $data = null, $options = array(), $headers = array())
    {
        global $lang, $app;
        if(!extension_loaded('curl')) return json_encode(array('result' => 'fail', 'message' => $lang->error->noCurlExt));

        commonModel::$requestErrors = array();

        if(!is_array($headers)) $headers = (array)$headers;
        $headers[] = "API-RemoteIP: " . helper::getRemoteIp();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);

        if(!empty($data))
        {
            if(is_object($data)) $data = (array) $data;
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if($options) curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $errors   = curl_error($curl);

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerString = substr($response, 0, $headerSize);
        $body       = substr($response, $headerSize);

        /* Parse header. */
        $header = explode("\n", $headerString);
        $newHeader = array();
        foreach($header as $item)
        {
            $field = explode(':', $item);
            if(count($field) < 2) continue;
            $headerkey = array_shift($field);
            $newHeader[$headerkey] = join('', $field);
        }
        curl_close($curl);


        $logFile = $app->getLogRoot() . 'saas.'. date('Ymd') . '.log.php';
        if(!file_exists($logFile)) file_put_contents($logFile, '<?php die(); ?' . '>');

        $fh = @fopen($logFile, 'a');
        if($fh)
        {
            fwrite($fh, date('Ymd H:i:s') . ": " . $app->getURI() . "\n");
            fwrite($fh, "url:    " . $url . "\n");
            if(!empty($data)) fwrite($fh, "data:   " . print_r($data, true) . "\n");
            fwrite($fh, "results:" . print_r($response, true) . "\n");
            if(!empty($errors)) fwrite($fh, "errors: " . $errors . "\n");
            fclose($fh);
        }

        if($errors) commonModel::$requestErrors[] = $errors;

        return array('body' => $body, 'header' => $newHeader);
    }

    /**
     * Http.
     *
     * @param  string       $url
     * @param  string|array $data
     * @param  array        $options   This is option and value pair, like CURLOPT_HEADER => true. Use curl_setopt function to set options.
     * @param  array        $headers   Set request headers.
     * @param  string       $dataType
     * @param  string       $method    POST|PATCH|PUT
     * @static
     * @access public
     * @return string
     */
    public static function http($url, $data = null, $options = array(), $headers = array(), $dataType = 'data', $method = 'POST')
    {
        global $lang, $app;
        if(!extension_loaded('curl'))
        {
             if($dataType == 'json') return print($lang->error->noCurlExt);
             return json_encode(array('result' => 'fail', 'message' => $lang->error->noCurlExt));
        }

        commonModel::$requestErrors = array();

        if(!is_array($headers)) $headers = (array)$headers;
        $headers[] = "API-RemoteIP: " . helper::getRemoteIp();
        if($dataType == 'json')
        {
            $headers[] = 'Content-Type: application/json;charset=utf-8';
            if(!empty($data)) $data = json_encode($data);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 2);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);

        if(!empty($data))
        {
            if(is_object($data)) $data = (array) $data;
            if($method == 'POST') curl_setopt($curl, CURLOPT_POST, true);
            if(in_array($method, array('PATCH', 'PUT'))) curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if($options) curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $errors   = curl_error($curl);

        curl_close($curl);

        $logFile = $app->getLogRoot() . 'saas.'. date('Ymd') . '.log.php';
        if(!file_exists($logFile)) file_put_contents($logFile, '<?php die(); ?' . '>');

        $fh = @fopen($logFile, 'a');
        if($fh)
        {
            fwrite($fh, date('Ymd H:i:s') . ": " . $app->getURI() . "\n");
            fwrite($fh, "url:    " . $url . "\n");
            if(!empty($data)) fwrite($fh, "data:   " . print_r($data, true) . "\n");
            fwrite($fh, "results:" . print_r($response, true) . "\n");
            if(!empty($errors)) fwrite($fh, "errors: " . $errors . "\n");
            fclose($fh);
        }

        if($errors) commonModel::$requestErrors[] = $errors;

        return $response;
    }

    /**
     * Set main menu.
     *
     * @static
     * @access public
     * @return string
     */
    public static function setMainMenu()
    {
        global $app, $lang;

        $tab = $app->tab;

        $isTutorialMode = common::isTutorialMode();
        $currentModule  = $isTutorialMode ? $app->moduleName : $app->rawModule;
        $currentMethod  = $isTutorialMode ? $app->methodName : $app->rawMethod;
        $currentMethod  = strtolower($currentMethod);

        /* If homeMenu is not exists or unset, display menu. */
        if(!isset($lang->$tab->homeMenu))
        {
            $lang->menu      = isset($lang->$tab->menu) ? $lang->$tab->menu : array();
            $lang->menuOrder = isset($lang->$tab->menuOrder) ? $lang->$tab->menuOrder : array();
            return;
        }

        if($currentModule == $tab and $currentMethod == 'create')
        {
            $lang->menu = $lang->$tab->homeMenu;
            return;
        }

        /* If the method is in homeMenu, display homeMenu. */
        foreach($lang->$tab->homeMenu as $menu)
        {
            $link   = is_array($menu) ? $menu['link'] : $menu;
            $params = explode('|', $link);
            $method = strtolower($params[2]);

            if($method == $currentMethod)
            {
                $lang->menu = $lang->$tab->homeMenu;
                return;
            }

            if(isset($menu['alias']) and in_array($currentMethod, explode(',', strtolower($menu['alias']))))
            {
                $lang->menu = $lang->$tab->homeMenu;
                return;
            }

            if(isset($menu['subModule']) and strpos(",{$menu['subModule']},", ",$currentModule,") !== false)
            {
                $lang->menu = $lang->$tab->homeMenu;
                return;
            }
        }

        /* Default, display menu. */
        $lang->menu      = isset($lang->$tab->menu) ? $lang->$tab->menu : array();
        $lang->menuOrder = isset($lang->$tab->menuOrder) ? $lang->$tab->menuOrder : array();
    }

    /**
     * Get relations for two object.
     *
     * @param  varchar $atype
     * @param  int     $aid
     * @param  varchar $btype
     * @param  int     $bid
     *
     * @access public
     * @return string
     */
    public function getRelations($AType = '', $AID = 0, $BType = '', $BID = 0)
    {
        return $this->dao->select('*')->from(TABLE_RELATION)
            ->where('AType')->eq($AType)
            ->andWhere('AID')->eq($AID)
            ->andwhere('BType')->eq($BType)
            ->beginif($BID)->andwhere('BID')->eq($BID)->fi()
            ->fetchAll();
    }

    /**
     * Replace the %s of one key of a menu by objectID or $params.
     *
     * All the menus are defined in the common's language file. But there're many dynamic params, so in the defination,
     * we used %s as placeholder. These %s should be setted in one module.
     *
     * @param  string  $moduleName
     * @param  int     $objectID
     * @param  array   $params
     *
     * @access public
     * @return string
     */
    static public function setMenuVars($moduleName, $objectID, $params = array())
    {
        global $app, $lang;

        $menuKey = 'menu';
        if($app->viewType == 'mhtml') $menuKey = 'webMenu';

        foreach($lang->$moduleName->$menuKey as $label => $menu)
        {
            $lang->$moduleName->$menuKey->$label = self::setMenuVarsEx($menu, $objectID, $params);
            if(isset($menu['subMenu']))
            {
                foreach($menu['subMenu'] as $key1 => $subMenu)
                {
                    $lang->$moduleName->$menuKey->{$label}['subMenu']->$key1 = self::setMenuVarsEx($subMenu, $objectID, $params);
                }
            }

            if(!isset($menu['dropMenu'])) continue;

            foreach($menu['dropMenu'] as $key2 => $dropMenu)
            {
                $lang->$moduleName->$menuKey->{$label}['dropMenu']->$key2 = self::setMenuVarsEx($dropMenu, $objectID, $params);

                if(!isset($dropMenu['subMenu'])) continue;

                foreach($dropMenu['subMenu'] as $key3 => $subMenu)
                {
                    $lang->$moduleName->$menuKey->{$label}['dropMenu']->$key3 = self::setMenuVarsEx($subMenu, $objectID, $params);
                }
            }
        }

        /* If objectID is set, cannot use homeMenu. */
        unset($lang->$moduleName->homeMenu);
    }

    /**
     * Check menuVars replaced.
     *
     * @static
     * @access public
     * @return void
     */
    public static function checkMenuVarsReplaced()
    {
        global $app, $lang;

        $tab          = $app->tab;
        $varsReplaced = true;
        foreach($lang->menu as $menuKey => $menu)
        {
            if(isset($menu['link']) and strpos($menu['link'], '%s') !== false) $varsReplaced = false;
            if(!isset($menu['link']) and is_string($menu) and strpos($menu, '%s') !== false) $varsReplaced = false;
            if(!$varsReplaced) break;
        }

        if(!$varsReplaced and strpos("|program|product|project|execution|qa|", "|{$tab}|") !== false)
        {
            $isTutorialMode = common::isTutorialMode();
            $currentModule  = $isTutorialMode ? $app->moduleName : $app->rawModule;

            if(isset($lang->$currentModule->menu))
            {
                $lang->menu      = isset($lang->$currentModule->menu) ? $lang->$currentModule->menu : array();
                $lang->menuOrder = isset($lang->$currentModule->menuOrder) ? $lang->$currentModule->menuOrder : array();
                $app->tab        = zget($lang->navGroup, $currentModule);
            }
            else
            {
                self::setMenuVars($tab, (int)$app->session->$tab);
            }
        }
    }

    /*
     * Replace the %s of one key of a menu by objectID or $params.
     * @param  object  $menu
     * @param  int     $objectID
     * @param  array   $params
     */
    static public function setMenuVarsEx($menu, $objectID, $params = array())
    {
        if(is_array($menu))
        {
            if(!isset($menu['link'])) return $menu;

            $link = sprintf($menu['link'], $objectID);
            $menu['link'] = vsprintf($link, $params);
        }
        else
        {
            $menu = sprintf($menu, $objectID);
            $menu = vsprintf($menu, $params);
        }

        return $menu;
    }

    /**
     * Process markdown.
     *
     * @param  string  $markdown
     * @static
     * @access public
     * @return string
     */
    static public function processMarkdown($markdown)
    {
        if(empty($markdown)) return false;

        global $app;
        $app->loadClass('parsedownextraplugin');

        $Parsedown = new parsedownextraplugin;

        $Parsedown->voidElementSuffix = '>'; // HTML5

        return $Parsedown->text($markdown);
    }

    /**
     * Sort featureBar.
     *
     * @param  string $module
     * @param  string $method
     * @static
     * @access public
     * @return bool
     */
    public static function sortFeatureMenu($module = '', $method = '')
    {
        global $lang, $config, $app;

        $module = $module ? $module : $app->rawModule;
        $method = $method ? $method : $app->rawMethod;

        /* It will be sorted according to the workflow in the future */
        if(!empty($config->featureBarSort[$module][$method]))
        {
            $featureBar = array();
            if(empty($lang->$module->featureBar[$method])) return false;
            foreach($lang->$module->featureBar[$method] as $key => $label)
            {
                foreach($config->featureBarSort[$module][$method] as $currentKey => $afterKey)
                {
                    if($key == $currentKey) continue;
                    $featureBar[$method][$key] = $label;
                    if($key == $afterKey && !empty($lang->$module->featureBar[$method][$currentKey]))
                    {
                        $featureBar[$method][$currentKey] = $lang->$module->featureBar[$method][$currentKey];
                    }
                }
            }
            $lang->$module->featureBar = $featureBar;
        }

        return true;
    }

    /**
     * Check valid row.
     *
     * @param  string $objectType
     * @param  array  $postData
     * @param  int    $index
     * @access public
     * @return bool
     */
    public function checkValidRow($objectType, $postData = array(), $index = 0)
    {
        if(empty($postData)) return false;

        foreach($postData as $key => $value)
        {
            if(!is_array($value) or strpos($this->config->$objectType->excludeCheckFileds, ",$key,") !== false) continue;
            if(isset($value[$index]) and !empty($value[$index]) and $value[$index] != 'ditto') return true;
        }

        return false;
    }
}

class common extends commonModel
{
}
