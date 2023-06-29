<?php
global $app, $lang;
$config->story->form = new stdclass();
$config->story->form->create = array();
$config->story->form->create['product']     = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->create['branch']      = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->create['module']      = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->create['plan']        = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->create['assignedTo']  = array('type' => 'string',  'control' => 'select',       'required' => false, 'default' => '', 'options' => 'users');
$config->story->form->create['source']      = array('type' => 'string',  'control' => 'select',       'required' => false, 'default' => '', 'options' => $lang->story->sourceList);
$config->story->form->create['sourceNote']  = array('type' => 'string',  'control' => 'text',         'required' => false, 'default' => '', 'filter'  => 'trim');
$config->story->form->create['feedbackBy']  = array('type' => 'string',  'control' => 'text',         'required' => false, 'default' => '', 'filter'  => 'trim');
$config->story->form->create['notifyEmail'] = array('type' => 'string',  'control' => 'text',         'required' => false, 'default' => '', 'filter'  => 'trim');
$config->story->form->create['reviewer']    = array('type' => 'array',   'control' => 'multi-select', 'required' => false, 'default' => '', 'options' => 'users');
$config->story->form->create['URS']         = array('type' => 'array',   'control' => 'multi-select', 'required' => false, 'default' => '', 'options' => array());
$config->story->form->create['parent']      = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->create['region']      = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->create['lane']        = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->create['title']       = array('type' => 'string',  'control' => 'text',         'required' => true,  'filter'  => 'trim');
$config->story->form->create['color']       = array('type' => 'string',  'control' => 'color',        'required' => false, 'default' => '');
$config->story->form->create['category']    = array('type' => 'string',  'control' => 'select',       'required' => false, 'default' => 'feature', 'options' => $lang->story->categoryList);
$config->story->form->create['pri']         = array('type' => 'string',  'control' => 'select',       'required' => false, 'default' => 3,         'options' => array_filter($lang->story->priList));
$config->story->form->create['estimate']    = array('type' => 'float',   'control' => 'text',         'required' => false, 'default' => 0);
$config->story->form->create['spec']        = array('type' => 'string',  'control' => 'editor',       'required' => false, 'default' => '');
$config->story->form->create['verify']      = array('type' => 'string',  'control' => 'editor',       'required' => false, 'default' => '');
$config->story->form->create['keywords']    = array('type' => 'string',  'control' => 'text',         'required' => false, 'default' => '');
$config->story->form->create['type']        = array('type' => 'string',  'control' => 'hidden',       'required' => false, 'default' => 'story',);
$config->story->form->create['mailto']      = array('type' => 'array',   'control' => 'multi-select', 'required' => false, 'default' => '', 'filter' => 'join', 'options' => 'users');
$config->story->form->create['status']      = array('type' => 'string',  'control' => 'hidden',       'required' => false, 'default' => 'active');
$config->story->form->create['branches']    = array('type' => 'array',   'control' => 'select',       'required' => false, 'default' => 0, 'options' => array());
$config->story->form->create['modules']     = array('type' => 'array',   'control' => 'select',       'required' => false, 'default' => 0, 'options' => array());
$config->story->form->create['plans']       = array('type' => 'array',   'control' => 'select',       'required' => false, 'default' => 0, 'options' => array());
$config->story->form->create['vision']      = array('type' => 'string',  'control' => '',             'required' => false, 'default' => $config->vision);
$config->story->form->create['version']     = array('type' => 'int',     'control' => '',             'required' => false, 'default' => 1);
$config->story->form->create['openedBy']    = array('type' => 'string',  'control' => '',             'required' => false, 'default' => $app->user->account);
$config->story->form->create['openedDate']  = array('type' => 'string',  'control' => '',             'required' => false, 'default' => helper::now());

