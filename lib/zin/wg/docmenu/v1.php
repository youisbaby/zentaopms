<?php
declare(strict_types=1);
namespace zin;

class docMenu extends wg
{
    private array $modules = array();

    protected static array $defineProps = array(
        'modules: array',
        'activeKey?: int',
        'settingLink?: string',
        'closeLink: string',
        'allText?: string',
        'linkParams?: string="%s"',
        'libID?: int=0',
        'moduleID?: int=0',
    );

    public static function getPageCSS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    private function buildLink($item): string
    {
        $url = $item->url;
        if(!empty($url)) return $url;

        $linkParams = sprintf($this->linkParams, "libID={$item->id}&moduleID={$this->moduleID}");
        if(in_array($this->spaceType, array('product', 'project', 'custom'))) $linkParams = "objectID={$item->objectID}&{$linkParams}";

        $objectType = $this->objectType;

        $moduleName = $this->spaceType == 'api' ? 'api' : 'doc';
        $methodName = '';
        if($this->spaceType == 'api')
        {
            $methodName = 'index';
            $linkParams =  substr($linkParams, 1);
        }
        else if($item->type == 'annex')
        {
            $methodName = 'showFiles';
            $linkParams = "type={$objectType}&objectID={$item->objectID}";
        }
        else if(in_array($item->type, array('text', 'word', 'ppt', 'excel')))
        {
            $methodName = 'view';
            $linkParams = "docID={$this->moduleID}";
        }
        else if($objectType == 'execution')
        {
            $moduleName = 'execution';
            $methodName = 'doc';
        }
        else
        {
            $methodName = $this->spaceMethod[$objectType] ? $this->spaceMethod[$objectType] : 'teamSpace';
            if(in_array($objectType, array('mine', 'view', 'collect', 'createdby', 'editedby')))
            {
                if(in_array($item->type, array('lib', 'annex', 'api', 'execution'))) $item->id = 0;

                $type       = in_array(strtolower($item->type), array('view', 'collect', 'createdby', 'editedby')) ? strtolower($item->type) : 'mine';
                $linkParams = "type={$type}&libID={$this->libID}&moduleID={$item->id}";
            }
            if($item->type == 'apiDoc')
            {
                $linkParams = str_replace(array('browseType=&', 'param=0'), array('browseType=byrelease&', "param={$this->release}"), $linkParams);
            }
        }
        return helper::createLink($moduleName, $methodName, $linkParams);
    }

    private function buildMenuTree(array $items, int $parentID = 0): array
    {
        if(empty($items)) $items = $this->modules;
        if(empty($items)) return array();

        $parentItems = array();
        $activeKey   = $this->prop('activeKey');
        foreach($items as $setting)
        {
            $setting->parentID = $parentID;

            $item = array(
                'key'     => $setting->id,
                'text'    => $setting->name,
                'icon'    => $this->getIcon($setting),
                'url'     => $this->buildLink($setting),
                'active'  => zget($setting, 'active', $setting->id == $activeKey),
                'actions' => $this->getActions($setting)
            );

            $children = zget($setting, 'children', array());
            if(!empty($children))
            {
                $children = $this->buildMenuTree($children, $setting->id ? $setting->id : $parentID);
                $item['items'] = $children;
            }

            $parentItems[] = $item;
        }
        return $parentItems;
    }

    private function setMenuTreeProps(): void
    {
        global $app, $lang;
        $this->lang          = $lang;
        $this->rawModule     = $app->rawModule;
        $this->rawMethod     = $app->rawMethod;
        $this->currentModule = $app->moduleName;

        $this->release     = $this->prop('release', 0);
        $this->libID       = $this->prop('libID');
        $this->moduleID    = $this->prop('moduleID');
        $this->modules     = $this->prop('modules');
        $this->linkParams  = $this->prop('linkParams', '%s');
        $this->spaceType   = $this->prop('spaceType', '');
        $this->objectType   = $this->prop('objectType', '');
        $this->spaceMethod = $this->prop('spaceMethod');

        if($this->rawModule == 'api' && $this->rawMethod == 'view') $this->spaceType = 'api';
        $this->setProp('items', $this->buildMenuTree(array(), $this->libID));
    }

    private function getActions($item): array|null
    {
        if(isset($item->hasAction) && !$item->hasAction) return null;
        if(in_array($item->type, array('mine', 'view', 'collect', 'createdBy', 'editedBy'))) return null;

        $actions = $this->getOperateItems($item);
        if(empty($actions)) return null;

        return array(
            array(
                'key'      => 'more',
                'icon'     => 'ellipsis-v',
                'type'     => 'dropdown',
                'caret'    => false,
                'dropdown' => array(
                    'placement' => 'bottom-end',
                    'items'     => $actions,
                )
            )
        );
    }

