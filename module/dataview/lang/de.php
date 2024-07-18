<?php
$lang->dataview->id     = 'ID';
$lang->dataview->name   = 'Name';
$lang->dataview->export = 'Export';

$lang->dataview->common         = 'Data Table';
$lang->dataview->id             = 'ID';
$lang->dataview->type           = 'Type';
$lang->dataview->name           = 'Name';
$lang->dataview->code           = 'Code';
$lang->dataview->group          = 'Group';
$lang->dataview->view           = 'View Name';
$lang->dataview->desc           = 'Description';
$lang->dataview->length         = 'length';
$lang->dataview->data           = 'Data';
$lang->dataview->schema         = 'Schema';
$lang->dataview->details        = 'Details';
$lang->dataview->fieldName      = 'Field Name';
$lang->dataview->fieldType      = 'Field Type';
$lang->dataview->create         = 'Create custom table';
$lang->dataview->browse         = 'Browse';
$lang->dataview->edit           = 'Edit';
$lang->dataview->design         = 'Design';
$lang->dataview->delete         = 'Delete';
$lang->dataview->createPriv     = 'Create Custom Table';
$lang->dataview->browsePriv     = 'Browse Data Table';
$lang->dataview->editPriv       = 'Edit Custom Table';
$lang->dataview->designPriv     = 'Design Custom Table';
$lang->dataview->deletePriv     = 'Delete Custom Table';
$lang->dataview->viewAction     = 'View Custom Table';
$lang->dataview->sql            = 'SQL Query';
$lang->dataview->sqlPlaceholder = 'Please enter a query statement. Only select query is supported.';
$lang->dataview->query          = 'Query';
$lang->dataview->add            = 'Add ';
$lang->dataview->sqlQuery       = 'SQL Query';
$lang->dataview->onlyOne        = 'Only allow one query';
$lang->dataview->empty          = 'Please enter a query statement';
$lang->dataview->allowSelect    = 'Only SELECT is allowed';
$lang->dataview->noStar         = "For performance, 'SELECT *' is not allowed";
$lang->dataview->fieldSettings  = "Field Settings";
$lang->dataview->queryFilters   = "Query Filters";
$lang->dataview->varSettings    = "Var Settings";
$lang->dataview->result         = "Query Result";
$lang->dataview->chart          = "Chart";
$lang->dataview->table          = "Table";
$lang->dataview->save           = "Save";
$lang->dataview->field          = "Field";
$lang->dataview->varError       = "Format of var is error";
$lang->dataview->time           = "Time";
$lang->dataview->confirmDelete  = 'Do you want to delete this custom table?';
$lang->dataview->manageGroup    = 'Manage group';
$lang->dataview->noModule       = '<div>You have no groups. </div><div>Manage Now</div>';
$lang->dataview->notSelect      = 'Please select a data table to dislay.';
$lang->dataview->existView      = "View exist.";
$lang->dataview->noQueryData    = "No Query Data";
$lang->dataview->builtIn        = 'Built-in Data Group';
$lang->dataview->default        = 'Default Group';
$lang->dataview->relatedTable   = 'Table';
$lang->dataview->relatedField   = 'Field';
$lang->dataview->multilingual   = 'After switching the system language, the corresponding name will be displayed';
$lang->dataview->duplicateField = 'Duplicate field names exist: <strong>%s</strong>. You are advised to: (1) Modify the <strong>*</strong> query to a specific field. (2) Use <strong>as</strong> to alias the field.';
$lang->dataview->errorField     = 'There is an illegal character in the current field <strong>%s</strong>. The [as] alias only supports a combination of Chinese, English, numbers, and underscores.';
$lang->dataview->queryFilterTip = 'Query filter is a way to implement dynamic query filtering by inserting variables into SQL. The result filter configured in the third step is to further filter the SQL query results.';
$lang->dataview->consumed       = 'Consumed hour';

$lang->dataview->varFilter = new stdclass();
$lang->dataview->varFilter->varCode     = 'Var Code';
$lang->dataview->varFilter->varLabel    = 'Var Label';
$lang->dataview->varFilter->default     = 'Default';
$lang->dataview->varFilter->requestType = 'Request Type';

$lang->dataview->varFilter->noticeVarName     = 'Var name cannot be empty';
$lang->dataview->varFilter->noticeRequestType = 'Request type cannot be empty';
$lang->dataview->varFilter->noticeShowName    = 'Show name cannot be empty';

$lang->dataview->varFilter->requestTypeList['input']    = 'Input';
$lang->dataview->varFilter->requestTypeList['date']     = 'Date';
$lang->dataview->varFilter->requestTypeList['datetime'] = 'Datetime';
$lang->dataview->varFilter->requestTypeList['select']   = 'Select';

$lang->dataview->varFilter->selectList['user']           = 'User List';
$lang->dataview->varFilter->selectList['product']        = $lang->productCommon . ' List';
$lang->dataview->varFilter->selectList['project']        = 'Project';
$lang->dataview->varFilter->selectList['execution']      = $lang->executionCommon . ' List';
$lang->dataview->varFilter->selectList['dept']           = 'Dept List';
$lang->dataview->varFilter->selectList['project.status'] = 'Project List';