$config->story->form->batchCreate = array();
$config->story->form->batchCreate['branch']     = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->batchCreate['module']     = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->batchCreate['plan']       = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->batchCreate['region']     = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->batchCreate['lane']       = array('type' => 'int',     'control' => 'select',       'required' => false, 'default' => 0,  'options' => array());
$config->story->form->batchCreate['title']      = array('type' => 'string',  'control' => 'text',         'required' => true,  'filter'  => 'trim');
$config->story->form->batchCreate['color']      = array('type' => 'string',  'control' => 'color',        'required' => false, 'default' => '');
$config->story->form->batchCreate['spec']       = array('type' => 'string',  'control' => 'textarea',     'required' => false, 'default' => '');
$config->story->form->batchCreate['source']     = array('type' => 'string',  'control' => 'select',       'required' => false, 'default' => '', 'options' => $lang->story->sourceList);
$config->story->form->batchCreate['sourceNote'] = array('type' => 'string',  'control' => 'text',         'required' => false, 'default' => '', 'filter'  => 'trim');
$config->story->form->batchCreate['verify']     = array('type' => 'string',  'control' => 'textarea',     'required' => false, 'default' => '');
$config->story->form->batchCreate['category']   = array('type' => 'string',  'control' => 'select',       'required' => false, 'default' => 'feature', 'options' => $lang->story->categoryList);
$config->story->form->batchCreate['pri']        = array('type' => 'string',  'control' => 'select',       'required' => false, 'default' => 3,         'options' => $lang->story->priList);
$config->story->form->batchCreate['estimate']   = array('type' => 'float',   'control' => 'text',         'required' => false, 'default' => 0);
$config->story->form->batchCreate['reviewer']   = array('type' => 'array',   'control' => 'multi-select', 'required' => false, 'default' => '', 'options' => 'users');
$config->story->form->batchCreate['keywords']   = array('type' => 'string',  'control' => 'text',         'required' => false, 'default' => '');

$config->story->form->change = array();
$config->story->form->change['reviewer']       = array('type' => 'array',   'control' => 'multi-select', 'required' => true,  'default' => '', 'filter' => 'join', 'options' => '');
$config->story->form->change['title']          = array('type' => 'string',  'control' => 'text',         'required' => true,  'filter'  => 'trim');
$config->story->form->change['color']          = array('type' => 'string',  'control' => 'color',        'required' => false, 'default' => '');
$config->story->form->change['spec']           = array('type' => 'string',  'control' => 'editor',       'required' => false, 'default' => '');
$config->story->form->change['verify']         = array('type' => 'string',  'control' => 'editor',       'required' => false, 'default' => '');
$config->story->form->change['status']         = array('type' => 'string',  'control' => 'hidden',       'required' => false, 'default' => '');
$config->story->form->change['lastEditedDate'] = array('type' => 'string',  'control' => 'hidden',       'required' => false, 'default' => '');

$config->story->form->review = array();
$config->story->form->review['reviewedDate']   = array('type' => 'date',    'control' => 'text',   'required' => false, 'default' => '');
$config->story->form->review['result']         = array('type' => 'string',  'control' => 'select', 'required' => true,  'default' => '', 'options' => '', 'title' => $lang->story->reviewResult);
$config->story->form->review['assignedTo']     = array('type' => 'string',  'control' => 'select', 'required' => false, 'default' => '', 'options' => 'users');
$config->story->form->review['closedReason']   = array('type' => 'string',  'control' => 'select', 'required' => false, 'default' => '', 'options' => $lang->story->reasonList, 'title' => $lang->story->rejectedReason);
$config->story->form->review['pri']            = array('type' => 'int',     'control' => 'select', 'required' => false, 'default' => '', 'options' => $lang->story->priList);
$config->story->form->review['estimate']       = array('type' => 'float',   'control' => 'text',   'required' => false, 'default' => '');
$config->story->form->review['duplicateStory'] = array('type' => 'string',  'control' => 'text',   'required' => false, 'default' => '');
$config->story->form->review['childStories']   = array('type' => 'string',  'control' => 'text',   'required' => false, 'default' => '');
$config->story->form->review['status']         = array('type' => 'string',  'control' => 'hidden', 'required' => false, 'default' => '');
