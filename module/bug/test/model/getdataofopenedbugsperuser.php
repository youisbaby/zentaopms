#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php"; su('admin');
include dirname(__FILE__, 2) . '/bug.class.php';

/**

title=bugModel->getDataOfOpenedBugsPerUser();
cid=1
pid=1

获取admin创建的数据 >> admin,315

*/

$bug=new bugTest();
r($bug->getDataOfOpenedBugsPerUserTest()) && p('admin:name,value') && e('admin,315');   // 获取admin创建的数据