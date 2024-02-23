<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'btn' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'backbtn' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'actionitem' . DS . 'v1.php';

class toolbar extends wg
{
    protected static array $defineProps = array(
        'items?:array',
        'btnClass?:string',
        'btnProps?: array'
    );

    public function onBuildItem($item): node|null
    {
        if($item === null) return null;

        if(!($item instanceof item))
        {
            if($item instanceof node) return $item;
            $item = item(set($item));
        }

        $type = $item->prop('type');
        if($type === 'divider')                        return div(setClass('divider toolbar-divider'));
        if($type === 'btnGroup')                       return new btnGroup(inherit($item));
        if($type == 'dropdown' || $type == 'checkbox') return new actionItem(inherit($item));

        list($btnClass, $btnProps) = $this->prop(array('btnClass', 'btnProps'));
        $btn = empty($item->prop('back')) ? '\zin\btn' : '\zin\backBtn';
        return new $btn
        (
            setClass('toolbar-item', $btnClass),
            is_array($btnProps) ? set($btnProps) : null,
            inherit($item)
        );
    }

    protected function build()
    {
        $items = $this->prop('items');
        return div
        (
            setClass('toolbar'),
            set($this->getRestProps()),
            is_array($items) ? array_map(array($this, 'onBuildItem'), $items) : null,
            $this->children()
        );
    }
}
