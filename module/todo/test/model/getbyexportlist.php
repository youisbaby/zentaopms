#!/usr/bin/env php
<?php
declare(strict_types=1);

include dirname(__FILE__, 5) . 'test/lib/init.php';
include dirname(__FILE__, 2) . '/todo.class.php';
su('admin');

/**

title=测试 todoModel->getByExportList();
cid=1
pid=1

*/

zdTable('todo')->config('getbyexportlist')->gen(5);

$todo = new todoTest();

$testWhere  = " `deleted` = '0' AND `vision` = 'rnd' AND `assignedTo` = 'admin' AND `date` >= '20230301' AND `date` <= '20230301' AND `status` IN ('wait') ";
$testWhere2 = " `deleted` = '0' and `status` IN ('closed') ";

$testResult  = $todo->getByExportListTest("date_desc", $testWhere,  $selectedItem = '');
$testResult2 = $todo->getByExportListTest("date_desc", $testWhere2, $selectedItem = '');

r(count($testResult)) && p() && e('1');
r($testResult[1]) && p('name,status') && e('待办1,wait');

r(count($testResult2)) && p() && e('2');
r($testResult2[4]) && p('name,status') && e('待办4,closed');
r($testResult2[5]) && p('name,status') && e('待办5,closed');
