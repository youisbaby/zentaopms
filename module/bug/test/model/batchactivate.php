#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/bug.class.php';
su('admin');

/**

title=测试bugModel->batchActivate();
cid=1
pid=1

测试批量激活bug >> active;active

*/

$bugIDList = array('1' => '1', '52' => '53', '82' => '82');

$bug = new bugTest();
r($bug->batchActivateObject($bugIDList)) && p('53:status;82:status') && e('active;active'); // 测试批量激活bug