    private function getOperateItems($item): array
    {
        $menus = array();
        if($item->type == 'lib')
        {
            if(hasPriv($this->currentModule, 'addCatalog'))
            {
                $menus[] = array(
                    'key'  => 'adddirectory',
                    'icon' => 'add-directory',
                    'text' => $this->lang->doc->libDropdown['addModule'],
                    'link' => '',
                );
            }

            if(hasPriv($this->currentModule, 'editCatalog'))
            {
                $menus[] = array(
                    'key'  => 'editlib',
                    'icon' => 'edit',
                    'text' => $this->lang->doc->libDropdown['editLib'],
                    'data-toggle' => 'modal',
                    'data-url'    => createlink($this->currentModule, 'editlib', "libid={$item->parentID}"),
                );
            }

            if(hasPriv($this->currentModule, 'deleteCatalog'))
            {
                $menus[] = array(
                    'key'          => 'dellib',
                    'icon'         => 'trash',
                    'text'         => $this->lang->doc->libDropdown['deleteLib'],
                    'class'        => 'ajax-submit',
                    'data-url'     => createLink($this->currentModule, 'deleteLib', "libID={$item->parentID}"),
                    'data-confirm' => $this->lang->doc->confirmDeleteLib,
                );
            }
        }
        elseif($item->type == 'doc')
        {
            if(hasPriv($this->currentModule, 'addCatalog'))
            {
                $menus[] = array(
                    'key'  => 'adddirectory',
                    'icon' => 'add-directory',
                    'text' => $this->lang->doc->libDropdown['addSameModule'],
                    'link' => '',
                );
                $menus[] = array(
                    'key'  => 'addsubdirectory',
                    'icon' => 'add-directory',
                    'text' => $this->lang->doc->libDropdown['addSubModule'],
                    'link' => '',
                );
            }

            if(hasPriv($this->currentModule, 'editCatalog'))
            {
                $menus[] = array(
                    'key'  => 'editmodule',
                    'icon' => 'edit',
                    'text' => $this->lang->doc->libDropdown['editModule'],
                    'link' => '',
                    'data-toggle' => 'modal',
                    'data-url'    => createlink($this->currentModule, 'editCatalog', "moduleID={$item->id}&type=" . ($this->rawModule == 'api' ? 'api' : 'doc')),
                );
            }

            if(hasPriv($this->currentModule, 'deleteCatalog'))
            {
                $menus[] = array(
                    'key'          => 'delmodule',
                    'icon'         => 'trash',
                    'text'         => $this->lang->doc->libDropdown['delModule'],
                    'class'        => 'ajax-submit',
                    'data-url'     => createLink($this->currentModule, 'deleteCatalog', "rootID={$item->parentID}&moduleID={$item->id}"),
                    'data-confirm' => $this->lang->api->confirmDeleteLib,
                );
            }
        }

        return $menus;
    }

    private function getIcon($item): string
    {
        $type = $item->type;
        if($type == 'apiDoc')    return 'interface-lib';
        if($type == 'lib')       return 'wiki-lib';
        if($type == 'annex')     return 'annex-lib';
        if($type == 'execution') return 'execution';
        if($type == 'text')      return 'file-text';
        if($type == 'word')      return 'file-word';
        if($type == 'ppt')       return 'file-powerpoint';
        if($type == 'excel')     return 'file-excel';
        return '';
    }

    private function getTitle(): string
    {
        global $lang;
        $activeKey = $this->prop('activeKey');

        if(empty($activeKey))
        {
            $allText = $this->prop('allText');
            if(empty($allText)) return '';
            return $allText;
        }

        foreach($this->modules as $module)
        {
            if($module->id == $activeKey) return $module->name;
        }

        return '';
    }

    private function buildBtns(): wg|null
    {
        $settingLink = $this->prop('settingLink');
        $settingText = $this->prop('settingText');
        if(!$settingLink) return null;

        global $app;
        $lang = $app->loadLang('datatable')->datatable;
        $currentModule = $app->rawModule;
        $currentMethod = $app->rawMethod;

        if(!$settingText) $settingText = $lang->moduleSetting;

        $datatableId = $app->moduleName . ucfirst($app->methodName);

        return div
        (
            setClass('col gap-2 py-3 px-7'),
            $settingLink
                ? a
                (
                    setClass('btn'),
                    setStyle('background', '#EEF5FF'),
                    setStyle('box-shadow', 'none'),
                    set('data-app', $app->tab),
                    set::href($settingLink),
                    $settingText
                )
                : null,
        );
    }

    private function buildCloseBtn(): ?wg
    {
        $activeKey = $this->prop('activeKey');
        if(empty($activeKey)) return null;

        return a
        (
            set('href', $this->prop('closeLink')),
            icon('close', setStyle('color', 'var(--color-slate-600)'))
        );
    }

    private function buildDropDownMenu()
    {
        return menu
        (
            setID('dropdownMenu'),
            set::items(array())
        );
    }

    protected function build(): wg
    {
        $this->setMenuTreeProps();
        $title = $this->getTitle();

        return div
        (
            setClass('module-menu rounded shadow-sm bg-white col rounded-sm'),
            $title ? h::header
            (
                setClass('h-10 flex items-center pl-4 flex-none gap-3'),
                span
                (
                    setClass('module-title text-lg font-semibold'),
                    html($title)
                ),
                $this->buildCloseBtn(),
            ) : null,
            h::main
            (
                setClass('col flex-auto overflow-y-auto overflow-x-hidden pl-4 pr-1'),
                zui::tree(set($this->props->pick(array('items', 'activeClass', 'activeIcon', 'activeKey', 'onClickItem', 'defaultNestedShow', 'changeActiveKey', 'isDropdownMenu'))))
            ),
            $this->buildBtns(),
            $this->buildDropDownMenu(),
        );
    }
}
