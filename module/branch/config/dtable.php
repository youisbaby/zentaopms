<?php
$config->branch->dtable = new stdclass();

$config->branch->dtable->fieldList['name']['type']     = 'title';
$config->branch->dtable->fieldList['name']['sortType'] = false;

$config->branch->dtable->fieldList['status']['type']      = 'status';
$config->branch->dtable->fieldList['status']['sortType']  = false;
$config->branch->dtable->fieldList['status']['statusMap'] = $lang->branch->statusList;

$config->branch->dtable->fieldList['createdDate']['type'] = 'date';

$config->branch->dtable->fieldList['closedDate']['type'] = 'date';

$config->branch->dtable->fieldList['desc']['type']     = 'desc';
$config->branch->dtable->fieldList['desc']['sortType'] = false;

$config->branch->dtable->fieldList['actions']['type'] = 'actions';
$config->branch->dtable->fieldList['actions']['menu'] = array('edit', 'close|activate');
$config->branch->dtable->fieldList['actions']['list'] = $config->branch->actionList;
