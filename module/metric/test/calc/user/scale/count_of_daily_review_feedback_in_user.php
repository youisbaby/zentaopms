#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('product')->config('product_shadow', $useCommon = true, $levels = 4)->gen(10);
zdTable('feedback')->config('feedback', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_daily_review_feedback_in_user
timeout=0
cid=1

*/

r(count($calc->getResult())) && p('') && e('120'); // 测试分组数。

r(count($calc->getResult(array('user' => 'admin'))))    && p('') && e('10'); // 测试用户admin
r(count($calc->getResult(array('user' => 'user'))))     && p('') && e('10'); // 测试用户user
r(count($calc->getResult(array('user' => 'dev'))))      && p('') && e('30'); // 测试用户dev
r(count($calc->getResult(array('user' => 'pm'))))       && p('') && e('20'); // 测试用户pm
r($calc->getResult(array('user' => 'notexist')))        && p('') && e('0');  // 测试不存在的用户
