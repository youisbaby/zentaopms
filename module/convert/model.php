<?php
declare(strict_types=1);
/**
 * The model file of convert module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     convert
 * @version     $Id: model.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        https://www.zentao.net
 */
?>
<?php
class convertModel extends model
{
    /**
     * 连接数据库。
     * Connect to db.
     *
     * @param  string $dbName
     * @access public
     * @return object|string
     */
    public function connectDB(string $dbName = ''): object|string
    {
        try
        {
            $params = clone $this->config->db;
            $params->name = $dbName;

            $dbh = new dbh($params);
            $dbh->exec("SET NAMES {$params->encoding}");
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->sourceDBH = $dbh;

            return $dbh;
        }
        catch (PDOException $exception)
        {
            return $exception->getMessage();
        }
    }

    /**
     * 检查数据库是否存在。
     * Check database exits or not.
     *
     * @param  string $dbName
     * @access public
     * @return object|false
     */
    public function dbExists(string $dbName = ''): object|false
    {
        if(!$this->checkDBName($dbName)) return false;

        return $this->dbh->execute('SHOW DATABASES like ?', array($dbName))->fetch();
    }

    /**
     * 检查数据表是否存在。
     * Check table exits or not.
     *
     * @param  string  $table
     * @access public
     * @return object|false
     */
    public function tableExists(string $table): object|false
    {
        $sql = "SHOW tables like '$table'";
        return $this->dbh->query($sql)->fetch();
    }

    /**
     * 检查jira数据库表是否存在。
     * Check table of jira databases exits or not.
     *
     * @param  string  $dbName
     * @param  string  $table
     * @access public
     * @return object|false
     */
    public function tableExistsOfJira(string $dbName, string $table): object|false
    {
        $this->connectDB($dbName);
        $sql = "SHOW tables like '$table'";
        return $this->dao->dbh($this->sourceDBH)->query($sql)->fetch();
    }

    /**
     * 保存每个表的最大ID。
     * Save the max id of every table. Thus when we convert again, when can delete id larger then the saved max id.
     *
     * @access public
     * @return void
     */
    public function saveState()
    {
        /* Get user defined tables. */
        $constants     = get_defined_constants(true);
        $userConstants = $constants['user'];

        /* These tables needn't save. */
        unset($userConstants['TABLE_BURN']);
        unset($userConstants['TABLE_GROUPPRIV']);
        unset($userConstants['TABLE_PROJECTPRODUCT']);
        unset($userConstants['TABLE_PROJECTSTORY']);
        unset($userConstants['TABLE_STORYSPEC']);
        unset($userConstants['TABLE_TEAM']);
        unset($userConstants['TABLE_USERGROUP']);
        unset($userConstants['TABLE_STORYSTAGE']);
        unset($userConstants['TABLE_SEARCHDICT']);

        /* Get max id of every table. */
        foreach($userConstants as $key => $value)
        {
            if(strpos($key, 'TABLE') === false) continue;
            if($key == 'TABLE_COMPANY') continue;
            $state[$value] = (int)$this->dao->select('MAX(id) AS id')->from($value)->fetch('id');
        }
        $this->session->set('state', $state);
    }

    /**
     * 从数据库获取jira数据。
     * Get jira data from db.
     *
     * @param  string $module
     * @param  int    $lastID
     * @param  int    $limit
     * @access public
     * @return array
     */
    public function getJiraDataFromDB(string $module = '', int $lastID = 0, int $limit = 0): array
    {
        $dataList = array();
        $table    = zget($this->config->convert->objectTables, $module, '');
        if($module == 'user')
        {
            $dataList = $this->dao->dbh($this->sourceDBH)->select('t1.`ID`, t1.`lower_user_name` as account, t1.`lower_display_name` as realname, t1.`lower_email_address` as email, t1.created_date as `join`, t2.user_key as userCode')->from(JIRA_USERINFO)->alias('t1')
                ->leftJoin(JIRA_USER)->alias('t2')->on('t1.`lower_user_name` = t2.`lower_user_name`')
                ->where('1 = 1')
                ->beginIF($lastID)->andWhere('t1.ID')->gt($lastID)->fi()
                ->orderBy('t1.ID asc')->limit($limit)
                ->fetchAll('ID');
        }
        elseif(!empty($table))
        {
            $dataList = $this->dao->dbh($this->sourceDBH)->select('*')->from($table)
                ->where('1 = 1')
                ->beginIF($lastID)->andWhere('ID')->gt($lastID)->fi()
                ->orderBy('ID asc')->limit($limit)
                ->fetchAll('ID');
        }

        return $dataList;
    }

