#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";

function initData()
{
    $task = zdTable('task');
    $task->id->range('1-5');
    $task->name->prefix('任务')->range('1-5');
    $task->consumed->range('0,10,15,20,25');
    $task->left->range('0,0,1,5,10');

    $task->gen(5);
}

/**

title=测试computeTasksProgress
timeout=0
cid=2


*/
$tester->loadModel('task');

initData();

$taskIDList = range(1,5);
$taskList   = $tester->task->getByList($taskIDList);

r($tester->task->computeTasksProgress($taskList)) && p('1:progress') && e('0');   //测试任务消耗工时为0，剩余工时为0的情况
r($tester->task->computeTasksProgress($taskList)) && p('2:progress') && e('100');   //测试任务消耗工时不为0，剩余工时为0的情况
r($tester->task->computeTasksProgress($taskList)) && p('3:progress') && e('94');  //测试任务消耗工时为15，剩余工时为1的情况
r($tester->task->computeTasksProgress($taskList)) && p('4:progress') && e('80');  //测试任务消耗工时为20，剩余工时为5的情况
r($tester->task->computeTasksProgress($taskList)) && p('5:progress') && e('71');  //测试任务消耗工时为25，剩余工时为10的情况
