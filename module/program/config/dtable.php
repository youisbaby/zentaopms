<?php
global $lang;
$config->program->dtable = new stdclass();

$config->program->dtable->fieldList['name']['name']         = 'name';
$config->program->dtable->fieldList['name']['title']        = $lang->nameAB;
$config->program->dtable->fieldList['name']['width']        = 356;
$config->program->dtable->fieldList['name']['type']         = 'link';
$config->program->dtable->fieldList['name']['flex']         = 1;
$config->program->dtable->fieldList['name']['nestedToggle'] = true;
$config->program->dtable->fieldList['name']['checkbox']     = true;
$config->program->dtable->fieldList['name']['iconRender']   = true;
$config->program->dtable->fieldList['name']['sortType']     = false;

$config->program->dtable->fieldList['status']['name']      = 'status';
$config->program->dtable->fieldList['status']['title']     = $lang->program->status;
$config->program->dtable->fieldList['status']['minWidth']  = 60;
$config->program->dtable->fieldList['status']['type']      = 'status';
$config->program->dtable->fieldList['status']['sortType']  = true;
$config->program->dtable->fieldList['status']['statusMap'] = $lang->program->statusList;

$config->program->dtable->fieldList['PM']['name']     = 'PM';
$config->program->dtable->fieldList['PM']['title']    = $lang->program->PM;
$config->program->dtable->fieldList['PM']['minWidth'] = 100;
$config->program->dtable->fieldList['PM']['type']     = 'avatarBtn';
$config->program->dtable->fieldList['PM']['sortType'] = true;

$config->program->dtable->fieldList['budget']['name']     = 'budget';
$config->program->dtable->fieldList['budget']['title']    = $lang->program->budget;
$config->program->dtable->fieldList['budget']['minWidth'] = 70;
$config->program->dtable->fieldList['budget']['type']     = 'format';
$config->program->dtable->fieldList['budget']['sortType'] = true;

$config->program->dtable->fieldList['begin']['name']     = 'begin';
$config->program->dtable->fieldList['begin']['title']    = $lang->program->begin;
$config->program->dtable->fieldList['begin']['minWidth'] = 90;
$config->program->dtable->fieldList['begin']['type']     = 'datetime';
$config->program->dtable->fieldList['begin']['sortType'] = true;

$config->program->dtable->fieldList['end']['name']     = 'end';
$config->program->dtable->fieldList['end']['title']    = $lang->program->end;
$config->program->dtable->fieldList['end']['minWidth'] = 90;
$config->program->dtable->fieldList['end']['type']     = 'datetime';
$config->program->dtable->fieldList['end']['sortType'] = true;

$config->program->dtable->fieldList['progress']['name']     = 'progress';
$config->program->dtable->fieldList['progress']['title']    = $lang->program->progressAB;
$config->program->dtable->fieldList['progress']['minWidth'] = 100;
$config->program->dtable->fieldList['progress']['type']     = 'circleProgress';

$config->program->dtable->fieldList['actions']['name']   = 'actions';
$config->program->dtable->fieldList['actions']['title']  = $lang->actions;
$config->program->dtable->fieldList['actions']['width']  = 160;
$config->program->dtable->fieldList['actions']['type']   = 'actions';
$config->program->dtable->fieldList['actions']['fixed']  = 'right';
$config->program->dtable->fieldList['actions']['module'] = 'program';

/* DataTable fields of Product View. */
$config->program->productview = new stdClass();
$config->program->productview->dtable = new stdClass();
$config->program->productview->dtable->fieldList = array();

$config->program->productview->dtable->fieldList['name']['name']         = 'name';
$config->program->productview->dtable->fieldList['name']['title']        = $lang->nameAB;
$config->program->productview->dtable->fieldList['name']['type']         = 'title';
$config->program->productview->dtable->fieldList['name']['link']         = array('module' => 'product', 'method' => 'browse', 'params' => 'productID={id}');
$config->program->productview->dtable->fieldList['name']['nestedToggle'] = true;
$config->program->productview->dtable->fieldList['name']['checkbox']     = true;
$config->program->productview->dtable->fieldList['name']['show']         = true;
$config->program->productview->dtable->fieldList['name']['sortType']     = true;
$config->program->productview->dtable->fieldList['name']['group']        = 'g1';

