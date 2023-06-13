<?php
global $lang;

$config->mr->dtable = new stdclass;

$config->mr->dtable->fieldList['id']['name']  = 'id';
$config->mr->dtable->fieldList['id']['title'] = $lang->idAB;
$config->mr->dtable->fieldList['id']['type']  = 'id';

$config->mr->dtable->fieldList['title']['name']     = 'title';
$config->mr->dtable->fieldList['title']['title']    = $lang->mr->title;
$config->mr->dtable->fieldList['title']['type']     = 'text';
$config->mr->dtable->fieldList['title']['link']     = helper::createLink('mr', 'view', "MRID={id}");
$config->mr->dtable->fieldList['title']['sortType'] = true;

$config->mr->dtable->fieldList['sourceBranch']['name']     = 'sourceProject';
$config->mr->dtable->fieldList['sourceBranch']['title']    = $lang->mr->sourceBranch;
$config->mr->dtable->fieldList['sourceBranch']['type']     = 'text';
$config->mr->dtable->fieldList['sourceBranch']['minWidth'] = '200';

$config->mr->dtable->fieldList['targetBranch']['name']     = 'targetProject';
$config->mr->dtable->fieldList['targetBranch']['title']    = $lang->mr->targetBranch;
$config->mr->dtable->fieldList['targetBranch']['type']     = 'text';
$config->mr->dtable->fieldList['targetBranch']['minWidth'] = '200';

$config->mr->dtable->fieldList['mergeStatus']['name']     = 'mergeStatus';
$config->mr->dtable->fieldList['mergeStatus']['title']    = $lang->mr->mergeStatus;
$config->mr->dtable->fieldList['mergeStatus']['type']     = 'text';
$config->mr->dtable->fieldList['mergeStatus']['sortType'] = true;

$config->mr->dtable->fieldList['approvalStatus']['name']     = 'approvalStatus';
$config->mr->dtable->fieldList['approvalStatus']['title']    = $lang->mr->approvalStatus;
$config->mr->dtable->fieldList['approvalStatus']['type']     = 'text';
$config->mr->dtable->fieldList['approvalStatus']['sortType'] = true;

$config->mr->dtable->fieldList['createdBy']['name']     = 'createdBy';
$config->mr->dtable->fieldList['createdBy']['title']    = $lang->mr->author;
$config->mr->dtable->fieldList['createdBy']['type']     = 'user';
$config->mr->dtable->fieldList['createdBy']['sortType'] = true;

$config->mr->dtable->fieldList['createdDate']['name']     = 'createdDate';
$config->mr->dtable->fieldList['createdDate']['title']    = $lang->mr->createdDate;
$config->mr->dtable->fieldList['createdDate']['type']     = 'date';
$config->mr->dtable->fieldList['createdDate']['sortType'] = true;

$config->mr->dtable->fieldList['actions']['name']  = 'actions';
$config->mr->dtable->fieldList['actions']['title'] = $lang->actions;
$config->mr->dtable->fieldList['actions']['type']  = 'actions';
$config->mr->dtable->fieldList['actions']['menu']  = array('view', 'edit', 'diff', 'link', 'delete');

$config->mr->dtable->fieldList['actions']['list']['view']['icon'] = 'eye';
$config->mr->dtable->fieldList['actions']['list']['view']['hint'] = $lang->mr->view;
$config->mr->dtable->fieldList['actions']['list']['view']['url']  = helper::createLink('mr', 'view', "MRID={id}");

$config->mr->dtable->fieldList['actions']['list']['edit']['icon'] = 'edit';
$config->mr->dtable->fieldList['actions']['list']['edit']['hint'] = $lang->mr->edit;
$config->mr->dtable->fieldList['actions']['list']['edit']['url']  = helper::createLink('mr', 'edit', "MRID={id}");

$config->mr->dtable->fieldList['actions']['list']['diff']['icon'] = 'diff';
$config->mr->dtable->fieldList['actions']['list']['diff']['hint'] = $lang->mr->diff;
$config->mr->dtable->fieldList['actions']['list']['diff']['url']  = helper::createLink('mr', 'diff', "MRID={id}");

$config->mr->dtable->fieldList['actions']['list']['link']['icon'] = 'link';
$config->mr->dtable->fieldList['actions']['list']['link']['hint'] = $lang->mr->link;
$config->mr->dtable->fieldList['actions']['list']['link']['url']  = helper::createLink('mr', 'link', "MRID={id}");

$config->mr->dtable->fieldList['actions']['list']['delete']['icon']        = 'trash';
$config->mr->dtable->fieldList['actions']['list']['delete']['hint']        = $lang->mr->delete;
$config->mr->dtable->fieldList['actions']['list']['delete']['url']         = helper::createLink('mr', 'delete', "MRID={id}");
$config->mr->dtable->fieldList['actions']['list']['delete']['data-toggle'] = 'modal';
