#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('product')->gen(200);
zdTable('story')->config('story_create')->gen(5000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_annual_created_requirement_in_product
cid=1
pid=1

*/

r(count($calc->getResult()))                                     && p('')        && e('507'); // 测试按产品的年度新增用户需求分组数。
r($calc->getResult(array('product' => '78', 'year' => '2021')))  && p('0:value') && e('10');  // 测试2021年产品78新增的用户需求数。
r($calc->getResult(array('product' => '84', 'year' => '2022')))  && p('0:value') && e('0');   // 测试2022年产品84新增的用户需求数。
r($calc->getResult(array('product' => '999', 'year' => '2021'))) && p('')        && e('0');   // 测试不存在的产品的用户需求数。
