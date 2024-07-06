<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'btn' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'sidebar' . DS . 'v1.php';
class thinkStepMenu extends wg
{
    private array $modules = array();

    protected static array $defineProps = array(
        'modules: array',
        'activeKey?: int',
        'hover?: bool=true',
        'showAction?: bool=true',
        'toggleNonNodeShow?: bool=false',
        'checkbox?: bool',
        'preserve?: string|bool',
        'checkOnClick?: bool|string',
        'defaultNestedShow?: bool=true',
        'hidden?: bool=false',
        'onCheck?: function',
        'sortable?: array',
        'onSort?: function'
    );

    public static function getPageCSS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    private function buildMenuTree(array $items, int $parentID = 0): array
    {
        jsVar('from', data('from') ?? '');
        if(empty($items)) $items = $this->modules;
        if(empty($items)) return array();

        $activeKey         = $this->prop('activeKey');
        $toggleNonNodeShow = $this->prop('toggleNonNodeShow');
        $hidden            = $this->prop('hidden');
        $sortTree          = $this->prop('sortable') || $this->prop('onSort');
        $parentItems       = array();
        foreach($items as $setting)
        {
            if(!is_object($setting)) continue;

            $canView     = common::hasPriv('thinkstep', 'view');
            $unClickable = $toggleNonNodeShow && $setting->id != $activeKey && $setting->type != 'node' && json_decode($setting->answer) == null;
            $item        = array(
                'key'         => $setting->id,
                'text'        => $setting->title,
                'hint'        => $unClickable ? $this->lang->thinkrun->error->unanswered :$setting->title,
                'url'         => $unClickable || !$canView ? '' : $setting->url,
                'data-id'     => $setting->id,
                'data-type'   => $setting->type,
                'data-parent' => $setting->parent,
                'data-level'  => $setting->grade,
                'selected'    => $setting->id == $activeKey,
                'disabled'    => $unClickable,
                'actions'     => $this->prop('showAction') ? $this->getActions($setting) : null,
                'data-wizard' => $setting->wizard,
                'class'       => $unClickable && $hidden ? 'hidden': ''
            );

            if($sortTree) $item['trailingIcon'] = 'move muted cursor-move';
            $children = zget($setting, 'children', array());
            if(!empty($children))
            {
                $children = $this->buildMenuTree($children, $setting->id);
                $item['items'] = $children;
            }

            $parentItems[] = $item;
        }
        return $parentItems;
    }

    private function setMenuTreeProps(): void
    {
        global $app, $lang;
        $this->lang    = $lang;
        $this->modules = $this->prop('modules');
        $app->loadLang('thinkstep');

        $untitledLangs = array('transition' => $lang->thinkstep->untitled . $lang->thinkstep->transition, 'question' => $lang->thinkstep->untitled . $lang->thinkstep->question);
        jsVar('untitledLangs', $untitledLangs);

        $this->setProp('items', $this->buildMenuTree(array(), 0));
    }

    private function getActions($item): array
    {
        $canCreate = common::hasPriv('thinkstep', 'create');
        $canEdit   = common::hasPriv('thinkstep', 'edit');
        $canDelete = common::hasPriv('thinkstep', 'delete');
        if(!$canCreate && !$canEdit && !$canDelete) return array();

        $actions = array();
        $moreBtn = $this->getOperateItems($item);
        $actions[] = array(
            'key'      => 'more',
            'icon'     => 'ellipsis-v',
            'type'     => 'dropdown',
            'caret'    => false,
            'dropdown' => array(
                'placement' => 'bottom-start',
                'items'     => $moreBtn
            )
        );

        return $actions;
    }