$config->program->productview->dtable->fieldList['PM']['name']  = 'PM';
$config->program->productview->dtable->fieldList['PM']['title'] = $lang->program->PM;
$config->program->productview->dtable->fieldList['PM']['type']  = 'avatarBtn';
$config->program->productview->dtable->fieldList['PM']['show']  = true;
$config->program->productview->dtable->fieldList['PM']['group'] = 'g2';

$config->program->productview->dtable->fieldList['createdDate']['name']     = 'createdDate';
$config->program->productview->dtable->fieldList['createdDate']['title']    = $lang->program->createdDate;
$config->program->productview->dtable->fieldList['createdDate']['type']     = 'datetime';
$config->program->productview->dtable->fieldList['createdDate']['sortType'] = true;
$config->program->productview->dtable->fieldList['createdDate']['group']    = 'g3';

$config->program->productview->dtable->fieldList['createdBy']['name']  = 'createdBy';
$config->program->productview->dtable->fieldList['createdBy']['title'] = $lang->openedByAB;
$config->program->productview->dtable->fieldList['createdBy']['type']  = 'user';
$config->program->productview->dtable->fieldList['createdBy']['group'] = 'g3';

$config->program->productview->dtable->fieldList['totalUnclosedStories']['name']     = 'totalUnclosedStories';
$config->program->productview->dtable->fieldList['totalUnclosedStories']['title']    = $lang->program->totalUnclosedStories;
$config->program->productview->dtable->fieldList['totalUnclosedStories']['minWidth'] = 100;
$config->program->productview->dtable->fieldList['totalUnclosedStories']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalUnclosedStories']['show']     = true;
$config->program->productview->dtable->fieldList['totalUnclosedStories']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalUnclosedStories']['group']    = 'g4';

$config->program->productview->dtable->fieldList['totalStories']['name']     = 'totalStories';
$config->program->productview->dtable->fieldList['totalStories']['title']    = $lang->program->totalStories;
$config->program->productview->dtable->fieldList['totalStories']['minWidth'] = 100;
$config->program->productview->dtable->fieldList['totalStories']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalStories']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalStories']['group']    = 'g4';

$config->program->productview->dtable->fieldList['closedStoryRate']['name']     = 'closedStoryRate';
$config->program->productview->dtable->fieldList['closedStoryRate']['title']    = $lang->program->closedStoryRate;
$config->program->productview->dtable->fieldList['closedStoryRate']['minWidth'] = 100;
$config->program->productview->dtable->fieldList['closedStoryRate']['type']     = 'progress';
$config->program->productview->dtable->fieldList['closedStoryRate']['show']     = true;
$config->program->productview->dtable->fieldList['closedStoryRate']['sortType'] = true;
$config->program->productview->dtable->fieldList['closedStoryRate']['group']    = 'g4';

$config->program->productview->dtable->fieldList['totalPlans']['name']     = 'totalPlans';
$config->program->productview->dtable->fieldList['totalPlans']['title']    = $lang->productplan->shortCommon;
$config->program->productview->dtable->fieldList['totalPlans']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalPlans']['show']     = true;
$config->program->productview->dtable->fieldList['totalPlans']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalPlans']['group']    = 'g5';

$config->program->productview->dtable->fieldList['totalProjects']['name']     = 'totalProjects';
$config->program->productview->dtable->fieldList['totalProjects']['title']    = $lang->program->project;
$config->program->productview->dtable->fieldList['totalProjects']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalProjects']['link']     = array('module' => 'product', 'method' => 'project', 'params' => 'status=all&&productID={id}');
$config->program->productview->dtable->fieldList['totalProjects']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalProjects']['group']    = 'g5';

$config->program->productview->dtable->fieldList['totalExecutions']['name']     = 'totalExecutions';
$config->program->productview->dtable->fieldList['totalExecutions']['title']    = $lang->execution->common;
$config->program->productview->dtable->fieldList['totalExecutions']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalExecutions']['show']     = true;
$config->program->productview->dtable->fieldList['totalExecutions']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalExecutions']['group']    = 'g5';

$config->program->productview->dtable->fieldList['testCaseCoverage']['name']     = 'testCaseCoverage';
$config->program->productview->dtable->fieldList['testCaseCoverage']['title']    = $lang->program->testCaseCoverage;
$config->program->productview->dtable->fieldList['testCaseCoverage']['minWidth'] = 100;
$config->program->productview->dtable->fieldList['testCaseCoverage']['type']     = 'progress';
$config->program->productview->dtable->fieldList['testCaseCoverage']['show']     = true;
$config->program->productview->dtable->fieldList['testCaseCoverage']['sortType'] = true;
$config->program->productview->dtable->fieldList['testCaseCoverage']['group']    = 'g6';

