<?php
/**
 * 按产品统计的研发需求评审通过率。
 * Rate of approved story in product.
 *
 * 范围：product
 * 对象：story
 * 目的：qc
 * 度量名称：按产品统计的研发需求评审通过率
 * 单位：%
 * 描述：产品中不需要评审的与评审通过的研发需求数相对于不需要评审的与有评审结果的需求数的比例
 * 度量库：
 * 收集方式：realtime
 *
 * @copyright Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @author    qixinzhi <qixinzhi@easycorp.ltd>
 * @package
 * @uses      func
 * @license   ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @Link      https://www.zentao.net
 */
class rate_of_approved_story_in_product extends baseCalc
{
    public $result = array();

    public function getStatement()
    {
        return $this->dao->select("COUNT(t1.result) as total, SUM(IF(result='pass', 1, 0)) as pass, t2.product")
            ->from(TABLE_STORYREVIEW)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t2.product=t3.id')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t2.type')->eq('story')
            ->andWhere('t3.shadow')->eq(0)
            ->groupBy('t2.product')
            ->query();
    }

    public function calculate($row)
    {
        $total   = $row->total;
        $pass    = $row->pass;
        $product = $row->product;

        $this->result[$product] = $total == 0 ? 0 : round($pass / $total, 4);
    }

    public function getResult($options = array())
    {
        $records = $this->getRecords(array('product', 'value'));
        return $this->filterByOptions($records, $options);
    }
}
