<?php
declare(strict_types=1);
namespace zin;

class label extends wg
{
    protected static array $defineProps = array(
        'text?:string'
    );

    public function onAddChild($child)
    {
        if(is_string($child) && !$this->props->has('text'))
        {
            $this->props->set('text', $child);
            return false;
        }
    }

    public function build()
    {
        return span
        (
            setClass('label'),
            set($this->getRestProps()),
            $this->prop('text'),
            $this->children()
        );
    }
}