    /**
     * 从文件中获取jira数据。
     * Get jira data from file.
     *
     * @param  string $module
     * @param  int    $lastID
     * @param  int    $limit
     * @access public
     * @return array
     */
    public function getJiraDataFromFile(string $module, int $lastID = 0, int $limit = 0): array
    {
        $fileName = $module;
        if($module == 'build') $fileName = 'version';
        if($module == 'file')  $fileName = 'fileattachment';

        $filePath = $this->app->getTmpRoot() . 'jirafile/' . $fileName . '.xml';
        if(!file_exists($filePath)) return array();

        $xmlContent = file_get_contents($filePath);
        $xmlContent = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $xmlContent);
        $parsedXML  = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);

        $dataList  = array();
        $parsedXML = $this->convertTao->object2Array($parsedXML);
        foreach($parsedXML as $key => $xmlArray)
        {
            if(strtolower($key) != strtolower($fileName)) continue;
            foreach($xmlArray as $key => $attributes)
            {
                $desc    = isset($attributes['description']) ? $attributes['description'] : '';
                $summary = isset($attributes['summary']) ? $attributes['summary'] : '';
                $body    = isset($attributes['body']) ? $attributes['body'] : '';

                if(is_numeric($key))
                {
                    foreach($attributes as $value)
                    {
                        if(!is_array($value)) continue;
                        if(!empty($desc))    $value['description'] = $desc;
                        if(!empty($summary)) $value['summary']     = $summary;
                        if(!empty($body))    $value['body']        = $body;
                        $dataList[$value['id']] = $value;
                    }
                }
                else
                {
                    $dataList[$attributes['id']] = $attributes;
                }
            }
        }

        if($limit)
        {
            $dataList = array_slice($dataList, $lastID, $limit, true);
            if(empty($dataList)) return array();
        }

        foreach($dataList as $key => $data)
        {
            $buildFunction  = 'build' . ucfirst($module) . 'Data';
            $dataList[$key] = $this->convertTao->$buildFunction($data);
        }

        return $dataList;
    }

    /**
     * 从jira文件中获取版本信息。
     * Get version group from jira file.
     *
     * @access public
     * @return array
     */
    public function getVersionGroup(): array
    {
        $xmlContent = file_get_contents($this->app->getTmpRoot() . 'jirafile/nodeassociation.xml');
        $xmlContent = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $xmlContent);
        $parsedXML  = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);

        $dataList  = array();
        $parsedXML = $this->convertTao->object2Array($parsedXML);
        foreach($parsedXML as $key => $xmlArray)
        {
            if(strtolower($key) != 'nodeassociation') continue;
            foreach($xmlArray as $key => $attributes)
            {
                foreach($attributes as $value)
                {
                    if(!is_array($value)) continue;
                    if($value['sinkNodeEntity'] != 'Version') continue;
                    $dataList[$value['sinkNodeId']][] = $value['sinkNodeId'];
                    $dataList[$value['sinkNodeId']][] = $value['sourceNodeId'];
                }
            }
        }

        return $dataList;
    }

    /**
     * 从DB文件中导入jira数据。
     * Import jira from db.
     *
     * @param  string $type user|project|issue|build|issuelink|action|file
     * @param  int    $lastID
     * @param  bool   $createTable
     * @access public
     * @return array
     */
    public function importJiraFromDB(string $type = '', int $lastID = 0, bool $createTable = false): array
    {
        if($createTable) $this->createTmpTable4Jira();

        $this->connectDB($this->session->jiraDB);

        $limit = 1000;
        $nextObject = false;
        if(empty($type)) $type = key($this->lang->convert->jira->objectList);

        foreach(array_keys($this->lang->convert->jira->objectList) as $module)
        {
            if($module != $type and !$nextObject) continue;
            if($module == $type) $nextObject = true;

            while(true)
            {
                $dataList = $this->getJiraDataFromDB($module, $lastID, $limit);

                if(empty($dataList))
                {
                    $lastID = 0;
                    break;
                }

                if($module == 'user')      $this->convertTao->importJiraUser($dataList);
                if($module == 'project')   $this->convertTao->importJiraProject($dataList);
                if($module == 'issue')     $this->convertTao->importJiraIssue($dataList);
                if($module == 'build')     $this->convertTao->importJiraBuild($dataList);
                if($module == 'issuelink') $this->convertTao->importJiraIssueLink($dataList);
                if($module == 'action')    $this->importJiraAction($dataList);
                if($module == 'file')      $this->importJiraFile($dataList);

                return array('type' => $module, 'count' => count($dataList), 'lastID' => max(array_keys($dataList)));
            }
        }

        $this->afterExec();
        return array('finished' => true);
    }

    /**
     * 从文件中导入jira数据。
     * Import jira from file.
     *
     * @param  string  $type user|project|issue|build|issuelink|action|file
     * @param  int     $lastID
     * @param  bool    $createTable
     * @access public
     * @return array
     */
    public function importJiraFromFile(string $type = '', int $lastID = 0, bool $createTable = false): array
    {
        if($createTable) $this->createTmpTable4Jira();

        $limit = 1000;
        $nextObject = false;
        if(empty($type)) $type = key($this->lang->convert->jira->objectList);

        foreach(array_keys($this->lang->convert->jira->objectList) as $module)
        {
            if($module != $type and !$nextObject) continue;
            if($module == $type) $nextObject = true;

            while(true)
            {
                $dataList = $this->getJiraDataFromFile($module, $lastID, $limit);

                if(empty($dataList))
                {
                    $lastID = 0;
                    break;
                }

                if($module == 'user')      $this->convertTao->importJiraUser($dataList);
                if($module == 'project')   $this->convertTao->importJiraProject($dataList, 'file');
                if($module == 'issue')     $this->convertTao->importJiraIssue($dataList, 'file');
                if($module == 'build')     $this->convertTao->importJiraBuild($dataList, 'file');
                if($module == 'issuelink') $this->convertTao->importJiraIssueLink($dataList, 'file');
                if($module == 'action')    $this->importJiraAction($dataList, 'file');
                if($module == 'file')      $this->importJiraFile($dataList, 'file');

                $offset = $lastID + $limit;
                return array('type' => $module, 'count' => count($dataList), 'lastID' => $offset);
            }
        }

        $this->afterExec('file');
        return array('finished' => true);
    }

    /**
     * Import jira action.
     *
     * @param  object $dataList
     * @param  string $method
     * @access public
     * @return void
     */
    public function importJiraAction($dataList, $method = 'db')
    {
        $issueStories = $this->dao->dbh($this->dbh)->select('AID,BID')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jstory')
            ->andWhere('BType')->eq('zstory')
            ->fetchPairs();

        $issueTasks = $this->dao->dbh($this->dbh)->select('AID,BID')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jtask')
            ->andWhere('BType')->eq('ztask')
            ->fetchPairs();

        $issueBugs = $this->dao->dbh($this->dbh)->select('AID,BID')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jbug')
            ->andWhere('BType')->eq('zbug')
            ->fetchPairs();

        $issueObjectType = $this->dao->dbh($this->dbh)->select('AID,extra')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jissueid')
            ->andWhere('BType')->eq('zissuetype')
            ->fetchPairs();

        $projectRelation = $this->dao->dbh($this->dbh)->select('AID,BID')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jproject')
            ->andWhere('BType')->eq('zproject')
            ->fetchPairs();

        $projectProduct = $this->dao->dbh($this->dbh)->select('project,product')->from(TABLE_PROJECTPRODUCT)
            ->where('project')->in(array_values($projectRelation))
            ->fetchPairs();

        foreach($dataList as $data)
        {
            $action = new stdclass();

            $issueID = $data->issueid;
            $comment = $data->actionbody;
            if(empty($comment)) continue;

            if(!isset($issueObjectType[$issueID])) continue;

            $objectType = $issueObjectType[$issueID];
            if($objectType == 'task')  $objectID = $issueTasks[$issueID];
            if($objectType == 'bug')   $objectID = $issueBugs[$issueID];
            if($objectType == 'story') $objectID = $issueStories[$issueID];

            if(empty($objectID)) continue;

            $action = new stdclass();
            $action->objectType = $objectType;
            $action->objectID   = $objectID;
            $action->actor      = $this->getJiraAccount($data->AUTHOR, $method);
            $action->action     = 'commented';
            $action->date       = substr($data->CREATED, 0, 19);
            $action->comment    = $comment;
            $this->dao->dbh($this->dbh)->insert(TABLE_ACTION)->data($action)->exec();
        }
    }

    /**
     * Import jira file.
     *
     * @param  object $dataList
     * @param  string $method
     * @access public
     * @return void
     */
    public function importJiraFile($dataList, $method = 'db')
    {
        $this->loadModel('file');

        $issueObjectType = $this->dao->dbh($this->dbh)->select('AID,extra')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jissueid')
            ->andWhere('BType')->eq('zissuetype')
            ->fetchPairs();

        $issueStories = $this->dao->dbh($this->dbh)->select('AID,BID')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jstory')
            ->andWhere('BType')->eq('zstory')
            ->fetchPairs();

        $issueTasks = $this->dao->dbh($this->dbh)->select('AID,BID')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jtask')
            ->andWhere('BType')->eq('ztask')
            ->fetchPairs();

        $issueBugs = $this->dao->dbh($this->dbh)->select('AID,BID')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jbug')
            ->andWhere('BType')->eq('zbug')
            ->fetchPairs();

        $filePaths = $this->dao->dbh($this->dbh)->select('AID,extra')->from(JIRA_TMPRELATION)
            ->where('AType')->eq('jissueid')
            ->andWhere('BType')->eq('jfilepath')
            ->fetchPairs();

        foreach($dataList as $fileAttachment)
        {
            $issueID    = $fileAttachment->issueid;
            if(!isset($issueObjectType[$issueID])) continue;

            $objectType = $issueObjectType[$issueID];
            if($objectType != 'bug' and $objectType != 'task' and $objectType != 'story') continue;

            $fileID     = $fileAttachment->ID;
            $fileName   = $fileAttachment->FILENAME;
            list($mime, $extension) = explode('/', $fileAttachment->MIMETYPE);

            if($objectType == 'bug')   $objectID = $issueBugs[$issueID];
            if($objectType == 'task')  $objectID = $issueTasks[$issueID];
            if($objectType == 'story') $objectID = $issueStories[$issueID];
            if(empty($objectID)) continue;

            $file = new stdclass();
            $file->pathname   = $this->file->setPathName($fileID, $extension);
            $file->title      = str_ireplace(".{$extension}", '', $fileName);
            $file->extension  = $extension;
            $file->size       = $fileAttachment->FILESIZE;
            $file->objectType = $objectType;
            $file->objectID   = $objectID;
            $file->addedBy    = $this->getJiraAccount($fileAttachment->AUTHOR, $method);
            $file->addedDate  = substr($fileAttachment->CREATED, 0, 19);
            $this->dao->dbh($this->dbh)->insert(TABLE_FILE)->data($file)->exec();

            $jiraFile = $this->app->getTmpRoot() . 'attachments/' . $filePaths[$issueID] .  $fileID;
            if(is_file($jiraFile)) copy($jiraFile, $this->file->savePath . $file->pathname);
        }
    }

    /**
     * Split jira file.
     *
     * @access public
     * @return void
     */
    public function splitFile()
    {
        $filePath = $this->app->getTmpRoot() . 'jirafile/';
        $fileName = 'entities.xml';
        $file     = $filePath . $fileName;
        $handle   = fopen($file, "r");

        $usingData  = array();
        $headerList = array('<Action', '<Project', '<Status', '<Resolution', '<User', '<Issue', '<ChangeGroup', '<ChangeItem', '<IssueLink', '<IssueLinkType', '<FileAttachment', '<Version', '<IssueType', '<NodeAssociation', '<ApplicationUser');
        $footerList = array('<Action' => '</Action>', '<Project' => '</Project>', '<Status' => '</Status>', '<Resolution' => '</Resolution>', '<User' => '</User>', '<Issue' => '</Issue>', '<ChangeGroup' => '</ChangeGroup>', '<ChangeItem' => '</ChangeItem>', '<IssueLink' => '</IssueLink>', '<IssueLinkType' => '</IssueLinkType>', '<FileAttachment' => '</FileattAchment>', '<Version' => '</Version>', '<IssueType' => '</IssueType>', '<NodeAssociation' => '</NodeAssociation>', '<ApplicationUser' => '</ApplicationUser>');

        while(!feof($handle))
        {
            $itemStr = fgets($handle);
            foreach($headerList as $object)
            {
                $itemName  = $object;
                $itemName .= ' ';

                if(strpos($itemStr, $itemName) === false) continue;

                if(strpos($itemStr, '/>') === false)
                {
                    $end = $footerList[$object];
                    while(true)
                    {
                        $followItemStr = fgets($handle);
                        $itemStr      .= $followItemStr;
                        if(strpos($itemStr, $end) !== false) break;
                    }
                }

                $object = str_replace('<', '', $object);
                $object = strtolower($object);
                $data   = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $itemStr);
                if(!file_exists($filePath . $object . '.xml')) $data = "<?xml version='1.0' encoding='UTF-8'?>\n  <entity-engine-xml>\n" . $data;
                file_put_contents($filePath . $object . '.xml', $data, FILE_APPEND);
            }
        }

        foreach($headerList as $object)
        {
            $object   = str_replace('<', '', $object);
            $object   = strtolower($object);
            $filename = $filePath . $object . '.xml';
            if(file_exists($filename)) file_put_contents($filename, '</entity-engine-xml>', FILE_APPEND);
        }

        fclose($handle);
    }

    /**
     * Create tmp table for import jira.
     *
     * @access public
     * @return void
     */
    public function createTmpTable4Jira()
    {
$sql = <<<EOT
CREATE TABLE `jiratmprelation`(
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `AType` char(30) NOT NULL,
  `AID` char(30) NOT NULL,
  `BType` char(30) NOT NULL,
  `BID` char(30) NOT NULL,
  `extra` char(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `relation` (`AType`,`BType`,`AID`,`BID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOT;

        try
        {
            $this->dbh->exec($sql);
        }
        catch(Exception $e){}
    }

    /**
     * After exec.
     *
     * @param  string $method
     * @access public
     * @return void
     */
    public function afterExec($method = 'db')
    {
        /* Set project min start date. */
        $minDate            = date('Y-m-d', time() - 30 * 24 * 3600);
        $executionProject   = $this->dao->dbh($this->dbh)->select('id,project')->from(TABLE_PROJECT)->where('type')->eq('sprint')->andWhere('project')->ne(0)->fetchPairs();
        $minOpenedDatePairs = $this->dao->dbh($this->dbh)->select('execution,min(openedDate) as minOpenedDate')->from(TABLE_TASK)->where('execution')->in(array_keys($executionProject))->fetchPairs('execution', 'minOpenedDate');

        foreach($executionProject  as $executionID => $projectID)
        {
            $minOpenedDate = isset($minOpenedDatePairs[$executionID]) ? $minOpenedDatePairs[$executionID] : $minDate;
            $minOpenedDate = substr($minOpenedDate, 0, 11);
            $minOpenedDate = helper::isZeroDate($minOpenedDate) ? $minDate : $minOpenedDate;
            $this->dao->update(TABLE_PROJECT)->set('begin')->eq($minOpenedDate)->where('id')->eq($projectID)->orWhere('id')->eq($executionID)->exec();
        }

        if($method == 'file') $this->deleteJiraFile();

        $this->dbh->exec("DROP TABLE" . JIRA_TMPRELATION);
    }

    /**
     * Delete jira backip file.
     *
     * @access public
     * @return void
     */
    public function deleteJiraFile()
    {
        $fileList = array('action', 'project', 'status', 'resolution', 'user', 'issue', 'changegroup', 'changeitem', 'issuelink', 'issuelinktype', 'fileattachment', 'version', 'issuetype', 'nodeassociation', 'applicationuser');
        foreach($fileList as $fileName)
        {
            $filePath = $this->app->getTmpRoot() . 'jirafile/' . $fileName . '.xml';
            if(file_exists($filePath)) @unlink($filePath);
        }
    }

    /**
     * Check dbName is valide.
     *
     * @param  string $dbName
     * @access public
     * @return bool
     */
    public function checkDBName($dbName)
    {
        if(preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $dbName)) return true;
        return false;
    }
}
