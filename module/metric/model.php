<?php
/**
 * The model file of metric module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      zhouxin <zhouxin@easycorp.ltd>
 * @package     metric
 * @version     $Id: model.php 5145 2013-07-15 06:47:26Z zhouxin@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class metricModel extends model
{
    /**
     * 用对象数据创建度量。
     * Create a metric.
     *
     * @param  object  $metric
     * @param  string  $back
     * @access public
     * @return int|false
     */
    public function create($metric)
    {
        $this->dao->insert(TABLE_METRIC)->data($metric)
            ->autoCheck()
            ->checkIF(!empty($metric->name), 'name', 'unique', "`deleted` = '0'")
            ->checkIF(!empty($metric->code), 'code', 'unique', "`deleted` = '0'")
            ->exec();
        if(dao::isError()) return false;
        $metricID = $this->dao->lastInsertID();

        $this->loadModel('action')->create('metric', $metricID, 'opened', '', '', $this->app->user->account);

        return $metricID;
    }

    /**
     * 获取度量项数据列表。
     * Get metric data list.
     *
     * @param  string $type
     * @param  int    $queryID
     * @param  string $sort
     * @param  object $pager
     * @access public
     * @return array|false
     */
    public function getList($scope, $stage, $param, $type = '', $queryID = 0, $sort = '', $pager = null)
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('metricQuery', $query->sql);
                $this->session->set('metricForm', $query->form);
            }
            else
            {
                $this->session->set('metricQuery', ' 1 = 1');
            }
        }

        $object = null;
        $purpose = null;
        if($type == 'byTree')
        {
            $object_purpose = explode('_', $param);
            $object = $object_purpose[0];
            if(count($object_purpose) == 2) $purpose = $object_purpose[1];
        }

        $metrics = $this->metricTao->fetchMetrics($scope, $stage, $object, $purpose, $this->session->metricQuery, $sort, $pager);

        return $metrics;
    }

    /**
     * 获取模块树数据。
     * Get module tree data.
     *
     * @param string  $scope
     * @access public
     * @return void
     */
    public function getModuleTreeList($scope)
    {
        return $this->metricTao->fetchModules($scope);
    }

    /**
     * 根据代号获取度量项信息。
     * Get metric info by code.
     *
     * @param  string       $code
     * @param  string|array $fieldList
     * @access public
     * @return object|false
     */
    public function getByCode(string $code, string|array $fieldList = '*')
    {
        if(is_array($fieldList)) $fieldList = implode(',', $fieldList);
        return $this->dao->select($fieldList)->from(TABLE_METRIC)->where('code')->eq($code)->fetch();
    }

    /**
     * 根据ID获取度量项信息。
     * Get metric info by id.
     *
     * @param  int          $metricID
     * @param  string|array $fieldList
     * @access public
     * @return object|false
     */
    public function getByID(int $metricID, string|array $fieldList = '*')
    {
        if(is_array($fieldList)) $fieldList = implode(',', $fieldList);
        return $this->dao->select($fieldList)->from(TABLE_METRIC)->where('id')->eq($metricID)->fetch();
    }

    /**
     * 获取度量项数据源句柄。
     * Get data source statement of calculator.
     *
     * @param  object $calculator
     * @access public
     * @return PDOStatement|string
     */
    public function getDataStatement($calculator, $returnType = 'statement')
    {
        if(!empty($calculator->dataset))
        {
            include_once $this->metricTao->getDatasetPath();

            $dataset    = new dataset($this->dao);
            $dataSource = $calculator->dataset;
            $fieldList  = implode(',', $calculator->fieldList);

            $statement = $dataset->$dataSource($fieldList);
            $sql       = $dataset->dao->get();
        }
        else
        {
            $calculator->setDAO($this->dao);

            $statement = $calculator->getStatement();
            $sql       = $calculator->dao->get();
        }

        return $returnType == 'sql' ? $sql : $statement;
    }

    /**
     * 根据度量项信息生成度量项php模板内容。
     * Generante php template content from metric information.
     *
     * @param  int    $metricID
     * @access public
     * @return string
     */
    public function getMetricPHPTemplate(int $metricID): string
    {
        $metric = $this->getByID($metricID);

        $metric->nameEN  = ucfirst(str_replace('_', ' ', $metric->code));
        $metric->scope   = $this->lang->metric->scopeList[$metric->scope];
        $metric->object  = $this->lang->metric->objectList[$metric->object];
        $metric->purpose = $this->lang->metric->purposeList[$metric->purpose];

        $replaceFields = array('name', 'nameEN', 'code', 'scope', 'object', 'purpose', 'unit', 'desc', 'definition');

        $content = file_get_contents($this->app->getModuleRoot() . DS . 'metric' . DS . 'template' . DS . 'metric.php.tmp');

        foreach($replaceFields as $replaceField)
        {
            $replaceContent = str_replace("\n", ';', $metric->$replaceField);
            $content = str_replace("{{{$replaceField}}}", $replaceContent, $content);
        }

        return $content;
    }

    /**
     * 根据代号获取计算实时度量项的结果。
     * Get result of calculate metric by code.
     *
     * @param  string $code
     * @param  array  $options e.g. array('product' => '1,2,3', 'year' => '2023')
     * @access public
     * @return array
     */
    public function getResultByCode($code, $options = array())
    {
        $metric = $this->dao->select('id,code,scope,purpose')->from(TABLE_METRIC)->where('code')->eq($code)->fetch();
        if(!$metric) return false;

        $calcPath = $this->metricTao->getCalcRoot() . $metric->scope . DS . $metric->purpose . DS . $metric->code . '.php';
        if(!is_file($calcPath)) return false;

        include_once $this->metricTao->getBaseCalcPath();
        include_once $calcPath;
        $calculator = new $metric->code;

        $statement = $this->getDataStatement($calculator);
        $rows = $statement->fetchAll();

        foreach($rows as $row) $calculator->calculate($row);
        return $calculator->getResult($options);
    }

    /**
     * 根据代号列表批量获取度量项的结果。
     * Get result of calculate metric by code list.
     *
     * @param  array $codes   e.g. array('code1', 'code2')
     * @param  array $options e.g. array('product' => '1,2,3', 'year' => '2023')
     * @access public
     * @return array
     */
    public function getResultByCodes($codes, $options = array())
    {
        $results = array();
        foreach($codes as $code)
        {
            $result = $this->getResultByCode($code, $options);
            if($result) $results[$code] = $result;
        }

        return $results;
    }

    /**
     * 获取可计算的度量项列表。
     * Get executable metric list.
     *
     * @access public
     * @return array
     */
    public function getExecutableMetric()
    {
        $currentWeek = date('w');
        $currentDay  = date('d');
        $now         = date('H:i');

        $metricList = $this->dao->select('id,code,time')
            ->from(TABLE_METRIC)
            ->where('deleted')->eq('0')
            ->fetchAll();

        $excutableMetrics = array();
        foreach($metricList as $metric)
        {
            $excutableMetrics[$metric->id] = $metric->code;
        }
        return $excutableMetrics;
    }

    /**
     * insertmetricLib
     *
     * @param  int    $records
     * @access public
     * @return void
     */
    public function insertmetricLib($records)
    {
        $this->dao->begin();
        foreach($records as $record)
        {
            $this->dao->insert(TABLE_METRICLIB)
                ->data($record)
                ->exec();
        }
        $this->dao->commit();

        return dao::isError();
    }

    /**
     * 获取可计算的度量项对象列表。
     * Get executable calculator list.
     *
     * @access public
     * @return array
     */
    public function getExecutableCalcList()
    {
        $funcRoot = $this->metricTao->getCalcRoot();

        $fileList = array();
        foreach($this->config->metric->scopeList as $scope)
        {
            foreach($this->config->metric->purposeList as $purpose)
            {
                $pattern = $funcRoot . $scope . DS . $purpose . DS . '*.php';
                $matchedFiles = glob($pattern);
                if($matchedFiles !== false) $fileList = array_merge($fileList, $matchedFiles);
            }
        }

        $calcList = array();
        $excutableMetric = $this->getExecutableMetric();
        foreach($fileList as $file)
        {
            $code = rtrim(basename($file), '.php');
            if(!in_array($code, $excutableMetric)) continue;
            $id = array_search($code, $excutableMetric);

            $calc = new stdclass();
            $calc->code = $code;
            $calc->file = $file;
            $calcList[$id] = $calc;
        }

        return $calcList;
    }

    /**
     * 获取度量项计算实例列表。
     * Get calculator instance list.
     *
     * @access public
     * @return array
     */
    public function getCalcInstanceList()
    {
        $calcList = $this->getExecutableCalcList();

        include $this->metricTao->getBaseCalcPath();
        $calcInstances = array();
        foreach($calcList as $id => $calc)
        {
            $file      = $calc->file;
            $className = $calc->code;

            require_once $file;
            $metricInstance = new $className;
            $metricInstance->id = $id;

            $calcInstances[$className] = $metricInstance;
        }

        return $calcInstances;
    }

    /**
     * 获取通用数据集对象。
     * Get instance of data set object.
     *
     * @access public
     * @return dataset
     */
    public function getDataset()
    {
        $datasetPath = $this->metricTao->getDatasetPath();
        include_once $datasetPath;
        return new dataset($this->dao);
    }

    /**
     * 对度量项按照通用数据集进行归类，没有数据集不做归类。
     * Classify calculator instance list by its data set.
     *
     * @param  array  $calcList
     * @access public
     * @return array
     */
    public function classifyCalc($calcList)
    {
        $datasetCalcGroup = array();
        $otherCalcList    = array();
        foreach($calcList as $code => $calc)
        {
            if(empty($calc->dataset))
            {
                $otherCalcList[$code] = $calc;
                continue;
            }

            $dataset = $calc->dataset;
            if(!isset($datasetCalcGroup[$dataset])) $datasetCalcGroup[$dataset] = array();
            $datasetCalcGroup[$dataset][$code] = $calc;
        }

        $classifiedCalcGroup = array();
        foreach($datasetCalcGroup as $dataset => $calcList) $classifiedCalcGroup[] = (object)array('dataset' => $dataset, 'calcList' => $calcList);

        foreach($otherCalcList as $code => $calc) $classifiedCalcGroup[] = (object)array('dataset' => '', 'calcList' => array($code => $calc));
        return $classifiedCalcGroup;
    }

    /**
     * 对度量项的字段列表取并集。
     * Unite field list of each calculator.
     *
     * @param  array  $calcList
     * @access public
     * @return string
     */
    public function uniteFieldList($calcList)
    {
        $fieldList = array();
        foreach($calcList as $calcInstance) $fieldList  = array_merge($fieldList, $calcInstance->fieldList);
        return implode(',', array_unique($fieldList));
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->metric->browse->search['actionURL'] = $actionURL;
        $this->config->metric->browse->search['queryID']   = $queryID;
        $this->config->metric->browse->search['params']['dept']['values']    = $this->loadModel('dept')->getOptionMenu();
        $this->config->metric->browse->search['params']['visions']['values'] = $this->loadModel('user')->getVisionList();

        $this->loadModel('search')->setSearchParams($this->config->metric->browse->search);
    }

    /**
     * 为度量详情页构建操作按钮
     * Build operate menu.
     *
     * @param  object $metric
     * @access public
     * @return array
     */
    public function buildOperateMenu(object $metric): array
    {
        $menuList = array
        (
            'main'   => array(),
            'suffix' => array()
        );

        if($metric->stage == 'wait')
        {
            $menuList['main'][] = $this->config->metric->actionList['implement'];
            $menuList['suffix'][] = commonModel::buildActionItem('metric', 'edit', "metric=$metric->id", $metric, array('icon' => 'edit'));
        }
        else
        {
            $menuList['main'][] = $this->config->metric->actionList['delist'];
        }

        if(!$metric->builtin)
        {
            $menuList['suffix'][] = $this->config->metric->actionList['delete'];
        }


        return $menuList;
    }

    /**
     * 获取范围的对象列表。
     * Get object pairs by scope.
     *
     * @param  string $scope
     * @access public
     * @return array
     */
    public function getPairsByScope($scope)
    {
        if($scope == 'global') return array();

        $objectPairs = array();
        switch($scope)
        {
            case 'dept':
                $objectPairs = $this->loadModel('dept')->getDeptPairs();
                break;
            case 'user':
                $objectPairs = $this->loadModel('user')->getPairs('noletter');
                break;
            default:
                $objectPairs = $this->loadModel($scope)->getPairs();
                break;
        }

        return $objectPairs;
    }

    /**
     * 获取度量项的日期字符串。
     * Build date cell.
     *
     * @param  object $row
     * @access public
     * @return string
     */
    public function buildDateCell($row)
    {
        extract((array)$row);

        if(isset($year, $month, $day))
        {
            return "{$year}-{$month}-{$day}";
        }
        elseif(isset($year, $week))
        {
            return sprintf($this->lang->metric->weekCell, $year, $week);
        }
        elseif(isset($year, $month))
        {
            return $year . $this->lang->year . $month . $this->lang->month;
        }
        elseif(isset($year))
        {
            return $year . $this->lang->year;
        }

        return false;
    }

    /**
     * 检查度量项计算文件是否存在。
     * Check if the calculator file exists or not.
     *
     * @param  object $row
     * @access public
     * @return bool
     */
    public function checkCalcExists($metric)
    {
        $calcName = $this->metricTao->getCustomCalcRoot() . $metric->code . '.php';
        return file_exists($calcName);
    }

    /**
     * 检查度量项计算文件是否定义了必要的类。
     * Check whether the necessary class exist in the file.
     *
     * @param  object $row
     * @access public
     * @return bool
     */
    public function checkCalcClass($metric)
    {
        return class_exists($metric->code);
    }

    /**
     * 检查度量项计算文件中是否编写了必要的方法。
     * Check whether the necessary methods exist in the file.
     *
     * @param  object $row
     * @access public
     * @return bool
     */
    public function checkCalcMethods($metric)
    {
        $methodNameList = $this->metricTao->getMethodNameList($metric->code);
        foreach($this->config->metric->necessaryMethodList as $method)
        {
            if(!in_array($method, $methodNameList)) return false;
        }

        return true;
    }

    /**
     * 没有度量的显示范围不做显示。
     * Unset scope item that have no metric.
     *
     * @access public
     * @return void
     */
    public function processScopeList()
    {
        foreach($this->lang->metric->scopeList as $scope => $name)
        {
            $metrics = $this->metricTao->fetchMetrics($scope);
            if(empty($metrics)) unset($this->lang->metric->scopeList[$scope]);
        }
    }

    /**
     * 根据后台配置的估算单位对列表赋值。
     * Assign unitList['measure'] by custom hourPoint.
     *
     * @access public
     * @return void
     */
    public function processUnitList()
    {
        $this->app->loadLang('custom');
        $key = zget($this->config->custom, 'hourPoint', '0');

        $this->lang->metric->unitList['measure'] = $this->lang->custom->conceptOptions->hourPoint[$key];
    }

    /**
     * 根据后台配置的是否开启用户需求设置对象列表。
     * Unset objectList['requirement'] if custom requirement is close.
     *
     * @access public
     * @return void
     */
    public function processObjectList()
    {
        if(!isset($this->config->custom->URAndSR) or !$this->config->custom->URAndSR) unset($this->lang->metric->objectList['requirement']);
    }

    /**
     * 导入用户自定义的度量项计算文件。
     * Include custom calculator file.
     *
     * @param  string $code
     * @access public
     * @return void
     */
    public function includeCalc($code)
    {
        require $this->metricTao->getBaseCalcPath();
        require $this->metricTao->getCustomCalcFile($code);
    }

    /**
     * 从度量项计算文件中提取代码，去除注释。
     * Extract code from calculator file, remove the comment.
     *
     * @param  string $file
     * @access public
     * @return string
     */
    public function extractCode($file)
    {
        $reg = '/class.*extends.*baseCalc/';
        if(basename($file) == 'calc.class.php') $reg = '/class.*baseCalc/';

        $contents = file_get_contents($file);
        $lines = explode("\n", $contents);

        $matchedLines = array_filter($lines, function($line) use($reg) {
            return preg_match($reg, $line);
        });
        $matchedLines = array_values($matchedLines);

        $startIndex = array_search($matchedLines[0], $lines);
        $code = implode("\n", array_slice($lines, $startIndex));

        return $code;
    }

    /**
     * 合并度量项计算文件与基类文件。
     * Merge the calculator file and base calculator file.
     *
     * @param  string $code
     * @access public
     * @return string
     */
    public function mergeBaseCalc($code)
    {
        $baseCalcFile = $this->metricTao->getBaseCalcPath();
        $calcFile     = $this->metricTao->getCustomCalcFile($code);

        $baseCalcScript = $this->extractCode($baseCalcFile);
        $calcScript     = $this->extractCode($calcFile);

        $phpTag = '<?php';
        $mergedScript = $phpTag . "\n" . $baseCalcScript . "\n" . $calcScript;

        return $mergedScript;
    }

    /**
     * 试运行用户自定义的度量项计算文件，返回错误信息。
     * Dry run custom calculator file, return error message.
     *
     * @param  string $code
     * @access public
     * @return string
     */
    public function dryRunCalc($code)
    {
        $tmpCalcFile = $this->metricTao->getCustomCalcRoot() . $code . '.php.tmp';

        file_put_contents($tmpCalcFile, $calcScript);
        exec("php $tmpCalcFile 2>&1", $output);

        return $output;
    }

    /**
     * 更新度量项。
     * Updata metric.
     *
     * @param  object $metric
     * @access public
     * @return void
     */
    public function update(object $metric)
    {
        $this->metricTao->updateMetric($metric->id, $metric);
    }

    /**
     * 根据数据初始化操作按钮。
     * Init action button by data.
     *
     * @param  array  $metrics
     * @access public
     * @return array
     */
    public function initActionBtn(array $metrics): array
    {
        foreach($metrics as $metric)
        {
            foreach($metric->actions as $key => $action)
            {
                $isClick = true;

                if($action['name'] == 'edit')      $isClick = $metric->canEdit;
                if($action['name'] == 'implement') $isClick = $metric->canImplement;
                if($action['name'] == 'delist')    $isClick = $metric->canDelist;

                $metric->actions[$key]['disabled'] = !$isClick;
            }
        }

        return $metrics;
    }
}
