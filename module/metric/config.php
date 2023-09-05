<?php
$config->metric = new stdclass();
$config->metric->scopeList     = array('system', 'program', 'project', 'execution', 'product', 'user', 'dept');
$config->metric->purposeList   = array('scale', 'qc', 'hour', 'cost', 'rate', 'time');
$config->metric->dateList      = array('year', 'month', 'week', 'day');
$config->metric->excludeGlobal = array('program', 'project', 'product', 'execution', 'user', 'dept');

global $lang;
$config->metric->actionList = array();
$config->metric->actionList['edit']['icon'] = 'edit';
$config->metric->actionList['edit']['text'] = $lang->edit;
$config->metric->actionList['edit']['hint'] = $lang->edit;
$config->metric->actionList['edit']['url']  = helper::createLink('metric', 'edit', 'metricID={id}');

$config->metric->actionList['implement']['icon']        = 'code';
$config->metric->actionList['implement']['text']        = $lang->metric->implement;
$config->metric->actionList['implement']['hint']        = $lang->metric->implement;
$config->metric->actionList['implement']['data-toggle'] = 'modal';
$config->metric->actionList['implement']['url']         = helper::createLink('metric', 'implement', 'metricID={id}&isVerify=true');

$config->metric->actionList['delist']['icon'] = 'ban-circle';
$config->metric->actionList['delist']['text'] = $lang->metric->delist;
$config->metric->actionList['delist']['hint'] = $lang->metric->delist;
$config->metric->actionList['delist']['url']  = 'javascript:confirmDelist("{id}", "{name}")';

$config->metric->actionList['delete']['icon']         = 'trash';
$config->metric->actionList['delete']['hint']         = $lang->delete;
$config->metric->actionList['delete']['url']          = helper::createLink('metric', 'delete', 'metricID={id}');
$config->metric->actionList['delete']['class']        = 'ajax-submit';
$config->metric->actionList['delete']['data-confirm'] = $lang->metric->confirmDelete;

$config->metric->necessaryMethodList = array('getStatement', 'calculator', 'getResult');

$config->metric->oldScopeMap = array();
$config->metric->oldScopeMap['project'] = 'project';
$config->metric->oldScopeMap['product'] = 'product';
$config->metric->oldScopeMap['sprint']  = 'execution';

$config->metric->oldPurposeMap = array();
$config->metric->oldPurposeMap['scale']    = 'scale';
$config->metric->oldPurposeMap['duration'] = 'time';
$config->metric->oldPurposeMap['workload'] = 'hour';
$config->metric->oldPurposeMap['cost']     = 'cost';
$config->metric->oldPurposeMap['quality']  = 'qc';

$config->metric->oldObjectMap = array();
$config->metric->oldObjectMap['staff']       = 'user';
$config->metric->oldObjectMap['finance']     = 'task';
$config->metric->oldObjectMap['case']        = 'case';
$config->metric->oldObjectMap['bug']         = 'bug';
$config->metric->oldObjectMap['review']      = 'review';
$config->metric->oldObjectMap['stage']       = 'execution';
$config->metric->oldObjectMap['program']     = 'project';
$config->metric->oldObjectMap['softRequest'] = 'story';
$config->metric->oldObjectMap['userRequest'] = 'requirement';
