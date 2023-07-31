<?php
/**
 * 按全局统计的未完成任务数。
 * Count of unfinished task.
 *
 * 范围：global
 * 对象：task
 * 目的：scale
 * 度量名称：按全局统计的未完成任务数
 * 单位：个
 * 描述：按全局统计的未完成任务数是指团队或组织未完成的任务总量。该度量项可以用来评估项目进展和未来工作量，同时也可以帮助进行资源分配和优先级确定。较大的未完成任务总数可能需要更多的努力和调整来确保任务按时完成。
 * 定义：复用：
 *       按全局统计的任务总数
 *       按全局统计的已完成任务数
 *       公式：
 *       按全局统计的未完成任务数=按全局统计的任务总数-按全局统计的已完成任务数
 * 度量库：
 * 收集方式：realtime
 *
 * @copyright Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @author    zhouxin <zhouxin@easycorp.ltd>
 * @package
 * @uses      func
 * @license   ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @Link      https://www.zentao.net
 */
class count_of_unfinished_task extends baseCalc
{
    public $dataset = 'getTasks';

    public $fieldList = array('t1.status');

    public function calculate($row)
    {
        if($this->status != 'done') $this->result ++;
    }

    public function getResult($options = array())
    {
        $records = $this->getRecords(array('value'));
        return $this->filterByOptions($records, $options);
    }
}
