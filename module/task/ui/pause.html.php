<?php
declare(strict_types=1);
/**
 * The pause view file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu<liumengyi@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */

namespace zin;

/* ====== Define the page structure with zin widgets ====== */

formPanel
(
    set::id('taskPauseForm'),
    set::title($lang->task->pauseAction),
    set::headingClass('status-heading'),
    set::titleClass('form-label .form-grid'),
    set::shadow(!isAjaxRequest('modal')),
    set::actions(array('submit')),
    set::submitBtnText($lang->task->pause),
    to::headingActions
    (
        entityLabel
        (
            setClass('my-3 gap-x-3'),
            set::level(1),
            set::text($task->name),
            set::entityID($task->id),
            set::reverse(true),
        )
    ),
    formGroup
    (
        set::label($lang->comment),
        editor
        (
            set::name('comment'),
            set::rows('5'),
        )
    ),
);

h::hr(set::class('mt-6'));

history();

render();
