#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/kanban.class.php';
su('admin');

/**

title=测试 kanbanModel->getToAndCcList();
cid=1
pid=1

获取id=1的卡片发信人员 >> admin;admin

*/
$kanban = new kanbanTest();

$card = $kanban->getCardByIDTest('1');
r($kanban->getToAndCcListTest($card)) && p('0;1') && e('admin;admin'); // 获取id=1的卡片发信人员