#!/usr/bin/env php
<?php

/**

title=关闭项目测试
timeout=0
cid=73

- 关闭项目成功  测试结果 @关闭项目成功

*/
chdir(__DIR__);
include '../lib/closeproject.ui.class.php';

$tester = new closeProjectTester();
$tester->login();

$project = array();

r($tester->closeProject($project])) && p('message') && e('关闭项目成功');                      //关闭项目

$tester->closeBrowser();