    private function getOperateItems($item): array
    {
        $canAddChild        = true;
        $showQuestionOfNode = true;
        if(!empty($item->children))
        {
            foreach($item->children as $child)
            {
                if($canAddChild && $child->type == 'question')    $canAddChild        = false;
                if($showQuestionOfNode && $child->type == 'node') $showQuestionOfNode = false;
            }
        }

        $canCreate   = common::hasPriv('thinkstep', 'create');
        $canEdit     = common::hasPriv('thinkstep', 'edit');
        $canDelete   = common::hasPriv('thinkstep', 'delete');
        $parentID    = $item->type != 'node' ? $item->parent : $item->id;
        $confirmTips = $this->lang->thinkstep->deleteTips[$item->type];

        $menus            = array();
        $transitionAction = array();
        if($canCreate)
        {
            if($item->type == 'node') $menus[] = array(
                'key'     => 'addNode',
                'icon'    => 'add-chapter',
                'text'    => $this->lang->thinkstep->actions['sameNode'],
                'onClick' => jsRaw("() => addNode({$item->id}, 'same')")
            );
            if($item->grade != 3 && $item->type == 'node' && $canAddChild) $menus[] = array(
                'key'     => 'addNode',
                'icon'    => 'add-sub-chapter',
                'text'    => $this->lang->thinkstep->actions['childNode'],
                'onClick' => jsRaw("() => addNode({$item->id}, 'child')")
            );

            $transitionAction[] = array('type' => 'divider');
            $transitionAction[] = array(
                'key'     => 'transition',
                'icon'    => 'transition',
                'text'    => $this->lang->thinkstep->actions['transition'],
                'onClick' => jsRaw("() => addQuestion({$item->id}, {$parentID}, 'transition')"),
            );
        }

        $marketID = data('marketID');
        $menus = array_merge($menus, array(
            $canEdit ? array(
                'key'  => 'editNode',
                'icon' => 'edit',
                'text' => $this->lang->thinkstep->actions['edit'],
                'url'  => createLink('thinkstep', 'edit', "marketID={$marketID}&stepID={$item->id}")
            ) : null,
            $canDelete ? (!$item->existNotNode ? array(
                'key'          => 'deleteNode',
                'icon'         => 'trash',
                'text'         => $this->lang->thinkstep->actions['delete'],
                'innerClass'   => 'ajax-submit',
                'data-url'     => createLink('thinkstep', 'delete', "marketID={$marketID}&stepID={$item->id}"),
                'data-confirm' => $confirmTips,
            ) : array(
                'key'            => 'deleteNode',
                'icon'           => 'trash',
                'text'           => $this->lang->thinkstep->actions['delete'],
                'innerClass'     => 'text-gray opacity-50',
                'data-toggle'    => 'tooltip',
                'data-title'     => $this->lang->thinkstep->cannotDeleteNode,
                'data-placement' => 'right',
            )) : null
        ), $transitionAction);

        if($canCreate && (($showQuestionOfNode && $item->type == 'node') || $item->hasSameQuestion || $item->type == 'question')) $menus = array_merge($menus, array(
            array('type' => 'divider'),
            array(
                'key'     => 'radio',
                'icon'    => 'radio',
                'text'    => $this->lang->thinkstep->actions['radio'],
                'onClick' => jsRaw("() => addQuestion({$item->id}, {$parentID}, 'question', 'radio')"),
            ),
            array(
                'key'     => 'checkbox',
                'icon'    => 'checkbox',
                'text'    => $this->lang->thinkstep->actions['checkbox'],
                'onClick' => jsRaw("() => addQuestion({$item->id}, {$parentID}, 'question', 'checkbox')"),
            ),
            array(
                'key'     => 'input',
                'icon'    => 'input',
                'text'    => $this->lang->thinkstep->actions['input'],
                'onClick' => jsRaw("() => addQuestion({$item->id}, {$parentID}, 'question', 'input')"),
            ),
            array(
                'key'     => 'tableInput',
                'icon'    => 'cell-input',
                'text'    => $this->lang->thinkstep->actions['tableInput'],
                'onClick' => jsRaw("() => addQuestion({$item->id}, {$parentID}, 'question', 'tableInput')"),
            ),
        ));
        return $menus;
    }

    private function buildActions(): node
    {
        return btn
        (
            set::type('ghost'),
            setClass('text-gray absolute top-2 right-3 z-10 toggle-btn'),
            set::icon('fold-all'),
            on::click('toggleQuestionShow'),
        );
    }

    protected function build(): array
    {
        $this->setMenuTreeProps();
        $treeProps   = $this->props->pick(array('items', 'activeClass', 'activeIcon', 'activeKey', 'onClickItem', 'defaultNestedShow', 'changeActiveKey', 'isDropdownMenu', 'checkbox', 'checkOnClick', 'onCheck', 'sortable', 'onSort'));
        $isInSidebar = $this->parent instanceof sidebar;
        $treeType    = (!empty($treeProps['onSort']) || !empty($treeProps['sortable'])) ? 'sortableTree' : 'tree';

        return array
        (
            div
            (
                setClass('think-node-menu rounded bg-white col bg-canvas ml-4 pb-3 h-full'),
                zui::$treeType
                (
                    set::_tag('menu'),
                    set::defaultNestedShow(true),
                    set::hover(true),
                    set::collapsedIcon('caret-right cursor-pointer'),
                    set::expandedIcon('caret-down cursor-pointer'),
                    set::className('tree-lines bg-canvas col flex-auto scrollbar-hover scrollbar-thin overflow-y-auto overflow-x-hidden'),
                    set($treeProps)
                ),
                $isInSidebar ? array
                (
                    $this->buildActions(),
                    row
                    (
                        setClass('w-full h-10 justify-end p-1 absolute bottom-0 right-0 pr-4 z-10 bg-canvas'),
                        btn
                        (
                            set::type('ghost'),
                            set::size('sm'),
                            set::icon('menu-arrow-left text-gray'),
                            set::hint($this->lang->collapse),
                            on::click('hideSidebar')
                        )
                    ),
                    h::js("$('#mainContainer').addClass('has-sidebar');$('#mainContainer .sidebar').addClass('relative');")
                ) : null
            ),
        );
    }
}
