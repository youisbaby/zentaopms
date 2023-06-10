<?php
declare(strict_types=1);
/**
 * The stroy view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      dingguodong <dingguodong@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */

namespace zin;

/* Show feature bar. */
featureBar
(
    set::current($type),
    set::link(createLink($app->rawModule, $app->rawMethod, "&executionID=$executionID&storyType=$storyType&orderBy=$orderBy&type={key}")),
    li(searchToggle(set::module('story')))
);

/* Build create story button. */
$fnBuildCreateStoryButton = function() use ($lang, $product, $storyType, $productID)
{
    if(!common::canModify('product', $product)) return null;

    $createLink      = createLink('story', 'create', "product=$productID&branch=0&moduleID=0&storyID=0&objectID=$executionID&bugID=0&planID=0&todoID=0&extra=&storyType=$storyType");
    $batchCreateLink = createLink('story', 'batchCreate', "productID=$productID&branch=0&moduleID=0&storyID=0&executionID=$executionID&plan=0&storyType=$storyType");

    $createBtnLink  = '';
    $createBtnTitle = '';
    if(hasPriv($storyType, 'create'))
    {
        $createBtnLink  = $createLink;
        $createBtnTitle = $lang->story->create;
    }
    elseif(hasPriv($storyType, 'batchCreate'))
    {
        $createBtnLink  = empty($productID) ? '' : $batchCreateLink;
        $createBtnTitle = $lang->story->batchCreate;
    }

    /* Without privilege, don't render create button. */
    if(empty($createBtnLink)) return null;

    if(!empty($productID) && hasPriv($storyType, 'batchCreate') && hasPriv($storyType, 'create'))
    {
        $items = array();

        if(commonModel::isTutorialMode())
        {
            /* Tutorial create link. */
            $wizardParams = helper::safe64Encode("productID=$productID&branch=0&moduleID=0");
            $link = $this->createLink('tutorial', 'wizard', "module=story&method=create&params=$wizardParams");
            $items[] = array('text' => $lang->story->createCommon, 'url' => $link);
        }
        else
        {
            $items[] = array('text' => $lang->story->create, 'url' => $createLink);
        }

        $items[] = array('text' => $lang->story->batchCreate, 'url' => $batchCreateLink);

        return dropdown
        (
            icon('plus'),
            $createBtnTitle,
            span(setClass('caret')),
            setClass('btn secondary'),
            set::items($items),
        );
    }

    return item(set(array
    (
        'text'  => $createBtnTitle,
        'icon'  => 'plus',
        'type'  => 'dropdown',
        'class' => 'secondary',
        'url'   => $createBtnLink
    )));
};

/* Build link story button. */
$fnBuildLinkStoryButton = function() use($lang, $product, $productID)
{
    if(!common::canModify('product', $product)) return null;

    /* Tutorial mode. */
    if(commonModel::isTutorialMode())
    {
        $wizardParams = helper::safe64Encode("project={$execution->id}");

        return item(set(array
        (
            'text' => $lang->project->linkStory,
            'url'  => createLink('tutorial', 'wizard', "module=project&method=linkStory&params=$wizardParams")
        )));
    }

    $buttonLink  = '';
    $buttonTitle = '';
    $dataToggle  = '';
    if(common::hasPriv('projectstory', 'importPlanStories'))
    {
        $buttonLink  = empty($productID) ? '' : '#linkStoryByPlan';
        $buttonTitle = $lang->execution->linkStoryByPlan;
        $dataToggle  = 'data-toggle="modal"';
    }
    if(common::hasPriv('projectstory', 'linkStory'))
    {
        $buttonLink  = $this->createLink('projectstory', 'linkStory', "project=0");
        $buttonTitle = $lang->execution->linkStory;
        $dataToggle  = '';
    }

    if(empty($buttonLink)) return null;

    if(!empty($productID) && common::hasPriv('projectstory', 'linkStory') && common::hasPriv('projectstory', 'importPlanStories'))
    {
        $items = array();
        $items[] = array('text' => $lang->execution->linkStory,       'url' => createLink('projectstory', 'linkStory', "project=0"));
        $items[] = array('text' => $lang->execution->linkStoryByPlan, 'url' => '#linkStoryByPlan', 'data-toggle' => $dataToggle);

        return dropdown
        (
            icon('link'),
            $buttonTitle,
            span(setClass('caret')),
            setClass('btn primary'),
            set::items($items),
        );
    }

    return null;
};

/* Show tool bar. */
toolbar
(
    item(set(array
    (
        'text' => $lang->story->report->common,
        'icon' => 'common-report icon-bar-chart muted',
        'class' => 'ghost'
    ))),
    item(set(array
    (
        'text'  => $lang->story->export,
        'icon'  => 'export',
        'class' => 'ghost',
        'url'   => createLink('story', 'export', "productID=$productID&orderBy=$orderBy&executionID=$executionID&browseType=$type&storyType=$storyType"),
    ))),
    $fnBuildCreateStoryButton(),
    $fnBuildLinkStoryButton()
);


/* DataTable columns. */
$setting = $this->datatable->getSetting('story');
$cols    = array_values($setting);
foreach($cols as $key => $col)
{
    $col->name  = $col->id;
    if($col->id == 'title')
    {
        $col->link = sprintf($col->link, createLink('story', 'view', array('storyID' => '${row.id}', 'version' => '0', 'param' => '0', 'storyType' => $storyType)));
    }

    $cols[$key] = $col;
}


/* DataTable data. */
$this->loadModel('story');

$data = array();
foreach($stories as $story)
{
    $story->taskCount = $storyTasks[$story->id];
    $story->actions   = $this->story->buildActionButtonList($story, 'browse');
    $story->plan      = isset($story->planTitle) ? $story->planTitle : $plans[$story->plan];

    $data[] = $story;

    if(!isset($story->children)) continue;

    /* Children. */
    foreach($story->children as $key => $child)
    {
        $child->taskCount = $storyTasks[$child->id];
        $child->actions   = $this->story->buildActionButtonList($child, 'browse');

        $data[] = $child;
    }
}

dtable
(
    set::userMap($users),
    set::customCols(true),
    set::groupDivider(true),
    set::cols($cols),
    set::data($data),
    set::className('shadow rounded'),
    set::footPager(usePager()),
    set::nested(true),
    set::footer(jsRaw("function(){return window.footerGenerator.call(this, '{$summary}');}"))
);

render();

