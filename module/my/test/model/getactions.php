#!/usr/bin/env php
<?php
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/my.class.php';

zdTable('action')->gen('100');
zdTable('user')->gen('1');

su('admin');

/**

title=测试 myModel->getActions();
cid=1
pid=1

正常查询action >> 0
正常查询action统计 >> 0

*/

$my = new myTest();


$actions = $my->getActionsTest();
r(implode(',', $actions)) && p() && e('96,64,32,95,63'); // 正常查询action
r(count($actions))        && p() && e('5');              // 正常查询action统计
