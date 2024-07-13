<?php
declare(strict_types=1);
namespace zin;

class gantt extends wg
{
    protected static array $defineProps = array(
        'id:string',
        'ganttLang:array',
        'canEdit:bool',
        'canEditDeadline:bool',
        'ganttFields:array',
        'showChart?:bool',
        'zooming?:string',
        'options?:array'
    );

    protected static array $defaultProps = array(
        'showChart' => true,
        'zooming' => 'day'
    );

    public static function getPageCSS(): string
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): ?string
    {
        global $app;
        $currentLang = $app->getClientLang();
        $langJSFile  = $app->getWwwRoot() . 'js/dhtmlxgantt/lang/' . $currentLang . '.js';

        $js = file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
        if($currentLang != 'en' && file_exists($langJSFile)) $js .= "\nwaitGantt(function(){\n" . file_get_contents($langJSFile) . "\n});\n";
        return $js;
    }
}
