<?php
declare(strict_types=1);
/**
 * The roadmap view file of block module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     block
 * @link        https://www.zentao.net
 */
namespace zin;

panel
(
    set('headingClass', 'border-b'),
    set::title($block->title),
    div
    (
        '正在开发中...'
    )
);

render();

