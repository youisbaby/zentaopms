#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/group.class.php';
su('admin');

/**

title=测试 groupModel->checkMenuModule();
cid=1
pid=1



*/

$group = new groupTest();

r($group->checkMenuModuleTest('','index'))    && p('') && e(1);  // 测试获取 '','index'    的返回结果
r($group->checkMenuModuleTest('my','tree'))   && p('') && e(''); // 测试获取 'my','tree'   的返回结果
r($group->checkMenuModuleTest('none','tree')) && p('') && e(''); // 测试获取 'none','tree' 的返回结果