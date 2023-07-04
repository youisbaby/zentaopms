<?php
declare(strict_types=1);
/**
* The UI file of story module of ZenTaoPMS.
*
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      Wang Yidong <yidong@easycorp.ltd>
* @package     story
* @link        https://www.zentao.net
*/

namespace zin;

$forceReview = $this->story->checkForceReview();

$params = $app->getParams();
array_shift($params);
jsVar('createParams', http_build_query($params));
jsVar('storyType', $type);
jsVar('feedbackSource', $config->story->feedbackSource);

formPanel
(
    on::click('#saveButton', 'customSubmit'),
    on::click('#saveDraftButton', 'customSubmit'),
    set::id('dataform'),
    set::title($lang->story->create),
    set::actions(false),
    to::headingActions
    (
        $forceReview ? checkbox(set::id('needNotReview'), set::value(1), set::text($lang->story->needNotReview), set::checked($needReview), on::change('toggleReviewer(e.target)')) : null,
    ),
    formRow
    (
        on::change('#product', 'loadProduct'),
        on::change('#branch', 'loadBranch'),
        formGroup
        (
            setClass($hiddenProduct ? 'hidden' : null),
            set::label($lang->story->product),
            set::width('1/2'),
            inputGroup
            (
                select(set::name('product'), set::value($fields['product']['default']), set::items($fields['product']['options'])),
                isset($fields['branch']) && $type != 'story' ? select(set::name('branch'), set::items($fields['branch']['options']), set::value($fields['branch']['default'])) : null,
            ),
            set::required(true),
        ),
        isset($fields['branch']) && $type == 'story' ? formGroup
        (
            set::id('assignedToBox'),
            set::label($lang->story->assignedTo),
            set::width('1/2'),
            set::name('assignedTo'),
            set::control('select'),
            set::items($fields['assignedTo']['options']),
        ) : null,
        isset($fields['branch']) && $type == 'story' ? null : formGroup
        (
            set::label($lang->story->module),
            inputGroup
            (
                span
                (
                    set::id('moduleIdBox'),
                    select(set::name('module'), set::items($fields['module']['options']), set::value($fields['module']['default']), set::required(true)),
                ),
                empty($fields['module']['options']) ? btn(set::url($this->createLink('tree', 'browse', "rootID=$productID&view=story&currentModuleID=0&branch=$branch")), setClass('primary'), set('data-toggle', 'modal'), $lang->tree->manage) : null,
                empty($fields['module']['options']) ? btn(set('data-on', 'click'), set('data-call', 'loadProductModules'), set('data-param', $productID), setClass('refresh'), icon('refresh')) : null,
            )
        ),
    ),
    isset($fields['branches']) && $type == 'story' ? formRow
    (
        setClass('switchBranch'),
        formGroup
        (
            set::width('1/4'),
            set::label(sprintf($lang->product->branch, $lang->product->branchName[$product->type])),
            inputGroup
            (
                set::id('branchBox'),
                select
                (
                    set::name('branches[0]'),
                    set::items($fields['branches']['options']),
                    set::value($fields['branches']['default']),
                    set('data-index', '0'),
                    set('data-on', 'change'),
                    set('data-call', 'loadBranchRelation'),
                    set('data-params', 'event'),
                )
            ),
        ),
        formGroup
        (
            set::width('1/4'),
            set::label($lang->story->module),
            inputGroup
            (
                set::id('moduleIdBox'),
                select(set::name('modules[0]'), set::items($fields['modules']['options']), set::value($fields['modules']['default']), set::required(true))
            ),
        ),
        formGroup
        (
            set::label($lang->story->plan),
            inputGroup
            (
                set::id('planIdBox'),
                select(set::name('plans[0]'), set::items($fields['plans']['options']), set::value($fields['plans']['default']))
            ),
        ),
        count($branches) > 1 ? formGroup
        (
            set::width('50px'),
            setClass('c-actions'),
            btn(setClass('btn-link addNewLine'), set('data-on', 'click'), set('data-call', 'addBranchesBox'), set('data-params', 'event'), set::title(sprintf($lang->story->addBranch, $lang->product->branchName[$product->type])), icon('plus')),
        ) : null,
    ) : null,
    isset($fields['branches']) && $type == 'story' ? formRow
    (
        set::id('storyNoticeBranch'),
        setClass('hidden'),
        formGroup
        (
            set::label(' '),
            div(setClass('text-gray'), icon(setClass('text-warning'), 'help'), set::style(array('font-size' => '12px')), $lang->story->notice->branch),
        )
    ) : null,
    !isset($fields['branch']) && $type == 'story' ? formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->story->planAB),
            inputGroup
            (
                span
                (
                    set::id('planIdBox'),
                    select(set::name('plan'), set::items($fields['plan']['options']), set::value($fields['plan']['default'])),
                ),
                empty($fields['plan']['options']) ? btn(set::url($this->createLink('productplan', 'create', "productID=$productID&branch=$branch")), set('data-toggle', 'modal'), set::title($lang->productplan->create), icon('plus')) : null,
                empty($fields['plan']['options']) ? btn(setClass('refresh'), set('data-toggle', 'modal'), set::title($lang->refresh), set('data-on', 'click'), set('data-call', 'loadProductPlans'), set('data-params', $productID), icon('refresh')) : null,
            ),
        ),
        formGroup
        (
            set::width('1/2'),
            set::id('assignedToBox'),
            set::name('assignedTo'),
            set::control('select'),
            set::label($lang->story->assignedTo),
            set::items($fields['assignedTo']['options']),
            set::value($fields['assignedTo']['default']),
        ),
    ) : null,
    $type == 'story' ? formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->story->source),
            set::name('source'),
            set::control('select'),
            set::items($fields['source']['options']),
            set::value($fields['source']['default']),
            on::change('toggleFeedback(e.target)'),
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->story->sourceNote),
            set::control('text'),
            set::name('sourceNote'),
            set::value($fields['sourceNote']['default']),
        ),
        formGroup
        (
            setClass($showFeedbackBox ? '' : ' hidden'),
            set::width('1/2'),
            setClass('feedbackBox'),
            set::label($lang->story->feedbackBy),
            set::control('text'),
            set::name('feedbackBy'),
            set::value($fields['feedbackBy']['default']),
        ),
        formGroup
        (
            setClass($showFeedbackBox ? '' : ' hidden'),
            set::width('1/2'),
            setClass('feedbackBox'),
            set::label($lang->story->notifyEmail),
            set::control('text'),
            set::name('notifyEmail'),
            set::value($fields['notifyEmail']['default']),
        ),
    ) : null,
    $type != 'story' ? formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->story->assignedTo),
            inputGroup
            (
                set::id('assignedToBox'),
                select(set::name('assignedTo'), set::items($fields['assignedTo']['options']), set::value($fields['assignedTo']['value']))
            )
        ),
        formGroup
        (
            set::width('1/4'),
            set::label($lang->story->source),
            select(set::name('source'), set::items($fields['source']['options']), set::value($fields['source']['value']))
        ),
        formGroup
        (
            set::width('1/4'),
            set::label($lang->story->sourceNote),
            input(set::name('sourceNote'), set::value($fields['sourceNote']['value']))
        ),
    ) : null,
    formGroup
    (
        set::width('1/2'),
        set::label($lang->story->reviewedBy),
        set::required($forceReview),
        inputGroup
        (
            set::id('reviewerBox'),
            select
            (
                set::name('reviewer[]'),
                set::multiple(true),
                set::items($fields['reviewer']['options']),
                set::value($fields['reviewer']['default']),
            ),
        ),
        $forceReview ? null : formHidden('needNotReview', 1),
    ),
    isset($fields['URS']) ? formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->story->requirement),
            inputGroup
            (
                span(setClass('URSBox'), select(set::name('URS[]'), set::items($fields['URS']['options']), set::value($fields['URS']['value']))),
                btn(set('data-on', 'click'), set('data-call', 'loadURS'), set('data-params', 'allURS'), $lang->story->loadAllStories),
            )
        ),
        formGroup
        (
            set::width('1/2'),
            setClass($hiddenParent ? 'hidden' : null),
            set::label($lang->story->parent),
            set::control('select'),
            set::name('parent'),
            set::items($fields['parent']['options']),
            set::value($fields['parent']['value']),
        )
    ) : null,
    $type == 'story' && !$this->config->URAndSR ? formRow
    (
        setClass($hiddenParent ? 'hidden' : null),
        formGroup
        (
            set::label($lang->story->parent),
            set::control('select'),
            set::name('parent'),
            set::items($fields['parent']['options']),
            set::value($fields['parent']['value']),
        )
    ) : null,
    isset($executionType) && $executionType == 'kanban' ? formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($fields['region']['title']),
            set::name('region'),
            set::control('select'),
            set::items($fields['region']['options']),
            set::value($fields['region']['default']),
            set('data-on', 'change'),
            set('data-call', 'setLane'),
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($fields['lane']['title']),
            set::name('lane'),
            set::control('select'),
            set::items($fields['lane']['options']),
            set::value($fields['lane']['default']),
        )
    ) : null,
    formRow
    (
        formGroup
        (
            set::width('3/4'),
            set::label($lang->story->title),
            set::required($fields['title']['required']),
            input(set::name('title'), set::value($fields['title']['default'])),
        ),
        formGroup
        (
            setClass('no-background'),
            inputGroup
            (
                $lang->story->category,
                select(set::name('category'), set::items($fields['category']['options']), set::value($fields['category']['default'])),
                $lang->story->pri,
                select(set::name('pri'), set::items($fields['pri']['options']), set::value($fields['pri']['default'])),
                $lang->story->estimateAB,
                input(set::name('estimate'), set::placeholder($lang->story->hour), set::value($fields['estimate']['default'])),
            )
        ),
    ),
    formGroup
    (
        set::label($lang->story->spec),
        set::control('editor'),
        set::name('spec'),
        set::placeholder(htmlSpecialString($lang->story->specTemplate . "\n" . $lang->noticePasteImg)),
        set::value($fields['spec']['default']),
        set::required($fields['spec']['required']),
    ),
    formGroup
    (
        set::label($lang->story->verify),
        set::control('editor'),
        set::name('verify'),
        set::value($fields['verify']['default']),
        set::required($fields['verify']['required']),
    ),
    formGroup
    (
        set::id('uploadFiles'),
        set::label($lang->story->legendAttatch),
        upload(set::name('files[]'), set::draggable(true), set::tip(sprintf($lang->noticeDrag, strtoupper(ini_get('upload_max_filesize'))))),
    ),
    formGroup
    (
        set::label($lang->story->mailto),
        set::name('mailto[]'),
        set::control(array('type' => 'select', 'multiple' => true)),
        set::items($fields['mailto']['options']),
        set::value($fields['mailto']['default']),
    ),
    formGroup
    (
        set::label($lang->story->keywords),
        set::name('keywords'),
        set::control('input'),
        set::values($fields['keywords']['default']),
    ),
    formHidden('type', $type),
    formRow
    (
        setClass('form-actions form-group no-label'),
        btn(setClass('primary'), set::id('saveButton'), $lang->save),
        btn(setClass('secondary'), set::id('saveDraftButton'), $lang->story->saveDraft),
        backBtn($lang->goback),
    ),
);