$config->program->productview->dtable->fieldList['totalActivatedBugs']['name']     = 'totalActivatedBugs';
$config->program->productview->dtable->fieldList['totalActivatedBugs']['title']    = $lang->program->totalActivatedBugs;
$config->program->productview->dtable->fieldList['totalActivatedBugs']['minWidth'] = 86;
$config->program->productview->dtable->fieldList['totalActivatedBugs']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalActivatedBugs']['show']     = true;
$config->program->productview->dtable->fieldList['totalActivatedBugs']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalActivatedBugs']['group']    = 'g7';

$config->program->productview->dtable->fieldList['totalBugs']['name']     = 'totalBugs';
$config->program->productview->dtable->fieldList['totalBugs']['title']    = $lang->program->totalBugs;
$config->program->productview->dtable->fieldList['totalBugs']['minWidth'] = 86;
$config->program->productview->dtable->fieldList['totalBugs']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalBugs']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalBugs']['group']    = 'g7';

$config->program->productview->dtable->fieldList['fixedRate']['name']     = 'fixedRate';
$config->program->productview->dtable->fieldList['fixedRate']['title']    = $lang->program->fixedRate;
$config->program->productview->dtable->fieldList['fixedRate']['minWidth'] = 80;
$config->program->productview->dtable->fieldList['fixedRate']['type']     = 'progress';
$config->program->productview->dtable->fieldList['fixedRate']['show']     = true;
$config->program->productview->dtable->fieldList['fixedRate']['sortType'] = true;
$config->program->productview->dtable->fieldList['fixedRate']['group']    = 'g7';

$config->program->productview->dtable->fieldList['totalReleases']['name']     = 'totalReleases';
$config->program->productview->dtable->fieldList['totalReleases']['title']    = $lang->release->common;
$config->program->productview->dtable->fieldList['totalReleases']['type']     = 'number';
$config->program->productview->dtable->fieldList['totalReleases']['show']     = true;
$config->program->productview->dtable->fieldList['totalReleases']['sortType'] = true;
$config->program->productview->dtable->fieldList['totalReleases']['group']    = 'g8';

$config->program->productview->dtable->fieldList['latestReleaseDate']['name']     = 'latestReleaseDate';
$config->program->productview->dtable->fieldList['latestReleaseDate']['title']    = $lang->program->latestRelease;
$config->program->productview->dtable->fieldList['latestReleaseDate']['minWidth'] = 120;
$config->program->productview->dtable->fieldList['latestReleaseDate']['type']     = 'date';
$config->program->productview->dtable->fieldList['latestReleaseDate']['sortType'] = true;
$config->program->productview->dtable->fieldList['latestReleaseDate']['group']    = 'g8';

$config->program->productview->dtable->fieldList['latestRelease']['name']       = 'latestRelease';
$config->program->productview->dtable->fieldList['latestRelease']['title']      = $lang->program->latestRelease;
$config->program->productview->dtable->fieldList['latestRelease']['minWidth']   = 80;
$config->program->productview->dtable->fieldList['latestRelease']['type']       = 'text';
$config->program->productview->dtable->fieldList['latestRelease']['filterType'] = true;
$config->program->productview->dtable->fieldList['latestRelease']['group']      = 'g8';

/* DataTable fields of Project View. */
$config->program->projectView = new stdClass();
$config->program->projectView->dtable = new stdClass();
$config->program->projectView->dtable->fieldList = array();

$config->program->projectView->dtable->fieldList['name']['name']         = 'name';
$config->program->projectView->dtable->fieldList['name']['title']        = $lang->nameAB;
$config->program->projectView->dtable->fieldList['name']['width']        = 200;
$config->program->projectView->dtable->fieldList['name']['type']         = 'link';
$config->program->projectView->dtable->fieldList['name']['flex']         = 1;
$config->program->projectView->dtable->fieldList['name']['nestedToggle'] = true;
$config->program->projectView->dtable->fieldList['name']['checkbox']     = true;
$config->program->projectView->dtable->fieldList['name']['sortType']     = true;
$config->program->projectView->dtable->fieldList['name']['iconRender']   = 'RAWJS<function(val,row){ if(row.data.type === \'program\') return \'icon-cards-view text-gray\'; if(row.data.type === \'productLine\') return \'icon-scrum text-gray\'; return \'\';}>RAWJS';
$config->program->projectView->dtable->fieldList['name']['show']         = true;
$config->program->projectView->dtable->fieldList['name']['group']        = 1;

