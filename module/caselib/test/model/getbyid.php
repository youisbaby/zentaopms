#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/caselib.class.php';

zdTable('testsuite')->gen(405);
zdTable('user')->gen(1);

su('admin');

/**

title=测试 caselibModel->getById();
cid=1
pid=1

*/

$caselibIdList  = array(201, 402, 500);
$setImgSizeList = array(false, true);

$caselib = new caselibTest();

$data = $caselib->getByIdTest(201);

r($caselib->getByIdTest($caselibIdList[0], $setImgSizeList[0])) && p('name,desc,type') && e('这是测试套件名称201,这是测试套件的描述201,library'); // 测试获取数据 201 不修改图片大小 的描述信息
r($caselib->getByIdTest($caselibIdList[0], $setImgSizeList[1])) && p('name,desc,type') && e('这是测试套件名称201,这是测试套件的描述201,library'); // 测试获取数据 201 修改图片大小 的描述信息
r($caselib->getByIdTest($caselibIdList[1], $setImgSizeList[0])) && p('name,desc,type') && e('这是测试套件名称402,这是测试套件的描述402,library'); // 测试获取数据 201 不修改图片大小 的描述信息
r($caselib->getByIdTest($caselibIdList[1], $setImgSizeList[1])) && p('name,desc,type') && e('这是测试套件名称402,这是测试套件的描述402,library'); // 测试获取数据 201 修改图片大小 的描述信息
r($caselib->getByIdTest($caselibIdList[2], $setImgSizeList[0])) && p('name,desc,type') && e('0,0,0'); // 测试获取不存在的数据 500 不修改图片大小 的描述信息
r($caselib->getByIdTest($caselibIdList[2], $setImgSizeList[1])) && p('name,desc,type') && e('0,0,0'); // 测试获取不存在的数据 500 修改图片大小 的描述信息
