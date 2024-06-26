<?php
declare(strict_types=1);
/**
 * The formPanel widget class file of zin module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      sunhao<sunhao@easycorp.ltd>
 * @package     zin
 * @link        http://www.zentao.net
 */

namespace zin;

require_once dirname(__DIR__) . DS . 'panel' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'formgroup' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'formrow' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'form' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'formbatch' . DS . 'v1.php';

/**
 * 表单面板（formPanel）部件类。
 * The form panel widget class.
 *
 * @author Hao Sun
 */
class formPanel extends panel
{
    /**
     * Define widget properties.
     *
     * @var    array
     * @access protected
     */
    protected static array $defineProps = array(
        'class?: string="panel-form"', // 类名。
        'size?: string="lg"',                          // 额外尺寸。
        'formID?: string="$GID"',                      // 表单 ID，如果指定为 '$AUTO'，则自动生成 form-$moduleName-$methodName。
        'formClass?: string',                          // 表单样式。
        'method?: "get"|"post"="post"',                // 表单提交方式。
        'enctype?: string',                            // 表单提交类型。
        'tagName?: string="form"',                     // 表单标签名。
        'url?: string',                                // 表单提交地址。
        'actions?: array',                             // 表单操作按钮，如果不指定则使用默认行为的 “保存” 和 “返回” 按钮。
        'actionsClass?: string="form-group no-label"', // 表单操作按钮栏类名。
        'stickyActions?: array|bool=false',            // 是否固定操作按钮栏。
        'target?: string="ajax"',                      // 表单提交目标，如果是 `'ajax'` 提交则为 ajax，在禅道中除非特殊目的，都使用 ajax 进行提交。
        'submitBtnText?: string',                      // 表单提交按钮文本，如果不指定则使用 `$lang->save` 的值。
        'cancelBtnText?: string',                      // 表单取消按钮文本，如果不指定则使用 `$lang->goback` 的值。
        'items?: array',                               // 使用一个列定义对象数组来定义表单项。
        'fields?: string|array|fieldList',             // 表单字段配置。
        'fullModeOrders?: string|array',               // 完整模式下字段显示顺序。
        'layout?: string="horz"',                      // 表单布局，可选值为：'horz'、'grid' 和 `normal`。
        'labelWidth?: int',                            // 标签宽度，单位为像素。
        'batch?: bool',                                // 是否为批量操作表单。
        'shadow?: bool=false',                         // 是否显示阴影层。
        'width?: string',                              // 最大宽度。
        'modeSwitcher?: bool',                         // 是否显示表单模式按钮。
        'data?: array|object',                         // 表单项值默认数据。
        'labelData?: array|object',                    // 表单项标签默认数据。
        'loadUrl?: string',                            // 动态更新 URL。
        'autoLoad?: array',                            // 动态更新策略。
        'defaultMode?: string="lite"',                 // 默认表单模式（lite: 简洁版，full: 完整版）。
        'foldableItems?: array|string',                // 可折叠的表单项。
        'pinnedItems?: array|string',                  // 固定显示的表单项。
        'customBtn?: array|bool',                      // 是否显示表单自定义按钮。
        'customFields?: array=[]',                     // @deprecated 自定义表单项。
        'showExtra?: bool=true'                        // 是否显示工作流字段。
    );

    public static function getPageJS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    protected function created()
    {
        $fields = $this->prop('fields');
        if(is_object($fields))
        {
            global $app;
            $fields = $app->control->appendExtendFields($fields);
            $this->setProp('fields', $fields);
        }

        $customFields = $this->prop('customFields');
        if($customFields === true)
        {
            global $app, $config, $lang;
            $module = $app->rawModule;
            $method = $app->rawMethod;

            $app->loadLang($module);
            $app->loadModuleConfig($module);

            $key           = $method . 'Fields';
            $listFields    = array();
            $listFieldsKey = 'custom' . ucfirst($key);
            if(!empty($config->$module->$listFieldsKey))       $listFields = explode(',', $config->$module->$listFieldsKey);
            if(!empty($config->$module->list->$listFieldsKey)) $listFields = explode(',', $config->$module->list->$listFieldsKey);

            if(empty($listFields))
            {
                $this->setProp('customFields', array());
                return false;
            }

            $fields = array();
            foreach($listFields as $field) $fields[$field] = $lang->$module->$field;

            $showFields   = explode(',', $config->$module->custom->$key);
            $customFields = array('list' => $fields, 'show' => $showFields, 'key' => $key);

            $this->setProp('customFields', $customFields);
        }

        if($this->prop('modeSwitcher'))
        {
            global $lang;

            $modeSwitcher = btnGroup
            (
                set::size('sm'),
                btn
                (
                    setClass('gray-300-outline text-sm rounded-full btn-lite-form'),
                    $lang->liteMode
                ),
                btn
                (
                    setClass('gray-300-outline text-sm rounded-full btn-full-form'),
                    $lang->fullMode
                )
            );
            $this->addToBlock('headingActions', $modeSwitcher);
        }
    }

