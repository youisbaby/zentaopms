<?php
declare(strict_types=1);
/**
 * The fixFirst view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;
to::header(
    span
    (
        set::class('article-h2'),
        $lang->execution->fixFirst,
    )
);
formPanel
(
    set::submitBtnText($lang->save),
    set::formClass('border-0'),
    formGroup
    (
        set::label($execution->begin),
        set::placeholder($lang->execution->placeholder->totalLeft),
        inputGroup
        (
            input
            (
                set::name('estimate'),
                set::value(!empty($firstBurn->estimate) ? $firstBurn->estimate : (!empty($firstBurn->left) ? $firstBurn->left : '')),
                set::placeholder($lang->execution->begin),
            ),
            checkbox
            (
                set::name('withLeft'),
                set::value(1),
                set::checked(true),
                set::text($lang->execution->fixFirstWithLeft),
                set::rootClass('ml-4 w-1/3 items-center'),
            ),
        ),
    )
);

div
(
    set::class('h-12 flex items-center bg-primary-50'),
    div
    (
        set::class('pl-4'),
        $lang->execution->totalEstimate . ': ',
        code($execution->totalEstimate),
        $lang->execution->workHour
    )
);
