<?php
declare(strict_types=1);
/**
 * The zen file of testreport module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     testreport
 * @link        https://www.zentao.net
 */
class testreportZen extends testreport
{
    /**
     * 为浏览页面获取报告
     * Get reports for browse.
     *
     * @param  int       $objectID
     * @param  string    $objectType
     * @param  int       $extra
     * @param  string    $orderBy
     * @param  int       $recTotal
     * @param  int       $recPerPage
     * @param  int       $pageID
     * @access protected
     * @return array
     */
    protected function getReportsForBrowse(int $objectID = 0, string $objectType = 'product', int $extra = 0, string $orderBy = 'id_desc', int $recTotal = 0, int $recPerPage = 20, int $pageID = 1): array
    {
        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $reports = $this->testreport->getList($objectID, $objectType, $extra, $orderBy, $pager);

        if(strpos('|project|execution|', $objectType) !== false && ($extra || isset($_POST['taskIdList'])))
        {
            $taskIdList = isset($_POST['taskIdList']) ? $_POST['taskIdList'] : array($extra);
            foreach($reports as $reportID => $report)
            {
                $tasks = explode(',', $report->tasks);
                if(count($tasks) != count($taskIdList) || array_diff($tasks, $taskIdList)) unset($reports[$reportID]);
            }
            $pager->setRecTotal(count($reports));
        }

        $this->view->pager = $pager;
        return $reports;
    }

    /**
     * 为创建获取任务键值。
     * Get task pairs for creation.
     *
     * @param  int       $objectID
     * @param  int       $extra
     * @access protected
     * @return array
     */
    protected function assignTaskParisForCreate(int $objectID = 0, int $extra = 0): array
    {
        if(!$objectID && $extra) $productID = $extra;
        if($objectID)
        {
            $task      = $this->testtask->getByID($objectID);
            $productID = $this->commonAction($task->product, 'product');
        }

        $taskPairs = array();
        $tasks     = $this->testtask->getProductTasks($productID, empty($objectID) ? 'all' : $task->branch, 'local,totalStatus', '', '', 'id_desc', null);
        foreach($tasks as $testTask)
        {
            if($testTask->build != 'trunk') $taskPairs[$testTask->id] = $testTask->name;
        }
        if(!$taskPairs) return $this->send(array('result' => 'fail', 'load' => array('confirm' => $this->lang->testreport->noTestTask, 'confirmed' => $this->createLink('testtask', 'create', "proudctID={$productID}"), 'canceled' => inlink('browse', "proudctID={$productID}"))));

        if(!$objectID)
        {
            $objectID  = key($taskPairs);
            $task      = $this->testtask->getByID($objectID);
            $productID = $this->commonAction($task->product, 'product');
        }

        $this->view->taskPairs = $taskPairs;
        $this->view->productID = $productID;
        return array($objectID, $task, $productID);
    }

    /**
     * 获取测试单的测试报告数据。
     * Get testtask report data.
     *
     * @param  int       $objectID
     * @param  string    $begin
     * @param  string    $end
     * @param  int       $productID
     * @param  object    $task
     * @param  string    $method
     * @access protected
     * @return array
     */
    protected function assignTesttaskReportData(int $objectID, string $begin = '', string $end = '', int $productID = 0, object $task = null, string $method = 'create'): array
    {
        $begin = !empty($begin) ? date("Y-m-d", strtotime($begin)) : $task->begin;
        $end   = !empty($end) ? date("Y-m-d", strtotime($end)) : $task->end;

        $productIdList[$productID] = $productID;

        $build  = $this->build->getById($task->build);
        $builds = !empty($build->id) ? array($build->id => $build) : array();
        $bugs   = $this->testreport->getBugs4Test($builds, $productID, $begin, $end);

        $tasks     = array($task->id => $task);
        $owner     = $task->owner;
        $stories   = empty($build->stories) ? array() : $this->story->getByList($build->stories);
        $execution = $this->execution->getById($task->execution);

        $this->setChartDatas($objectID);

        if($method == 'create')
        {
            if($this->app->tab == 'execution') $this->execution->setMenu($task->execution);
            if($this->app->tab == 'project') $this->project->setMenu($task->project);

            $this->view->title       = $task->name . $this->lang->testreport->create;
            $this->view->reportTitle = date('Y-m-d') . " TESTTASK#{$task->id} {$task->name} {$this->lang->testreport->common}";
        }

        $reportData = array('begin' => $begin, 'end' => $end, 'builds' => $builds, 'tasks' => $tasks, 'owner' => $owner, 'stories' => $stories, 'bugs' => $bugs, 'execution' => $execution, 'productIdList' => $productIdList);
        if($method == 'create') unset($reportData['owner']);
        return $reportData;
    }

