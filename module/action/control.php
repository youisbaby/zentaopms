<?php
/**
 * The control file of action module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     action
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class action extends control
{
    /**
     * 创建一个动作或者删除所有的补丁动作，此方法由Ztools使用。
     * Create a action or delete all patch actions, this method is used by the Ztools.
     *
     * @param  string $objectType
     * @param  string $actionType
     * @param  string $objectName
     * @access public
     * @return void
     */
    public function create(string $objectType, string $actionType, string $objectName)
    {
        $actionID = $this->action->create($objectType, 0, $actionType, '', $objectName);
        if($actionID)
        {
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
        }
        else
        {
            $this->send(array('result' => 'fail', 'message' => 'error'));
        }
    }

    /**
     * 回收站。
     * Trash.
     *
     * @param  string $browseType
     * @param  string $type all|hidden
     * @param  bool   $byQuery
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function trash(string $browseType = 'all', string $type = 'all', bool $byQuery = false, int $queryID = 0, string $orderBy = 'id_desc', int $recTotal = 0, int $recPerPage = 20, int $pageID = 1)
    {
        $this->loadModel('backup');

        /* Url存入session。 */
        /* Save url into session. */
        $uri = $this->app->getURI(true);
        $this->session->set('productList',        $uri, 'product');
        $this->session->set('productPlanList',    $uri, 'product');
        $this->session->set('storyList',          $uri, 'product');
        $this->session->set('releaseList',        $uri, 'product');
        $this->session->set('programList',        $uri, 'program');
        $this->session->set('projectList',        $uri, 'project');
        $this->session->set('executionList',      $uri, 'execution');
        $this->session->set('taskList',           $uri, 'execution');
        $this->session->set('buildList',          $uri, 'execution');
        $this->session->set('bugList',            $uri, 'qa');
        $this->session->set('caseList',           $uri, 'qa');
        $this->session->set('testtaskList',       $uri, 'qa');
        $this->session->set('docList',            $uri, 'doc');
        $this->session->set('opportunityList',    $uri, 'project');
        $this->session->set('riskList',           $uri, 'project');
        $this->session->set('trainplanList',      $uri, 'project');
        $this->session->set('roomList',           $uri, 'admin');
        $this->session->set('researchplanList',   $uri, 'project');
        $this->session->set('researchreportList', $uri, 'project');
        $this->session->set('meetingList',        $uri, 'project');
        $this->session->set('designList',         $uri, 'project');
        $this->session->set('storyLibList',       $uri, 'assetlib');
        $this->session->set('issueLibList',       $uri, 'assetlib');
        $this->session->set('riskLibList',        $uri, 'assetlib');
        $this->session->set('opportunityLibList', $uri, 'assetlib');
        $this->session->set('practiceLibList',    $uri, 'assetlib');
        $this->session->set('componentLibList',   $uri, 'assetlib');

        /* 保存用于替换搜索语言项的对象名称。 */
        /* Save the object name used to replace the search language item. */
        $this->session->set('objectName', zget($this->lang->action->objectTypes, $browseType, ''), 'admin');

        /* 搭建搜索表单。*/
        /* Build the search form. */
        $queryID   = (int)$queryID;
        $actionURL = $this->createLink('action', 'trash', "browseType=$browseType&type=$type&byQuery=true&queryID=myQueryID");
        $this->action->buildTrashSearchForm($queryID, $actionURL);

        /* 获取回收站内的对象。 */
        /* Get deleted objects. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* 解析排序字段。 */
        /* Append id for second sort. */
        $sort           = common::appendOrder($orderBy);
        $trashes        = $byQuery ? $this->action->getTrashesBySearch($browseType, $type, $queryID, $sort, $pager) : $this->action->getTrashes($browseType, $type, $sort, $pager);
        $objectTypeList = $this->action->getTrashObjectTypes($type);
        $objectTypeList = array_keys($objectTypeList);

        /* 获取头部导航标题。 */
        /* Build the header navigation title. */
        $preferredType       = array();
        $moreType            = array();
        $preferredTypeConfig = $this->config->action->preferredType->ALM;
        if($this->config->systemMode == 'light') $preferredTypeConfig = $this->config->action->preferredType->light;
        foreach($objectTypeList as $objectType)
        {
            if(!isset($this->config->objectTables[$objectType])) continue;
            in_array($objectType, $preferredTypeConfig) ? $preferredType[$objectType] = $objectType : $moreType[$objectType] = $objectType;
        }
        if(count($preferredType) < $this->config->action->preferredTypeNum)
        {
            $toPreferredType = array_splice($moreType, 0, $this->config->action->preferredTypeNum - count($preferredType));
            $preferredType   = $preferredType + $toPreferredType;
        }

        /* 获取执行所属的项目名称。 */
        /* Get the projects name of executions. */
        if($browseType == 'execution')
        {
            $this->loadModel('project');
            $projectIdList = array();
            foreach($trashes as $trash) $projectIdList[] = $trash->project;
            $projectList = $this->project->getByIdList($projectIdList, 'all');
            $this->view->projectList = $projectList;
        }

        /* 获取用户故事所属的产品名称。 */
        /* Get the products name of story. */
        if(in_array($browseType, array('story', 'requirement')))
        {
            $this->loadModel('story');
            $storyIdList = array();
            foreach($trashes as $trash) $storyIdList[] = $trash->objectID;
            $productList = $this->story->getByList($storyIdList, 'all');
            $this->view->productList = $productList;
        }

        /* 获取任务的执行名称。 */
        /* Get the executions name of task. */
        if($browseType == 'task')
        {
            $this->app->loadLang('task');
            $this->loadModel('execution');
            $executionIdList = array();
            foreach($trashes as $trash) $executionIdList[] = $trash->execution;
            $executionList = $this->execution->getByIdList($executionIdList, 'all');
            $this->view->executionList = $executionList;
        }

        /* 补充操作记录的信息。 */
        /* Supplement the information recorded by the operation. */
        foreach($trashes as $action)
        {
            if($action->objectType == 'pivot')
            {
                $pivotNames = json_decode($action->objectName, true);
                $action->objectName = zget($pivotNames, $this->app->getClientLang(), '');
                if(empty($action->objectName))
                {
                    $pivotNames = array_filter($pivotNames);
                    $action->objectName = reset($pivotNames);
                }
            }
            else
            {
                $module     = $action->objectType == 'case' ? 'testcase' : $action->objectType;
                $params     = $action->objectType == 'user' ? "account={$action->objectName}" : "id={$action->objectID}";
                $methodName = 'view';
                if($module == 'caselib')
                {
                    $methodName = 'view';
                    $module     = 'caselib';
                }
                if($module == 'basicmeas')
                {
                    $module     = 'measurement';
                    $methodName = 'setSQL';
                    $params     = "id={$action->objectID}";
                }
                if($action->objectType == 'api')
                {
                    $params     = "libID=0&moduelID=0&apiID={$action->objectID}";
                    $methodName = 'index';
                }
                if(in_array($module, array('traincourse','traincontents')))
                {
                    $methodName = $module == 'traincourse' ? 'viewcourse' : 'viewchapter';
                    $module     = 'traincourse';
                }
                if(isset($this->config->action->customFlows[$action->objectType]))
                {
                    $flow   = $this->config->action->customFlows[$action->objectType];
                    $module = $flow->module;
                }
                if(strpos($this->config->action->noLinkModules, ",{$module},") === false)
                {
                    $tab     = '';
                    $canView = common::hasPriv($module, $methodName);
                    if($action->objectType == 'meeting') $tab = $action->project ? "data-app='project'" : "data-app='my'";
                    if($module == 'requirement') $module = 'story';
                    $action->objectName = $canView ? html::a($this->createLink($module, $methodName, $params), $action->objectName, '_self', "title='{$action->objectName}' $tab") : "<span title='$action->objectName'>$action->objectName</span>";
                }
            }

            if(!empty($projectList[$action->project]))     $action->project   = $projectList[$action->project]->name          . ($projectList[$action->project]->deleted         ? "<span class='label danger ml-2'>{$this->lang->project->deleted}</span>" : '');
            if(!empty($productList[$action->objectID]))    $action->product   = $productList[$action->objectID]->productTitle . ($productList[$action->objectID]->productDeleted ? "<span class='label danger ml-2'>{$this->lang->story->deleted}</span>" : '');
            if(!empty($executionList[$action->execution])) $action->execution = $executionList[$action->execution]->name      . ($executionList[$action->execution]->deleted     ? "<span class='label danger ml-2'>{$this->lang->execution->deleted}</span>" : '');
        }

        $this->view->title               = $this->lang->action->trash;
        $this->view->trashes             = $trashes;
        $this->view->type                = $type;
        $this->view->currentObjectType   = $browseType;
        $this->view->orderBy             = $orderBy;
        $this->view->pager               = $pager;
        $this->view->users               = $this->loadModel('user')->getPairs('noletter');
        $this->view->preferredType       = $preferredType;
        $this->view->moreType            = $moreType;
        $this->view->preferredTypeConfig = $preferredTypeConfig;
        $this->view->byQuery             = $byQuery;
        $this->view->queryID             = $queryID;
        $this->display();
    }

    /**
     * 恢复一个回收站对象。
     * Undelete an object.
     *
     * @param  int    $actionID
     * @param  string $browseType
     * @param  string $confirmChange
     * @access public
     * @return void
     */
    public function undelete(int $actionID, string $browseType = 'all', string $confirmChange = 'no')
    {
        $oldAction = $this->action->getById($actionID);
        $extra     = $oldAction->extra == ACTIONMODEL::BE_HIDDEN ? 'hidden' : 'all';

        if(in_array($oldAction->objectType, array('program', 'project', 'execution', 'product')))
        {
            $object = new stdclass();
            $repeatObject = $this->action->getRepeatObject($oldAction, $object);
            if($repeatObject)
            {
                $table  = $oldAction->objectType == 'product' ? TABLE_PRODUCT : TABLE_PROJECT;

                $existNames = $this->action->getLikeObject($table, 'name', 'name', $repeatObject->name . '_%');
                for($i = 1; $i < 10000; $i ++)
                {
                    $replaceName = $repeatObject->name . '_' . $i;
                    if(!in_array($replaceName, $existNames)) break;
                }
                $replaceCode = '';
                if($object->code)
                {
                    $existCodes = $this->action->getLikeObject($table, 'code', 'code', $repeatObject->code . '_%');
                    for($i = 1; $i < 10000; $i ++)
                    {
                        $replaceCode = $repeatObject->code . '_' . $i;
                        if(!in_array($replaceCode, $existCodes)) break;
                    }
                }

                if($repeatObject->name == $object->name && $repeatObject->code && $repeatObject->code == $object->code)
                {
                    if($confirmChange == 'no')
                    {
                        $message = sprintf($this->lang->action->repeatChange, $this->lang->{$oldAction->objectType}->common, $replaceName, $replaceCode);
                        $url     = $this->createLink('action', 'undelete', "action={$actionID}&browseType={$browseType}&confirmChange=yes");
                        return $this->send(array('result' => 'fail', 'callback' => "zui.Modal.confirm({message: '{$message}', icon: 'icon-exclamation-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) => {if(res) $.ajaxSubmit({url: '{$url}'});     });"));
                    }
                    if($confirmChange == 'yes') $this->action->updateObjectByID($table, $oldAction->id, array('code' => $replaceCode, 'name' => $replaceName));
                }
                elseif($repeatObject->name == $object->name)
                {
                    if($confirmChange == 'no')
                    {
                        $message = sprintf($this->lang->action->nameRepeatChange, $this->lang->{$oldAction->objectType}->common, $replaceName);
                        $url     = $this->createLink('action', 'undelete', "action={$actionID}&browseType={$browseType}&confirmChange=yes");
                        return $this->send(array('result' => 'fail', 'callback' => "zui.Modal.confirm({message: '{$message}', icon: 'icon-exclamation-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) => {if(res) $.ajaxSubmit({url: '{$url}'});     });"));
                    }
                    if($confirmChange == 'yes') $this->action->updateObjectByID($table, $oldAction->id, array('name' => $replaceName));
                }
                elseif($repeatObject->code and $repeatObject->code == $object->code)
                {
                    if($confirmChange == 'no')
                    {
                        $message = sprintf($this->lang->action->codeRepeatChange, $this->lang->{$oldAction->objectType}->common, $replaceCode);
                        $url     = $this->createLink('action', 'undelete', "action={$actionID}&browseType={$browseType}&confirmChange=yes");
                        return $this->send(array('result' => 'fail', 'callback' => "zui.Modal.confirm({message: '{$message}', icon: 'icon-exclamation-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) => {if(res) $.ajaxSubmit({url: '{$url}'});     });"));
                    }
                    if($confirmChange == 'yes') $this->action->updateObjectByID($table, $oldAction->id, array('code' => $replaceCode));
                }
            }

            if($oldAction->objectType == 'execution')
            {
                $confirmLang = $this->restoreStages($oldAction, $browseType, $confirmChange);
                $url         = $this->createLink('action', 'undelete', "action={$actionID}&browseType={$browseType}&confirmChange=yes");
                if($confirmLang !== true) return $this->send(array('result' => 'fail', 'callback' => "zui.Modal.confirm({message: '{$confirmLang}', icon: 'icon-exclamation-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) => {if(res) $.ajaxSubmit({url: '{$url}'});     });"));
            }
        }

        $this->action->undelete($actionID);

        $sameTypeObjects = $this->action->getTrashes($oldAction->objectType, $extra, 'id_desc', null);
        $browseType      = ($sameTypeObjects and $browseType != 'all') ? $oldAction->objectType : 'all';

        return $this->send(array('result' => 'success', 'load' => $this->createLink('action', 'trash', "browseType=$browseType&type=$extra")));
    }

    /**
     * Hide an deleted object.
     *
     * @param  int    $actionID
     * @param  string $browseType
     * @access public
     * @return void
     */
    public function hideOne($actionID, $browseType = 'all')
    {
        $oldAction = $this->action->getById($actionID);

        $this->action->hideOne($actionID);

        $sameTypeObjects = $this->action->getTrashes($oldAction->objectType, 'all', 'id_desc', null);
        $browseType      = ($sameTypeObjects and $browseType != 'all') ? $oldAction->objectType : 'all';

        return $this->send(array('result' => 'success', 'load' => $this->createLink('action', 'trash', "browseType=$browseType")));
    }

    /**
     * Hide all deleted objects.
     *
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function hideAll($confirm = 'no')
    {
        if($confirm == 'no')
        {
            echo js::confirm($this->lang->action->confirmHideAll, inlink('hideAll', "confirm=yes"));
        }
        else
        {
            $this->action->hideAll();
            echo js::reload('parent');
        }
    }

    /**
     * Comment.
     *
     * @param  string $objectType
     * @param  int    $objectID
     * @access public
     * @return void
     */
    public function comment($objectType, $objectID)
    {
        if(strtolower($objectType) == 'task')
        {
            $task       = $this->loadModel('task')->getById($objectID);
            $executions = explode(',', $this->app->user->view->sprints);
            if(!in_array($task->execution, $executions)) return print(js::error($this->lang->error->accessDenied));
        }
        elseif(strtolower($objectType) == 'story')
        {
            $story      = $this->loadModel('story')->getById($objectID);
            $executions = explode(',', $this->app->user->view->sprints);
            $products   = explode(',', $this->app->user->view->products);
            if(!array_intersect(array_keys($story->executions), $executions) and !in_array($story->product, $products) and empty($story->lib)) return print(js::error($this->lang->error->accessDenied));
        }

        $actionID = $this->action->create($objectType, $objectID, 'Commented', $this->post->comment);
        if(defined('RUN_MODE') && RUN_MODE == 'api')
        {
            return $this->send(array('status' => 'success', 'data' => $actionID));
        }

        /* For new UI with ZIN. */
        return $this->send(array('status' => 'success', 'closeModal' => true, 'load' => true));
    }

    /**
     * Edit comment of a action.
     *
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function editComment($actionID)
    {
        if(strlen(trim(strip_tags($this->post->lastComment, '<img>'))) != 0)
        {
            $this->action->updateComment($actionID);
        }
        else
        {
            dao::$errors['submit'][] = $this->lang->action->historyEdit;
            return $this->send(array('result' => 'fail', 'message' => dao::getError()));
        }
        return $this->send(array('result' => 'success', 'locate' => 'reload'));
    }

    /**
     * Restore stages.
     *
     * @param  object $action
     * @param  string $browseType
     * @param  string $confirmChange
     * @access public
     * @return bool|string
     */
    public function restoreStages($action, $browseType, $confirmChange)
    {
        /* Check parent stage isCreateTask. */
        $execution      = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->eq($action->objectID)->fetch();
        $hasCreatedTask = $this->loadModel('programplan')->isCreateTask($execution->parent);
        if(!$hasCreatedTask) die(js::alert($this->lang->action->hasCreatedTask));

        /* Check type of siblings. */
        $siblings = $this->dao->select('DISTINCT type')->from(TABLE_EXECUTION)->where('deleted')->eq(0)->andWhere('parent')->eq($execution->parent)->fetchPairs('type');
        if($execution->type == 'stage' and (isset($siblings['sprint']) or isset($siblings['kanban']))) die(js::alert($this->lang->action->hasOtherType[$execution->type]));
        if(($execution->type == 'sprint' or $execution->type == 'kanban') and isset($siblings['stage'])) die(js::alert($this->lang->action->hasOtherType[$execution->type]));

        /* If parent stage is not exists, you should recover its parent stages, refresh status. */
        $stagePathList    = explode(',', trim($execution->path, ','));
        $deletedStageList = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->in($stagePathList)->andWhere('deleted')->eq(1)->andWhere('type')->eq('stage')->orderBy('id_asc')->fetchAll('id');
        $deletedParents   = $deletedStageList;
        array_pop($deletedParents);

        $deletedTitle = '';
        foreach($deletedParents as $deletedParent) $deletedTitle .= "'{$deletedParent->name}',";

        /* If parent stage's attribute has changed, sub-stage's attribute need change. */
        $deletedTopParent = current($deletedStageList);
        $checkTopStage    = $this->loadModel('programplan')->checkTopStage($deletedTopParent->id);
        $parentAttr       = $deletedTopParent->attribute;
        if(!$checkTopStage) $parentAttr = $this->dao->select('attribute')->from(TABLE_EXECUTION)->where('id')->eq($deletedTopParent->parent)->fetch('attribute');

        $needChangeAttr    = false;
        $startChangedStage = $execution;
        foreach($deletedStageList as $deletedStage)
        {
            if($parentAttr != 'mix' and $parentAttr != $deletedStage->attribute)
            {
                $startChangedStage = $deletedStage;
                $needChangeAttr    = true;
                break;
            }
            $parentAttr = $deletedStage->attribute;
        }

        /* Confirm. */
        if(!empty($deletedTitle) or $needChangeAttr)
        {
            $this->app->loadLang('stage');

            $deletedTitle = trim($deletedTitle, ',');
            $confirmLang  = sprintf($this->lang->action->hasDeletedParent, $deletedTitle) . $this->lang->action->whetherToRestore;
            if($needChangeAttr) $confirmLang = sprintf($this->lang->action->hasChangedAttr, zget($this->lang->stage->typeList, $parentAttr)) . $this->lang->action->whetherToRestore;
            if(!empty($deletedTitle) and $needChangeAttr) $confirmLang = sprintf($this->lang->action->hasDeletedParent, $deletedTitle) . sprintf($this->lang->action->hasChangedAttr, zget($this->lang->stage->typeList, $parentAttr)) . $this->lang->action->whetherToRestore;

            if($confirmChange == 'no')
            {
                return $confirmLang;
            }
            else
            {
                if(!empty($deletedTitle)) $this->action->restoreStages($deletedParents);
                if($needChangeAttr)
                {
                    $needChangedStages = substr($execution->path, strpos($execution->path, ",{$startChangedStage->id},"));
                    $needChangedStages = explode(',', trim($needChangedStages, ','));
                    $this->dao->update(TABLE_EXECUTION)->set('attribute')->eq($parentAttr)->where('id')->in($needChangedStages)->exec();
                }
            }
        }

        $this->programplan->computeProgress($startChangedStage->parent);

        return true;
    }
}