$lang->dataview->objects = array();
$lang->dataview->objects['product']     = $lang->product->common;
$lang->dataview->objects['story']       = $lang->story->common;
$lang->dataview->objects['build']       = $lang->build->common;
$lang->dataview->objects['productplan'] = 'plan';
$lang->dataview->objects['release']     = $lang->release->common;
$lang->dataview->objects['bug']         = $lang->bug->common;
$lang->dataview->objects['project']     = $lang->project->common;
$lang->dataview->objects['task']        = $lang->task->common;
$lang->dataview->objects['team']        = 'team';
$lang->dataview->objects['user']        = 'user';
$lang->dataview->objects['execution']   = $lang->execution->common;
$lang->dataview->objects['testtask']    = 'testtask';
$lang->dataview->objects['testrun']     = 'testrun';
$lang->dataview->objects['testcase']    = 'testcase';
$lang->dataview->objects['testresult']  = 'result';
$lang->dataview->objects['casemodule']  = 'case module';
$lang->dataview->objects['action']      = 'Action';
$lang->dataview->objects['effort']      = 'Effort';
if($this->config->edition != 'open') $lang->dataview->objects['feedback'] = 'Feedback';

$lang->dataview->tables = array();
$lang->dataview->tables['build']       = array('name' => 'Build', 'desc' => '');
$lang->dataview->tables['product']     = array('name' => 'Product', 'desc' => '');
$lang->dataview->tables['productplan'] = array('name' => 'Product Plan', 'desc' => '');
$lang->dataview->tables['release']     = array('name' => 'Release', 'desc' => '');
$lang->dataview->tables['project']     = array('name' => 'Project', 'desc' => '');
$lang->dataview->tables['execution']   = array('name' => 'Execution',  'desc' => '');
$lang->dataview->tables['task']        = array('name' => 'Task',  'desc' => '');
//$lang->dataview->tables['team']        = array('name' => 'Team',  'desc' => '');
$lang->dataview->tables['bug']         = array('name' => 'Bug', 'desc' => 'pri, severity, project, product...');
$lang->dataview->tables['bugbuild']    = array('name' => 'Build Bug', 'desc' => 'pri, severity, project, build...');
$lang->dataview->tables['story']       = array('name' => 'Story', 'desc' => '');
$lang->dataview->tables['testcase']    = array('name' => 'Testcase', 'desc' => '');
$lang->dataview->tables['casestep']    = array('name' => 'Testcase step', 'desc' => '');
$lang->dataview->tables['testtask']    = array('name' => 'Testtask', 'desc' => '');
$lang->dataview->tables['testrun']     = array('name' => 'Test run', 'desc' => '');
$lang->dataview->tables['testresult']  = array('name' => 'Test result', 'desc' => '');

$lang->dataview->typeList = array();
$lang->dataview->typeList['view']  = 'Custom Table';
$lang->dataview->typeList['table'] = 'Origin Table';

$lang->dataview->fieldTypeList = array();
$lang->dataview->fieldTypeList['string'] = 'String';
$lang->dataview->fieldTypeList['option'] = 'Option';
$lang->dataview->fieldTypeList['date']   = 'Date';

$lang->dataview->groupList['my']        = 'Dashboard';
$lang->dataview->groupList['program']   = 'Program';
$lang->dataview->groupList['product']   = $lang->productCommon;
$lang->dataview->groupList['project']   = 'project';
$lang->dataview->groupList['execution'] = $lang->execution->common;
$lang->dataview->groupList['kanban']    = 'Kanban';
$lang->dataview->groupList['qa']        = 'Test';
$lang->dataview->groupList['feedback']  = 'Feedback';
$lang->dataview->groupList['doc']       = 'Doc';
$lang->dataview->groupList['assetlib']  = 'Assetlib';
$lang->dataview->groupList['report']    = 'Report';
$lang->dataview->groupList['company']   = 'Company';
$lang->dataview->groupList['repo']      = 'CI';
$lang->dataview->groupList['api']       = 'API';
$lang->dataview->groupList['message']   = 'Message';
$lang->dataview->groupList['search']    = 'Search';
$lang->dataview->groupList['admin']     = 'Admin';
$lang->dataview->groupList['system']    = 'System';
$lang->dataview->groupList['other']     = 'Others';

$lang->dataview->secondGroup['story']          = 'Story';
$lang->dataview->secondGroup['review']         = 'Review';
$lang->dataview->secondGroup['projectContent'] = 'Project content';
$lang->dataview->secondGroup['measure']        = 'Measure';
$lang->dataview->secondGroup['user']           = 'User';

$lang->dataview->error = new stdclass();
$lang->dataview->error->canNotDesign  = 'This table is already in use and cannot be redesigned.';
$lang->dataview->error->canNotDelete  = 'This table is already in use and cannot be deleted.';
$lang->dataview->error->warningDesign = 'The custom table has been referenced. Design will cause the chart, pivot, and screen to fail to be displayed. Do you want to continue?';
$lang->dataview->error->warningDelete = 'The custom table has been referenced. Delete will cause the chart, pivot, and screen to fail to be displayed. Do you want to continue?';

$lang->dataview->querying      = 'Querying, please wait...';
$lang->dataview->queryResult   = 'Query data in row %s x %s column';
$lang->dataview->viewResult    = '%s x %s rows of data';
$lang->dataview->recTotalTip   = '<strong> %s </strong> items in total';
$lang->dataview->recPerPageTip = "<strong> %s </strong> items per page <span class='caret'></span>";

$lang->dataview->projectStatusList['done']   = 'Done';
$lang->dataview->projectStatusList['cancel'] = 'Cancel';
$lang->dataview->projectStatusList['pause']  = 'Pause';