    /**
     * 获取创建项目 / 执行的测试报告数据。
     * Get project or execution report data for creation.
     *
     * @param  int       $objectID
     * @param  string    $objectType
     * @param  int       $extra
     * @param  string    $begin
     * @param  string    $end
     * @param  int       $executionID
     * @access protected
     * @return array
     */
    protected function assignProjectReportDataForCreate(int $objectID, string $objectType, int $extra, string $begin = '', string $end = '', int $executionID = 0): array
    {
        $owners        = array();
        $buildIdList   = array();
        $productIdList = array();
        $tasks         = $this->testtask->getExecutionTasks($executionID, $objectType);
        foreach($tasks as $i => $task)
        {
            if($extra && strpos(",{$extra},", ",{$task->id},") === false)
            {
                unset($tasks[$i]);
                continue;
            }

            $owners[$task->owner] = $task->owner;
            $productIdList[$task->product] = $task->product;
            $this->setChartDatas($task->id);
            if($task->build != 'trunk') $buildIdList[$task->build] = $task->build;
        }

        $task      = $objectID ? $this->testtask->getByID($objectID) : key($tasks);
        $begin     = !empty($begin) ? date("Y-m-d", strtotime($begin)) : $task->begin;
        $end       = !empty($end) ? date("Y-m-d", strtotime($end)) : $task->end;
        $builds    = $this->build->getByList($buildIdList);
        $bugs      = $this->testreport->getBugs4Test($builds, $productIdList, $begin, $end, 'execution');
        $execution = $this->execution->getById($executionID);
        $stories   = !empty($builds) ? $this->testreport->getStories4Test($builds) : $this->story->getExecutionStories($execution->id);;
        $owner     = current($owners);

        if($this->app->tab == 'qa')
        {
            $productID = $this->product->checkAccess(key($productIdList), $this->products);
            $this->loadModel('qa')->setMenu($productID);
        }
        elseif($this->app->tab == 'project')
        {
            $projects  = $this->project->getPairsByProgram();
            $projectID = $this->project->checkAccess($execution->id, $projects);
            $this->project->setMenu($projectID);
        }

        $this->view->title       = $execution->name . $this->lang->testreport->create;
        $this->view->reportTitle = date('Y-m-d') . ' ' . strtoupper($objectType) . "#{$execution->id} {$execution->name} {$this->lang->testreport->common}";

        return array('begin' => $begin, 'end' => $end, 'builds' => $builds, 'tasks' => $tasks, 'owner' => $owner, 'stories' => $stories, 'bugs' => $bugs, 'execution' => $execution, 'productIdList' => $productIdList);
    }

    /**
     * 获取编辑项目 / 执行的测试报告数据。
     * Get project or execution report data for edit.
     *
     * @param  object    $report
     * @param  string    $begin
     * @param  string    $end
     * @access protected
     * @return array
     */
    protected function assignProjectReportDataForEdit(object $report, string $begin = '', string $end = ''): array
    {
        $begin = !empty($begin) ? date("Y-m-d", strtotime($begin)) : $report->begin;
        $end   = !empty($end) ? date("Y-m-d", strtotime($end)) : $report->end;

        $productIdList[$report->product] = $report->product;

        $tasks = $this->testtask->getByList($report->tasks);
        foreach($tasks as $task) $this->setChartDatas($task->id);

        $execution = $this->execution->getById($report->execution);
        $builds    = $this->build->getByList($report->builds);
        $stories   = !empty($builds) ? $this->testreport->getStories4Test($builds) : $this->story->getExecutionStories($report->execution);
        $bugs      = $this->testreport->getBugs4Test($builds, $productIdList, $begin, $end, 'execution');

        return array('begin' => $begin, 'end' => $end, 'builds' => $builds, 'tasks' => $tasks, 'stories' => $stories, 'bugs' => $bugs, 'execution' => $execution, 'productIdList' => $productIdList);
    }

    /**
     * 展示测试报告数据。
     * Assign report data.
     *
     * @param  array     $reportData
     * @param  string    $method
     * @access protected
     * @return void
     */
    protected function assignReportData(array $reportData, string $method, object $pager = null): void
    {
        foreach($reportData as $key => $value)
        {
            if(strpos(',productIdList,tasks,', ",{$key},") !== false)
            {
                $this->view->{$key} = join(',', array_keys($value));
            }
            else
            {
                $this->view->{$key} = $value;
            }
        }
        if($method == 'create')
        {
            $this->view->members = $this->dao->select('DISTINCT lastRunner')->from(TABLE_TESTRUN)->where('task')->in(array_keys($reportData['tasks']))->fetchPairs('lastRunner', 'lastRunner');
        }

        $this->view->storySummary = $this->product->summary($reportData['stories']);
        $this->view->users        = $this->user->getPairs('noletter|noclosed|nodeleted');

        $cases = $method != 'view' ? $this->testreport->getTaskCases($reportData['tasks'], $reportData['begin'], $reportData['end']) : $this->testreport->getTaskCases($reportData['tasks'], $reportData['begin'], $reportData['end'], $reportData['cases'], $pager);
        $this->view->cases        = $cases;
        $this->view->caseSummary  = $this->testreport->getResultSummary($reportData['tasks'], $cases, $reportData['begin'], $reportData['end']);

        $caseList = array();
        foreach($cases as $casesList)
        {
            foreach($casesList as $caseID => $case) $caseList[$caseID] = $case;
        }
        $this->view->caseList = $caseList;

        $caseIdList = isset($reportData['cases']) ? $reportData['cases'] : array_keys($caseList);
        $perCaseResult = $this->testreport->getPerCaseResult4Report($reportData['tasks'], $caseIdList, $reportData['begin'], $reportData['end']);
        $perCaseRunner = $this->testreport->getPerCaseRunner4Report($reportData['tasks'], $caseIdList, $reportData['begin'], $reportData['end']);
        $this->view->datas['testTaskPerRunResult'] = $this->loadModel('report')->computePercent($perCaseResult);
        $this->view->datas['testTaskPerRunner']    = $this->report->computePercent($perCaseRunner);

        list($bugInfo, $bugSummary) = $this->testreport->getBug4Report($reportData['tasks'], $reportData['productIdList'], $reportData['begin'], $reportData['end'], $reportData['builds']);
        if($method == 'view') $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);
        $this->view->bugInfo    = $bugInfo;
        $this->view->legacyBugs = $bugSummary['legacyBugs'];
        $this->view->bugSummary = $bugSummary;

