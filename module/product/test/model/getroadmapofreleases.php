#!/usr/bin/env php
<?php

/**

title=测试productModel->getRoadmapOfReleases();
cid=0

- 获取产品ID为1的产品发布数据 @3
- 获取产品ID为1下主干的产品发布数据 @3
- 获取产品ID为1下分支1的产品发布数据 @3
- 获取产品ID为1的产品发布数据 @3
- 获取产品ID为6的产品发布数据 @3
- 获取产品ID为6下主干的产品发布数据 @3
- 获取产品ID为6下分支1的产品发布数据 @3
- 获取产品ID为6的产品发布数据 @3

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/product.unittest.class.php';

zenData('user')->gen(5);
zenData('productplan')->loadYaml('productplan')->gen(30);
zenData('release')->loadYaml('release')->gen(30);
su('admin');

$productIdList = array(1, 6);
$branchList    = array('all', '0', '1');
$countList     = array(0, 1);

$productTester = new productTest();
r(count($productTester->getRoadmapOfReleasesTest($productIdList[0], $branchList[0], $countList[0]))) && p() && e('3'); // 获取产品ID为1的产品发布数据
r(count($productTester->getRoadmapOfReleasesTest($productIdList[0], $branchList[1], $countList[0]))) && p() && e('3'); // 获取产品ID为1下主干的产品发布数据
r(count($productTester->getRoadmapOfReleasesTest($productIdList[0], $branchList[2], $countList[0]))) && p() && e('3'); // 获取产品ID为1下分支1的产品发布数据
r(count($productTester->getRoadmapOfReleasesTest($productIdList[0], $branchList[0], $countList[1]))) && p() && e('3'); // 获取产品ID为1的产品发布数据
r(count($productTester->getRoadmapOfReleasesTest($productIdList[1], $branchList[0], $countList[0]))) && p() && e('3'); // 获取产品ID为6的产品发布数据
r(count($productTester->getRoadmapOfReleasesTest($productIdList[1], $branchList[1], $countList[0]))) && p() && e('3'); // 获取产品ID为6下主干的产品发布数据
r(count($productTester->getRoadmapOfReleasesTest($productIdList[1], $branchList[2], $countList[0]))) && p() && e('3'); // 获取产品ID为6下分支1的产品发布数据
r(count($productTester->getRoadmapOfReleasesTest($productIdList[1], $branchList[0], $countList[1]))) && p() && e('3'); // 获取产品ID为6的产品发布数据
