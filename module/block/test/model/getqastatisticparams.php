#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/block.class.php';
su('admin');

/**

title=测试 blockModel->getQaStatisticParams();
cid=1
pid=1

测试name >> 类型
测试control >> select

*/

$block = new blockTest();

r($block->getQaStatisticParamsTest()) && p('type:name')    && e('类型');  //测试name
r($block->getQaStatisticParamsTest()) && p('type:control') && e('select');//测试control