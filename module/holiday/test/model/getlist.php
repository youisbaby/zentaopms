#!/usr/bin/env php
<?php
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/holiday.class.php';

zdTable('holiday')->gen(10);
zdTable('user')->gen(1);

su('admin');

/**

title=测试 holidayModel->getList();
cid=1
pid=1

返回2022年的holiday list >> 100
返回2002年的holiday list >> 0
返回所有年份的holiday list >> 0
返回2022年所有类型的holiday list >> 100
返回2022年类型为holiday的holiday list >> 50
返回2022年类型为working的holiday list >> 50
返回2022年类型为空holiday list >> 100

*/

$holiday = new holidayTest();
$t_numyear = array('thisyear', 'lastyear', '');
$t_type    = array('all', 'holiday', 'working', '');

r($holiday->getListTest($t_numyear[0]))             && p() && e('1,2,3,4,5,6,7,8,9,10'); // 返回2022年的holiday list
r($holiday->getListTest($t_numyear[1]))             && p() && e('0');                    // 返回2002年的holiday list
r($holiday->getListTest($t_numyear[2]))             && p() && e('1,2,3,4,5,6,7,8,9,10'); // 返回所有年份的holiday list
r($holiday->getListTest($t_numyear[0], $t_type[0])) && p() && e('1,2,3,4,5,6,7,8,9,10'); // 返回2022年所有类型的holiday list
r($holiday->getListTest($t_numyear[0], $t_type[1])) && p() && e('1,3,5,7,9');            // 返回2022年类型为holiday的holiday list
r($holiday->getListTest($t_numyear[0], $t_type[2])) && p() && e('2,4,6,8,10');           // 返回2022年类型为working的holiday list
r($holiday->getListTest($t_numyear[0], $t_type[3])) && p() && e('1,2,3,4,5,6,7,8,9,10'); // 返回2022年类型为空holiday list
