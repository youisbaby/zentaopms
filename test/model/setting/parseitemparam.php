#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/setting.class.php';
su('admin');

/**

title=测试 settingModel->parseItemParam();
cid=1
pid=1

*/

$setting = new settingTest();

r($setting->parseItemParamTest()) && p() && e();