        if($method == 'view') $this->view->pager = $pager;
    }

    /**
     * 为创建查看测试报告数据构建报告数据。
     * Build testreport data for view.
     *
     * @param  object    $report
     * @access protected
     * @return array
     */
    protected function buildReportDataForView(object $report): array
    {
        $reportData = array();
        $reportData['begin']         = $report->begin;
        $reportData['end']           = $report->end;
        $reportData['cases']         = $report->cases;
        $reportData['productIdList'] = array($report->product);
        $reportData['execution']     = $this->execution->getById($report->execution);
        $reportData['stories']       = $report->stories ? $this->story->getByList($report->stories)  : array();
        $reportData['tasks']         = $report->tasks   ? $this->testtask->getByList(explode(',', $report->tasks)) : array();
        $reportData['builds']        = $report->builds  ? $this->build->getByList($report->builds)   : array();
        $reportData['bugs']          = $report->bugs    ? $this->bug->getByIdList($report->bugs)     : array();
        $reportData['report']        = $report;
        return $reportData;

    }

    /**
     * 为创建准备测试报告数据。
     * Prepare testreport data for creation.
     *
     * @access protected
     * @return object
     */
    protected function prepareTestreportForCreate(): object
    {
        /* Build testreport. */
        $execution  = $this->execution->getByID((int)$this->post->execution);
        $testreport = form::data($this->config->testreport->form->create)
            ->setDefault('project', empty($execution) ? 0 : ($execution->type == 'project' ? $execution->id : $execution->project))
            ->get();
        $testreport = $this->loadModel('file')->processImgURL($testreport, $this->config->testreport->editor->create['id'], $this->post->uid);
        $testreport->members = trim($testreport->members, ',');

        /* Check reuqired. */
        $reportErrors = array();
        foreach(explode(',', $this->config->testreport->create->requiredFields) as $field)
        {
            $field = trim($field);
            if($field && empty($testreport->{$field}))
            {
                $fieldName = $this->config->testreport->form->showImport[$field]['type'] != 'array' ? "{$field}" : "{$field}[]";
                $reportErrors[$fieldName][] = sprintf($this->lang->error->notempty, $this->lang->testreport->{$field});
            }
         }
        if($testreport->end < $testreport->begin) $reportErrors['end'][] = sprintf($this->lang->error->ge, $this->lang->testreport->end, $testreport->begin);
        if(!empty($reportErrors)) dao::$errors = $reportErrors;

        return $testreport;
    }

    /**
     * 为编辑准备测试报告数据。
     * Prepare testreport data for edit.
     *
     * @param  int       $reportID
     * @access protected
     * @return object
     */
    protected function prepareTestreportForEdit(int $reportID): object
    {
        /* Build testreport. */
        $testreport = form::data($this->config->testreport->form->edit)->add('id', $reportID)->get();
        $testreport = $this->loadModel('file')->processImgURL($testreport, $this->config->testreport->editor->edit['id'], $this->post->uid);
        $testreport->members = trim($testreport->members, ',');

        /* Check reuqired. */
        $reportErrors = array();
        foreach(explode(',', $this->config->testreport->edit->requiredFields) as $field)
        {
            $field = trim($field);
            if($field && empty($testreport->{$field}))
            {
                $fieldName = $this->config->testreport->form->showImport[$field]['type'] != 'array' ? "{$field}" : "{$field}[]";
                $reportErrors[$fieldName][] = sprintf($this->lang->error->notempty, $this->lang->testreport->{$field});
            }
         }
        if($testreport->end < $testreport->begin) $reportErrors['end'][] = sprintf($this->lang->error->ge, $this->lang->testreport->end, $testreport->begin);
        if(!empty($reportErrors)) dao::$errors = $reportErrors;

        return $testreport;
    }
}

