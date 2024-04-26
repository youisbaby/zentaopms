#!/usr/bin/env php
<?php

/**

title=count_of_closed_top_program
timeout=0
cid=1

- 测试关闭的一级项目集数量第0条的value属性 @45

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.class.php';

zendata('project')->loadYaml('program_closed', true, 4)->gen(356, true, false);

$metric = new metricTest();
$calc = $metric->calcMetric(__FILE__);

r($calc->getResult()) && p('0:value') && e('45'); // 测试关闭的一级项目集数量