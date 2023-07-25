<?php
declare(strict_types=1);
/**
 * The results view file of testcase module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     testcase
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('tab', $app->tab);

modalHeader
(
    set::title($lang->testtask->lblResults),
    set::entityText($case->title),
    set::entityID($case->id),
);

div
(
    setClass('border-b'),
    div
    (
        h::strong($lang->testcase->precondition),
    ),
    div
    (
        setClass('leading-6 h-6 my-2'),
        nl2br($case->precondition),
    ),
);

$count      = count($results);
$trs        = array();
$fileModals = array();
$trCount    = 1;
$failCount  = 0;
foreach($results as $i => $result)
{
    $class     = ($result->caseResult == 'pass' ? 'success' : ($result->caseResult == 'fail' ? 'danger' : ($result->caseResult == 'blocked' ? 'warning' : '')));
    $fileCount = '(' . count($result->files) . ')';
    if($class != 'success') $failCount ++;
    $trs[] = h::tr
    (
        setClass("result-item result{$class} h-12 is-collapsed"),
        on::click('toggleShowResults'),
        set('data-id', $result->id),
        set('data-status', $result->node > 0 && empty($result->ZTFResult) ? 'running': 'ready'),
        h::td
        (
            set('colspan', '6'),
            span(setClass('toggle-icon circle inline-block align-middle mr-2')),
            width('120px'),
            $result->date,
            $result->node > 0 ? sprintf($lang->testtask->runNode, zget($users, $result->lastRunner), $result->nodeName, $lang->testtask->runCase) : '',
            $result->node > 0 ? span
            (
                setClass('label'),
                $lang->testtask->auto
            ) : null,
            $result->node == 0 && $result->task > 0 ? html(sprintf($lang->testtask->runInTask, zget($testtasks, $result->task, ''))) : '',
            $result->node == 0 || !empty($result->ZTFResult) ? html
            (
                sprintf($lang->testtask->runCaseResult, zget($users, $result->lastRunner), zget($builds, $result->build, ''), $class, $lang->testcase->resultList[$result->caseResult])
            ) : h::strong
            (
                setClass('text-waring'),
                $lang->testtask->running,
            ),
        ),
        h::td
        (
            width('60px'),
            !empty($result->files) ? h::a
            (
                set::href("#caseResult{$result->id}"),
                set('data-toggle', 'modal'),
                $lang->files . $fileCount,
            ) : '',
        ),
        h::td
        (
            setClass('text-center'),
            width('50px'),
            icon
            (
                setClass('collapse-handle'),
                'angle-down'
            ),
        ),
    );

    $fileModals[] = modal
    (
        set::id("caseResult{$result->id}"),
        !empty($result->files) ? fileList(set::files($result->files)) : '',
    );

    $stepID = $childID = 0;
    $stepResultTrs = array();
    foreach($result->stepResults as $key => $stepResult)
    {
        if(empty($stepResult['type']))   $stepResult['type']   = 'step';
        if(empty($stepResult['parent'])) $stepResult['parent'] = 0;
        if($stepResult['type'] == 'group' || $stepResult['type'] == 'step')
        {
            $stepID ++;
            $childID = 0;
        }

        $stepClass = $stepResult['type'] == 'item' ? "step-item group-{$stepResult['parent']}" : "step-group";
        $inputName = $stepResult['type'] != 'group' ? 'stepIdList[]' : '';

        $itemTds = array();
        if($stepResult['type'] != 'group')
        {
            $modalID   = $result->id . '-' . $key;
            $fileCount = '(' . count($stepResult['files']) . ')';

            $itemTds[] = h::td
            (
                setClass('text-left'),
                isset($stepResult['expect']) ? html(nl2br($stepResult['expect'])) : '',
            );
            $itemTds[] = h::td(isset($result->version) ? html($result->version) : '');
            $itemTds[] = !empty($stepResult['result']) ? h::td
            (
                setClass("status-{$stepResult['result']} text-center"),
                $lang->testcase->resultList[$stepResult['result']],
            ) : h::td();
            $itemTds[] = !empty($stepResult['result']) ? h::td
            (
                nl2br($stepResult['real']),
            ) : h::td();
            $itemTds[] = !empty($stepResult['result']) ? h::td
            (
                setClass('text-center'),
                !empty($stepResult['files']) ? h::a
                (
                    set::href("#stepResult{$modalID}"),
                    set('data-toggle', 'modal'),
                    $lang->files . $fileCount,
                ) : '',
            ) : h::td();
            $fileModals[] = modal
            (
                set::id("stepResult{$modalID}"),
                !empty($stepResult['files']) ? fileList(set::files($stepResult['files'])) : '',
            );
        }

        $stepResultTrs[] = h::tr
        (
            setClass("step {$stepClass}"),
            set('data-parent', $stepResult['parent']),
            set('data-id', $key),
            h::td
            (
                setClass('step-id'),
                $result->caseResult == 'fail' ? div
                (
                    setClass('checkbox-primary check-item'),
                    on::click('toggleCheckChildItem'),
                    h::input
                    (
                        set::id($inputName),
                        set('type', 'checkbox'),
                        set('name', $inputName),
                        set('value', $key),
                    ),
                ) : '',
                $stepResult['type'] == 'group' ? set('colspan', '6') : '',
                div
                (
                    setClass('inputGroup'),
                    h::span
                    (
                        setClass('step-item-id mr-2'),
                        $stepResult['type'] == 'item' ? "{$stepID}.{$childID}" : $stepID,
                    ),
                    isset($stepResult['desc']) ? nl2br($stepResult['desc']) : '',
                ),
            ),
            $itemTds,
        );

        $childID ++;
    }
    $stepResultTrs[] = $result->caseResult == 'fail' && common::hasPriv('testcase', 'createBug') ? h::tr
    (
        h::td
        (
            set('colspan', '2'),
            div
            (
                setClass('checkbox-primary check-all'),
                on::click('toggleCheckAll'),
                h::input
                (
                    set::id("checkAll[{$i}]"),
                    set('type', 'checkbox'),
                    set('name', 'checkAll'),
                ),
                h::label($lang->selectAll),
            ),
        ),
        h::td
        (
            set('colspan', '4'),
        ),
        h::td
        (
            setClass('to-bug-button'),
            btn
            (
                setClass('btn h-7'),
                set::type('primary'),
                set::btnType('btnType'),
                on::click('createBug'),
                set('data-target', '_blank'),
                set('data-close-modal', true),
                $lang->testcase->createBug
            ),
        ),
    ) : '';
    $stepResultTrs[] = !empty($result->ZTFResult) && $result->node > 0 ? h::td
    (
        set('colspan', '6'),
        h::p($lang->testtask->runningLog),
        h::p($result->ZTFResult),
    ) : '';

    $projectParam   = $this->app->tab == 'project' ? "projectID={$this->session->project}," : '';
    $executionParam = $this->app->tab == 'execution' ? "executionID={$this->session->execution}" : "";
    $executionParam = isset($testtask) ? "executionID={$testtask->execution}" : $executionParam;
    $params         = isset($testtask) ? ",testtask={$testtask->id}" : "";
    $params         = $params . ',buildID=' . (isset($testtask->build) ? $testtask->build : $result->build);
    $linkParams     = "product={$case->product}&branch={$case->branch}&extras={$projectParam}caseID={$case->id},version={$case->version},resultID={$result->id},runID={$result->run}{$params}";
    if($executionParam) $params .= ',' . $executionParam;
    $trs[] = h::tr
    (
        set::id('tr-detail_' . $trCount ++),
        setClass('result-detail hidden'),
        h::td
        (
            setClass('pd-0'),
            set('colspan', '7'),
            h::form
            (
                setClass('form load-indicator form-ajax form-grid'),
                set('data-params', $linkParams),
                set('action', createLink('bug', 'create', $linkParams)),
                h::table
                (
                    setClass('table resultSteps'),
                    h::thead
                    (
                        h::tr
                        (
                            h::td
                            (
                                setClass('text-left'),
                                $lang->testcase->stepDesc,
                            ),
                            h::td
                            (
                                width('25%'),
                                setClass('text-left'),
                                $lang->testcase->stepExpect,
                            ),
                            h::td
                            (
                                width('60px'),
                                setClass('text-left'),
                                $lang->testcase->stepVersion,
                            ),
                            h::td
                            (
                                width('80px'),
                                setClass('text-center'),
                                $lang->testcase->result,
                            ),
                            h::td
                            (
                                width('100px'),
                                setClass('text-left'),
                                $lang->testcase->real,
                            ),
                            h::td
                            (
                                width('80px'),
                            ),
                        ),
                    ),
                    h::tbody
                    (
                        $stepResultTrs,
                    ),
                ),
            ),
        ),
    );
}

div
(
    div
    (
        setClass('main'),
        set::id('casesResults'),
        $case->auto != 'unit' ? formRowGroup
        (
            set::title($lang->testcase->result),
            set::items
            (
                array
                (
                    span
                    (
                        setClass('px-3 my-1 border-r'),
                        html(sprintf($lang->testtask->showResult, $count)),
                    ),
                    span
                    (
                        setClass('pl-3 my-1'),
                        html(sprintf($lang->testtask->showFail, $failCount)),
                    ),
                ),
            ),
        ) : null,
        h::table
        (
            setClass('table condensed table-hover border'),
            $trs,
        ),
        $fileModals,
    ),
);
jsVar('bugCreateParams', $linkParams);

render();