    protected function getHeadingActions(): array
    {
        $actions = parent::getHeadingActions();

        /* Custom fields. */
        $customFields = $this->prop('customFields', array());
        if($customFields)
        {
            global $app;
            $listFields = zget($customFields, 'list', array());
            $showFields = zget($customFields, 'show', array());
            $key        = zget($customFields, 'key', $app->rawMethod);

            if($listFields && $key)
            {
                /* Custom button submit params. */
                $urlParams        = $this->prop('customUrlParams') ? $this->prop('customUrlParams') : "module={$app->rawModule}&section=custom&key={$key}";
                $actions[] = formSettingBtn
                (
                    set::customFields(array('list' => $listFields, 'show' => $showFields)),
                    set::urlParams(zget($customFields, 'urlParams', $urlParams)),
                );
            }
        }

        return $actions;
    }

    protected function buildExtraMain()
    {
        global $app;

        $layout = $this->prop('layout');
        if($layout == 'grid') return null;

        $moduleName = $app->getModuleName();
        if($moduleName == 'caselib') $moduleName = 'lib';
        $data      = data($moduleName);
        $fields    = $app->control->appendExtendForm('info', $data);
        $extraMain = array();
        foreach($fields as $field)
        {
            $extraMain[] = formGroup
            (
                $field->control == 'file' && $data->files ? fileList
                (
                    set::files($data->files),
                    set::extra($field->field),
                    set::fieldset(false),
                    set::showEdit(true),
                    set::showDelete(true)
                ) : null,
                set::width($field->width),
                set::label($field->name),
                set::id($field->field),
                set::name($field->field),
                set::required($field->required),
                set::disabled((bool)$field->readonly),
                set::control($field->control),
                set::items($field->items),
                set::value($field->value)
            );
        }
        return $extraMain;
    }

    protected function buildExtraBatchItem()
    {
        global $app;

        $data   = data($app->getModuleName());
        $fields = $app->control->appendExtendForm('info', $data);

        $formBatchItem = array();
        foreach($fields as $field)
        {
            $formBatchItem[] = formBatchItem
            (
                set::name($field->field),
                set::label($field->name),
                set::required($field->required),
                set::control($field->control),
                set::items($field->items),
                set::width('200px')
            );
        }
        return $formBatchItem;
    }

    /**
     * Build form widget by mode.
     *
     * @access protected
     * @return node
     */
    protected function buildForm(): node
    {
        $customFields = $this->prop('customFields', array());
        $listFields   = zget($customFields, 'list', array());
        $showFields   = zget($customFields, 'show', array());
        $hiddenFields = $listFields && $showFields ? array_values(array_diff(array_keys($listFields), $showFields)) : array();
        $formID       = $this->prop('formID');

        if($this->prop('batch'))
        {
            $props = formBatch::definedPropsList();
            unset($props['id']);

            return new formBatch
            (
                set::id($formID),
                set($this->props->pick(array_keys($props))),
                $this->children(),
                $this->prop('showExtra') ? $this->buildExtraBatchItem() : null,
                set::hiddenFields($hiddenFields),
                jsVar('formBatch', true),
                $hiddenFields ? jsVar('hiddenFields', $hiddenFields) : null
            );
        }

        $props     = array_keys(form::definedPropsList());
        $formProps = array();
        foreach($props as $propName)
        {
            if($this->hasProp($propName) && $propName !== 'id') $formProps[] = $propName;
        }

        return new form
        (
            set::id($formID),
            set::className($this->prop('formClass')),
            set($this->props->pick($formProps)),
            $this->children(),
            $this->prop('showExtra') ? $this->buildExtraMain() : null,
            $hiddenFields ? jsVar('hiddenFields', $hiddenFields) : null
        );
    }

    /**
     * Build widget props.
     *
     * @access protected
     * @return array
     */
    protected function buildProps(): array
    {
        list($width, $batch, $shadow, $defaultMode) = $this->prop(array('width', 'batch', 'shadow', 'defaultMode'));
        $props = parent::buildProps();
        $props[] = setClass("is-$defaultMode");

        if($width)     $props[] = setCssVar('--zt-panel-form-max-width', $width);
        elseif($batch) $props[] = setCssVar('--zt-panel-form-max-width', 'auto');
        if($shadow)    $props[] = setClass('shadow');

        return $props;
    }

    /**
     * Build panel body.
     *
     * @access protected
     * @return node
     */
    protected function buildBody(): node
    {
        global $app;

        return div
        (
            setClass('panel-body ' . $this->prop('bodyClass')),
            set($this->prop('bodyProps')),
            $this->buildContainer($this->buildForm()),
            html($app->control->appendExtendCssAndJS())
        );
    }
}
