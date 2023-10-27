<?php
declare(strict_types = 1);
/**
 * The project deviation view file of pivot module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Gang Liu <liugang@easycorp.ltd>
 * @package     pivot
 * @link        https://www.zentao.net
 */
namespace zin;

$getDeviationHtml = function(float $deviation): string
{
    if($deviation > 0) return "<span class='up'>&uarr;</span>" . $deviation;
    if($deviation < 0) return "<span class='down'>&darr;</span>" . abs($deviation);
    return "<span class='zero'>0</span>";
};

$getDeviationRateHtml = function(float $deviationRate): string
{
    if($deviationRate >= 50)    return "<span class='u50'>" . $deviationRate . '%</span>';
    if($deviationRate >= 30)    return "<span class='u30'>" . $deviationRate . '%</span>';
    if($deviationRate >= 10)    return "<span class='u10'>" . $deviationRate . '%</span>';
    if($deviationRate > 0)      return "<span class='u0'>" . abs($deviationRate) . '%</span>';
    if($deviationRate <= -20)   return "<span class='d20'>" . abs($deviationRate) . '%</span>';
    if($deviationRate < 0)      return "<span class='d0'>" . abs($deviationRate) . '%</span>';
    if($deviationRate == 'n/a') return "<span class='zero'>" . $deviationRate . '</span>';
    return "<span class='zero'>" . abs($deviationRate) . '%</span>';
};

foreach($executions as $execution)
{
    $execution->deviation     = $getDeviationHtml($execution->deviation);
    $execution->deviationRate = $getDeviationRateHtml($execution->deviationRate);
}

$cols = $config->pivot->dtable->projectDeviation->fieldList;

$generateData = function() use ($module, $method, $lang, $cols, $executions)
{
    if(empty($module) || empty($method)) return div(setClass('bg-white center text-gray w-full h-40'), $lang->error->noData);

    return div
    (
        setClass('w-full'),
        div
        (
            setID('conditions'),
            setClass('bg-white mb-4 p-2'),
        ),
        dtable
        (
            set::cols($cols),
            set::data($executions),
            set::fixedLeftWidth('0.25'),
            set::emptyTip($lang->error->noData)
        ),
        echarts
        (
        )
    );
};
