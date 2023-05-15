<?php
declare(strict_types=1);
namespace zin;

class commentBtn extends btn
{
    static $defineProps = array(
        'dataTarget?:string',
        'dataUrl?:string',
        'dataType?:string',
        'icon?:string',
        'iconClass?:string',
        'text?:string',
        'square?:bool',
        'disabled?:bool',
        'active?:bool',
        'url?:string',
        'target?:string',
        'size?:string|int',
        'trailingIcon?:string',
        'trailingIconClass?:string',
        'caret?:string|bool',
        'hint?:string',
        'type?:string',
        'btnType?:string'
    );

    protected function getProps(): array
    {
        $dataTarget = $this->prop('dataTarget');
        $dataUrl    = $this->prop('dataUrl');
        $dataType   = $this->prop('dataType');
        $props      = parent::getProps();

        $props['data-toggle']    = 'modal';
        $props['data-type']      = $dataType;
        $props['data-url']       = $dataUrl;
        $props['data-target']    = $dataTarget;

        return $props;
    }
}
