#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/group.class.php';
su('admin');

/**

title=测试 groupModel->copyUser();
cid=1
pid=1

复制分组6用户到分组7 >> 1

*/

$fromGroup = 6;
$toGroup   = 7;

$group = new groupTest();

r($group->copyUserTest($fromGroup, $toGroup)) && p() && e('1'); // 复制分组6用户到分组7