isset($fields['branches']) && $type == 'story' ? formRow
(
    set::id('addBranchesBox'),
    setClass('hidden'),
    formGroup
    (
        set::width('1/4'),
        set::label(sprintf($lang->product->branch, $lang->product->branchName[$product->type])),
        inputGroup
        (
            set::id('branchBox'),
            select
            (
                set::name('branches[%i%]'),
                set::items($fields['branches']['options']),
                set::value($fields['branches']['default']),
            )
        ),
    ),
    formGroup
    (
        set::width('1/4'),
        set::label($lang->story->module),
        inputGroup
        (
            set::id('moduleIdBox'),
            select(set::name('modules[%i%]'), set::items($fields['modules']['options']), set::value($fields['modules']['default']))
        ),
    ),
    formGroup
    (
        set::label($lang->story->plan),
        inputGroup
        (
            set::id('planIdBox'),
            select(set::name('plans[%i%]'), set::items($fields['plans']['options']), set::value($fields['plans']['default']))
        ),
    ),
    formGroup
    (
        set::width('50px'),
        setClass('c-actions'),
        btn(setClass('btn-link addNewLine'),    set::title(sprintf($lang->story->addBranch,    $lang->product->branchName[$product->type])), icon('plus')),
        btn(setClass('btn-link removeNewLine'), set::title(sprintf($lang->story->deleteBranch, $lang->product->branchName[$product->type])), icon('trash')),
    ),
) : null;

render();
