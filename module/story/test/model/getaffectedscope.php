#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

$story = zdTable('story');
$story->product->range(1);
$story->plan->range('0,1,0{100}');
$story->duplicateStory->range('0,4,0{100}');
$story->linkStories->range('0,6,0{100}');
$story->linkRequirements->range('3,0{100}');
$story->childStories->range('0,8,0{100}');
$story->toBug->range('0{9},1,0{100}');
$story->parent->range('0{17},`-1`,0,18,0{100}');
$story->twins->range('``{27},30,``,28');
$story->gen(30);

$storySpec = zdTable('storyspec');
$storySpec->story->range('1-30{3}');
$storySpec->version->range('1-3');
$storySpec->gen(90);

$project = zdTable('project');
$project->project->range('0{10},1-10,11-20{6}');
$project->parent->range('0{10},1-10,11-20{6}');
$project->type->range('program{10},project{10},sprint{60}');
$project->gen(80)->fixPath();

$projectStory = zdTable('projectstory');
$projectStory->project->range('11-17{6},21-40{2}');
$projectStory->product->range('1');
$projectStory->story->range('2-30:2');
$projectStory->gen(80);

$task = zdTable('task');
$task->story->range('2-30:2{2}');
$task->project->range('11-17{6}');
$task->execution->range('21-30{2}');
$task->storyVersion->range('3');
$task->assignedTo->range('admin');
$task->gen(60);

$team = zdTable('team');
$team->account->range('admin,user1,user2,user3,user4');
$team->root->range('11-40{3}');
$team->type->range('project');
$team->gen(90);

$bug = zdTable('bug');
$bug->story->range('2-30:2');
$bug->gen(40);

$case = zdTable('case');
$case->story->range('2-30:2');
$case->gen(40);

zdTable('storystage')->gen(30);
zdTable('productplan')->gen(1);
zdTable('branch')->gen(5);

/**

title=测试 storyModel->getAffectedScope();
cid=1
pid=1

获取需求1影响任务的数量 >> 6
获取需求15影响任务的数量 >> 0
查看返回的需求1的title >> 用户需求版本三41
查看返回的需求15的title >> 软件需求版本三55
查看需求100的影响的迭代的名字 >> 迭代25

*/

$story = new storyTest();
$affectedStory2  = $story->getAffectedScopeTest(2);
$affectedStory28 = $story->getAffectedScopeTest(28);

r(implode('|', array_keys($affectedStory2->teams))) && p() && e('22|30|37');  //获取需求2团队成员的数量
r(implode('|', array_keys($affectedStory2->tasks))) && p() && e('26|21');     //获取需求2影响任务的数量
r($affectedStory2->tasks[21][0]) && p('assignedTo') && e('A:admin');          //获取需求2影响任务的指派给

r(count($affectedStory2->bugs))            && p() && e('3');  //获取需求2关联bug数
r(count($affectedStory28->bugs))           && p() && e('4');  //获取需求28关联bug数，包含孪生需求
r(count($affectedStory2->cases))           && p() && e('3');  //获取需求2关联用例数
r(count($affectedStory28->cases))          && p() && e('4');  //获取需求28关联用例数，包含孪生需求
r((int)empty($affectedStory2->twins))      && p() && e('1');  //检查需求2孪生需求
r((int)isset($affectedStory28->twins[30])) && p() && e('1');  //检查需求28孪生需求