$config->program->projectView->dtable->fieldList['status']['name']      = 'status';
$config->program->projectView->dtable->fieldList['status']['title']     = $lang->program->status;
$config->program->projectView->dtable->fieldList['status']['minWidth']  = 60;
$config->program->projectView->dtable->fieldList['status']['type']      = 'status';
$config->program->projectView->dtable->fieldList['status']['sortType']  = true;
$config->program->projectView->dtable->fieldList['status']['statusMap'] = $lang->program->statusList;
$config->program->projectView->dtable->fieldList['status']['show']      = true;
$config->program->projectView->dtable->fieldList['status']['group']     = 2;

$config->program->projectView->dtable->fieldList['PM']['name']     = 'PM';
$config->program->projectView->dtable->fieldList['PM']['title']    = $lang->program->PM;
$config->program->projectView->dtable->fieldList['PM']['minWidth'] = 80;
$config->program->projectView->dtable->fieldList['PM']['type']     = 'avatarBtn';
$config->program->projectView->dtable->fieldList['PM']['sortType'] = true;
$config->program->projectView->dtable->fieldList['PM']['show']     = true;
$config->program->projectView->dtable->fieldList['PM']['group']    = 3;

$config->program->projectView->dtable->fieldList['budget']['name']     = 'budget';
$config->program->projectView->dtable->fieldList['budget']['title']    = $lang->program->budget;
$config->program->projectView->dtable->fieldList['budget']['width']    = 90;
$config->program->projectView->dtable->fieldList['budget']['type']     = 'format';
$config->program->projectView->dtable->fieldList['budget']['sortType'] = true;
$config->program->projectView->dtable->fieldList['budget']['show']     = true;
$config->program->projectView->dtable->fieldList['budget']['group']    = 4;

$config->program->projectView->dtable->fieldList['invested']['name']     = 'invested';
$config->program->projectView->dtable->fieldList['invested']['title']    = $lang->program->invested;
$config->program->projectView->dtable->fieldList['invested']['minWidth'] = 70;
$config->program->projectView->dtable->fieldList['invested']['type']     = 'format';
$config->program->projectView->dtable->fieldList['invested']['sortType'] = true;
$config->program->projectView->dtable->fieldList['invested']['show']     = true;
$config->program->projectView->dtable->fieldList['invested']['group']    = 4;

$config->program->projectView->dtable->fieldList['openedDate']['name']     = 'openedDate';
$config->program->projectView->dtable->fieldList['openedDate']['title']    = $lang->program->openedDate;
$config->program->projectView->dtable->fieldList['openedDate']['type']     = 'date';
$config->program->projectView->dtable->fieldList['openedDate']['sortType'] = true;
$config->program->projectView->dtable->fieldList['openedDate']['minWidth'] = 90;
$config->program->projectView->dtable->fieldList['openedDate']['group']    = 5;

$config->program->projectView->dtable->fieldList['openedBy']['name']     = 'openedBy';
$config->program->projectView->dtable->fieldList['openedBy']['title']    = $lang->program->openedBy;
$config->program->projectView->dtable->fieldList['openedBy']['type']     = 'user';
$config->program->projectView->dtable->fieldList['openedBy']['sortType'] = true;
$config->program->projectView->dtable->fieldList['openedBy']['minWidth'] = 80;
$config->program->projectView->dtable->fieldList['openedBy']['group']    = 5;

$config->program->projectView->dtable->fieldList['begin']['name']     = 'begin';
$config->program->projectView->dtable->fieldList['begin']['title']    = $lang->program->begin;
$config->program->projectView->dtable->fieldList['begin']['minWidth'] = 90;
$config->program->projectView->dtable->fieldList['begin']['type']     = 'date';
$config->program->projectView->dtable->fieldList['begin']['sortType'] = true;
$config->program->projectView->dtable->fieldList['begin']['show']     = true;
$config->program->projectView->dtable->fieldList['begin']['group']    = 6;

$config->program->projectView->dtable->fieldList['end']['name']     = 'end';
$config->program->projectView->dtable->fieldList['end']['title']    = $lang->program->end;
$config->program->projectView->dtable->fieldList['end']['minWidth'] = 90;
$config->program->projectView->dtable->fieldList['end']['type']     = 'date';
$config->program->projectView->dtable->fieldList['end']['sortType'] = true;
$config->program->projectView->dtable->fieldList['end']['show']     = true;
$config->program->projectView->dtable->fieldList['end']['group']    = 6;

