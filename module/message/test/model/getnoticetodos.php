#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/message.class.php';
su('admin');

/**

title=测试 messageModel->getNoticeTodos();
cid=1
pid=1

查询notice为空的todo的信息 >> 0

*/

$message = new messageTest();

r($message->getNoticeTodosTest()) && p('0') && e('0'); //查询notice为空的todo的信息