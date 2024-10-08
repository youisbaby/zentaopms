#!/usr/bin/env php
<?php

/**

title=测试 storyModel->change();
timeout=0
cid=0

- 查看变更后需求数据。
 - 属性title @测试需求1变更标题
 - 属性spec @测试需求1的变更描述
 - 属性version @4
- 变更时不填写需求名称，给出提示 @1

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/story.unittest.class.php';
su('admin');

zenData('story')->gen(30);
zenData('storyspec')->gen(90);

$story  = new storyTest();
$story1 = new stdclass();
$story1->title              = '测试需求1变更标题';
$story1->spec               = '测试需求1的变更描述';
$story1->verify             = '测试需求1的变更验收标准';
$story1->deleteFiles        = array();
$story1->reviewerHasChanged = '';
$story1->estimate           = 1;
$story1->reviewer           = array();
$story1->version            = 4;

$story2 = clone $story1;
$story2->reviewer = array('admin', 'test2');
$story2->title    = '';
$story2->version  = 5;

$result1 = $story->changeTest(1,  $story1);
$result2 = $story->changeTest(2,  $story2);

r($result1) && p('title,spec,version') && e('测试需求1变更标题,测试需求1的变更描述,4'); // 查看变更后需求数据。
r((int)strpos($result2['title'][0], '名称』不能为空') !== false) && p() && e('1');      // 变更时不填写需求名称，给出提示