$config->program->projectView->dtable->fieldList['realBegan']['name']     = 'realBegan';
$config->program->projectView->dtable->fieldList['realBegan']['title']    = $lang->program->realBeganAB;
$config->program->projectView->dtable->fieldList['realBegan']['minWidth'] = 90;
$config->program->projectView->dtable->fieldList['realBegan']['type']     = 'date';
$config->program->projectView->dtable->fieldList['realBegan']['sortType'] = true;
$config->program->projectView->dtable->fieldList['realBegan']['group']    = 7;

$config->program->projectView->dtable->fieldList['realEnd']['name']     = 'realEnd';
$config->program->projectView->dtable->fieldList['realEnd']['title']    = $lang->program->realEndAB;
$config->program->projectView->dtable->fieldList['realEnd']['minWidth'] = 90;
$config->program->projectView->dtable->fieldList['realEnd']['type']     = 'date';
$config->program->projectView->dtable->fieldList['realEnd']['sortType'] = true;
$config->program->projectView->dtable->fieldList['realEnd']['group']    = 7;

$config->program->projectView->dtable->fieldList['progress']['name']     = 'progress';
$config->program->projectView->dtable->fieldList['progress']['title']    = $lang->program->progressAB;
$config->program->projectView->dtable->fieldList['progress']['minWidth'] = 100;
$config->program->projectView->dtable->fieldList['progress']['type']     = 'circleProgress';
$config->program->projectView->dtable->fieldList['progress']['show']     = true;
$config->program->projectView->dtable->fieldList['progress']['group']    = 8;

global $app;
$app->loadLang('project');

$config->program->projectView->dtable->fieldList['actions']['name']       = 'actions';
$config->program->projectView->dtable->fieldList['actions']['title']      = $lang->actions;
$config->program->projectView->dtable->fieldList['actions']['width']      = 160;
$config->program->projectView->dtable->fieldList['actions']['type']       = 'actions';
$config->program->projectView->dtable->fieldList['actions']['fixed']      = 'right';
$config->program->projectView->dtable->fieldList['actions']['actionsMap'] = array
(
    'program_start'     => array('icon'  => 'icon-start',        'hint' => $lang->program->start),
    'program_suspend'   => array('icon'  => 'icon-pause',        'hint' => $lang->program->suspend),
    'program_close'     => array('icon'  => 'icon-off',          'hint' => $lang->program->close),
    'program_activate'  => array('icon'  => 'icon-active',       'hint' => $lang->program->activate),
    'program_other'     => array('caret' => true,                'hint' => $lang->other, 'type' => 'dropdown'),
    'program_edit'      => array('icon'  => 'icon-edit',         'hint' => $lang->program->edit),
    'program_create'    => array('icon'  => 'icon-split',        'hint' => $lang->program->create),
    'program_delete'    => array('icon'  => 'icon-trash',        'hint' => $lang->program->delete),
    'project_start'     => array('icon'  => 'icon-start',        'hint' => $lang->project->start),
    'project_suspend'   => array('icon'  => 'icon-pause',        'hint' => $lang->project->suspend),
    'project_close'     => array('icon'  => 'icon-off',          'hint' => $lang->project->close),
    'project_activate'  => array('icon'  => 'icon-active',       'hint' => $lang->project->activate),
    'project_other'     => array('caret' => true,                'hint' => $lang->project->other, 'type' => 'dropdown'),
    'project_edit'      => array('icon'  => 'icon-edit',         'hint' => $lang->project->edit),
    'project_team'      => array('icon'  => 'icon-groups',       'hint' => $lang->project->manageMembers),
    'project_group'     => array('icon'  => 'icon-lock',         'hint' => $lang->project->group),
    'project_more'      => array('icon'  => 'icon-ellipsis-v',   'hint' => $lang->project->moreActions, 'type' => 'dropdown', 'caret' => false),
    'project_link'      => array('icon'  => 'icon-link',         'hint' => $lang->project->manageProducts),
    'project_whitelist' => array('icon'  => 'icon-shield-check', 'hint' => $lang->project->whitelist),
    'project_delete'    => array('icon'  => 'icon-trash',        'hint' => $lang->project->delete)
);
