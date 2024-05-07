<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'thinkradio' . DS . 'v1.php';

/**
 * 多选题型部件类
 * The thinkCheckbox widget class
 */
class thinkCheckbox extends thinkRadio
{
    protected static array $defineProps = array
    (
        'minCountName?: string="minCount"',
        'maxCountName?: string="maxCount"',
        'minCount?: string',
        'maxCount?: string',
    );

    public static function getPageJS(): string
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    protected function buildBody(): array
    {
        $items = parent::buildBody();

        list($minCountName, $minCount, $maxCountName, $maxCount) = $this->prop(array('minCountName', 'minCount', 'maxCountName', 'maxCount'));
        $items[] = formRow
        (
            setClass('gap-4'),
            formGroup
            (
                set::label(data('lang.thinkwizard.step.label.minCount')),
                setClass('selectable-rows hidden'),
                input
                (
                    set::placeholder(data('lang.thinkwizard.step.inputContent')),
                    set::type('number'),
                    set::name($minCountName),
                    set::value($minCount),
                ),
            ),
            formGroup
            (
                set::label(data('lang.thinkwizard.step.label.maxCount')),
                setClass('selectable-rows hidden'),
                input
                (
                    set::placeholder(data('lang.thinkwizard.step.inputContent')),
                    set::type('number'),
                    set::name($maxCountName),
                    set::value($maxCount)
                )
            )
        );
        return $items;
    }
}
