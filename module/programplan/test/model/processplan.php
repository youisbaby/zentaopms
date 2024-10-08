#!/usr/bin/env php
<?php

/**

title=测试 programplanModel->processPlan();
cid=0

- 测试id为1的瀑布项目
 - 属性id @1
 - 属性name @瀑布项目1
 - 属性productName @瀑布产品1
- 测试id为2的瀑布项目阶段
 - 属性id @2
 - 属性name @阶段a
 - 属性productName @瀑布产品2
 - 属性attribute @review
- 测试id为3的瀑布项目阶段
 - 属性id @3
 - 属性name @阶段a子1
 - 属性productName @瀑布产品2
 - 属性attribute @release

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/programplan.unittest.class.php';
su('admin');

zenData('project')->loadYaml('project')->gen(5);
zenData('projectproduct')->loadYaml('projectproduct')->gen(5);
zenData('product')->loadYaml('product')->gen(2);
$planIDList = array(1, 2, 3);

$programplan = new programplanTest();

r($programplan->processPlanTest($planIDList[0])) && p('id,name,productName')           && e('1,瀑布项目1,瀑布产品1');        // 测试id为1的瀑布项目
r($programplan->processPlanTest($planIDList[1])) && p('id,name,productName,attribute') && e('2,阶段a,瀑布产品2,review');     // 测试id为2的瀑布项目阶段
r($programplan->processPlanTest($planIDList[2])) && p('id,name,productName,attribute') && e('3,阶段a子1,瀑布产品2,release'); // 测试id为3的瀑布项目阶段
