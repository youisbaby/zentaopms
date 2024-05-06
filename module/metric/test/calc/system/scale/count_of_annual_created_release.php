#!/usr/bin/env php
<?php

/**

title=count_of_annual_created_release
timeout=0
cid=1

- 测试新增发布分组数。 @11
- 测试2019年新增发布数。第0条的value属性 @22
- 测试2020年新增发布数。第0条的value属性 @22

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('product')->loadYaml('product', true, 4)->gen(10);
zendata('project')->loadYaml('project', true, 4)->gen(10);
zendata('release')->loadYaml('release', true, 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('11'); // 测试新增发布分组数。

r($calc->getResult(array('year' => '2019'))) && p('0:value') && e('36'); // 测试2019年新增发布数。
r($calc->getResult(array('year' => '2020'))) && p('0:value') && e('36'); // 测试2020年新增发布数。
