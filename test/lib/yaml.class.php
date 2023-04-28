<?php
/**
 * 本文件主要进行生成每个脚本文件对应的测试数据yaml文件。
 *
 * All request of entries should be routed by this router.
 *
 * @copyright   Copyright 2009-2017 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      liyang <liyang@easycorp.ltd>
 * @package     ZenTaoPMS
 * @version     1.0
 * @link        http://www.zentao.net/
 */

/**
 * Set fields for test data yaml file.
 *
 * @copyright Copyright 2009-2022 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @author    liyang <liyang@easycorp.ltd>
 * @package
 * @license   LGPL
 * @version   1.0
 * @Link      https://www.zentao.net
 */
class fields
{
    /**
     * Field Arr.
     *
     * @var array
     * @access public
     */
    public $fieldArr = array();

    /**
     * Field.
     *
     * @var int
     * @access private
     */
    private $field;

    /**
     * Set yaml field.
     *
     * @param  string    $value
     * @access public
     * @return object
     */
    public function setField($value)
    {
        $this->fieldArr[$value] = array();
        $this->field = $value;
        return $this;
    }

    /**
     * Set field rang.
     *
     * @param  string    $range
     * @access public
     * @return object
     */
    public function range($range)
    {
        $this->fieldArr[$this->field]['range'] = $range;
        return $this;
    }

    /**
     * Set field prefix.
     *
     * @param  string    $prefix
     * @access public
     * @return object
     */
    public function prefix($prefix)
    {
        $this->fieldArr[$this->field]['prefix'] = $prefix;
        return $this;
    }

    /**
     * Set field postfix.
     *
     * @param  string    $postfix
     * @access public
     * @return object
     */
    public function postfix($postfix)
    {
        $this->fieldArr[$this->field]['postfix'] = $postfix;
        return $this;
    }

    /**
     * Set field type.
     *
     * @param  string    $type
     * @access public
     * @return object
     */
    public function type($type)
    {
        $this->fieldArr[$this->field]['type'] = $type;
        return $this;
    }

    /**
     * Set field format.
     *
     * @param  string    $format
     * @access public
     * @return object
     */
    public function format($format)
    {
        $this->fieldArr[$this->field]['format'] = $format;
        return $this;
    }

    /**
     * Set field fields.
     *
     * @param  array    $fields
     * @access public
     * @return object
     */
    public function setFields($fields)
    {
        if(!is_array($fields))
        {
            echo "fields must be an array";
            return;
        }

        $this->fieldArr[$this->field]['fields'] = $fields;
        return $this;
    }

    /**
     * Get field array.
     *
     * @access public
     * @return array
     */
    public function getFields()
    {
        return $this->fieldArr;
    }

    /**
     * Assembly field generation rules.
     *
     * @param  array     $fieldArr
     * @access public
     * @return array
     */
    public function setFieldRule($fieldArr)
    {
        $ruleArr = array();
        $index   = 0;

        foreach($fieldArr as $field => $rule)
        {
            $ruleArr[$index]['field'] = $field;

            if(array_key_exists('fields', $rule))
            {
                $ruleArr[$index]['fields'] = $this->setFieldRule($rule['fields']);
            }
            else
            {
                if(!empty($rule['range'])) $ruleArr[$index]['range'] = $rule['range'];
            }

            if(!empty($rule['prefix']))  $ruleArr[$index]['prefix']  = $rule['prefix'];
            if(!empty($rule['postfix'])) $ruleArr[$index]['postfix'] = $rule['postfix'];
            if(!empty($rule['type']))    $ruleArr[$index]['type']    = $rule['type'];
            if(!empty($rule['format']))  $ruleArr[$index]['format']  = $rule['format'];
            $index++;
        }

        return $ruleArr;
    }
}

/**
 * Create test data from yaml file.
 *
 * @copyright Copyright 2009-2022 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @author    liyang <liyang@easycorp.ltd>
 * @package
 * @uses      field
 * @license   LGPL
 * @version   1.0
 * @Link      https://www.zentao.net
 */
class yaml
{
    /**
     * Set fields for yaml file.
     *
     * @var int
     * @access public
     */
    public $fields;

    /**
     * Global config.
     *
     * @var object
     * @access public
     */
    public $config;

    /**
     * The generated data table name.
     *
     * @var string
     * @access public
     */
    public $tableName;

    /**
     * The config files will be merged.
     *
     * @var string[]
     * @access private
     */
    private $configFiles = array();

    /**
     * __construct function load config and tableName.
     * @param  string $tableName
     * @access public
     * @return void
     */
    public function __construct($tableName)
    {
        global $config;
        $this->config    = $config;
        $this->tableName = $tableName;
        $this->fields    = new fields();

        $this->configFiles[] = dirname(dirname(__FILE__)) . "/data/{$this->tableName}.yaml";
    }

    /**
     * Yaml configuration file for script。
     *
     * @param  string  $fileName
     * @access public
     * @return object
     */
    public function config($fileName)
    {
        $runFileName = str_replace(strrchr($_SERVER['SCRIPT_FILENAME'], "."), "", $_SERVER['SCRIPT_FILENAME']);

        $pos = strripos($runFileName, DS);
        if($pos !== false) $runFileName = mb_substr($runFileName, $pos+1);

        $backtrace = debug_backtrace();
        $runPath   = $backtrace[count($backtrace)-1]['file'];

        $this->configFiles[] = dirname($runPath) . "/yaml/$runFileName/{$fileName}.yaml";

        return $this;
    }

    /**
     * Magic method, return fild.
     *
     * @param  string    $property_name
     * @access protected
     * @return object
     */
    public function __get($property_name)
    {
        $this->fields->setField($property_name);
        return $this->fields;
    }

    /**
     * Build yaml file and insert table.
     *
     * @param  int     $rows
     * @param  string  $dataDirYaml The yaml file names in the data directory
     * @param  bool  $isClear Truncate table if set isClear to true.
     * @access public
     * @return void
     */
    public function gen($rows, $dataDirYaml = '', $isClear = true)
    {
        $mergeData = array('fields' => array());
        foreach($this->configFiles as $configFile)
        {
            $configData = yaml_parse_file($configFile);
            $configData['title'] = $configData['title'];
            $configData['author'] = $configData['author'];
            $configData['version'] = $configData['version'];

            foreach($configData['fields'] as $configItem)
            {
                $field = $configItem['field'];
                $mergeData['fields'][$field] = $configItem;
            }
        }

        $runFileName = str_replace(strrchr($_SERVER['SCRIPT_FILENAME'], "."), "", $_SERVER['SCRIPT_FILENAME']);

        $pos = strripos($runFileName, DS);
        if($pos !== false) $runFileName = mb_substr($runFileName, $pos+1);

        $runFileDir  = dirname($runFileName);

        if(!is_dir("{$runFileDir}/data")) mkdir("{$runFileDir}/data", 0777);
        $yamlFile = "{$runFileDir}/data/{$this->tableName}_{$runFileName}.yaml";


        if(!empty($this->fields->fieldArr))
        {
            $fields = $this->fields->setFieldRule($this->fields->fieldArr);
            foreach($fields as $field) $mergeData['fields'][$field['field']] = $field;
        }
        $mergeData['fields'] = array_values($mergeData['fields']);

        yaml_emit_file($yamlFile, $mergeData, YAML_UTF8_ENCODING);

        $this->insertDB($yamlFile, $this->tableName, $rows, $isClear);
    }

    /**
     * Insert the data into database.
     *
     * @param  string    $yamlFile
     * @param  string    $tableName
     * @param  int       $rows
     * @param  bool      $isClear Truncate table if set isClear to true.
     * @access public
     * @return string
     */
    function insertDB($yamlFile, $tableName, $rows, $isClear = true)
    {
        $tableSqlDir = "{$_SERVER['PWD']}/data/sql";

        if(!is_dir($tableSqlDir)) mkdir($tableSqlDir, 0777, true);
        $dumpCommand = "mysqldump -u%s -p%s -h%s -P%s %s %s > {$tableSqlDir}/{$tableName}.sql 2>/dev/null";

        $runtimeRoot = dirname(dirname(__FILE__)) . '/runtime/';
        $zdPath      = $runtimeRoot . 'zd';
        $configYaml  = $runtimeRoot . 'tmp/config.yaml';

        $tableName = $this->config->db->prefix . $tableName;
        $dbName    = $this->config->db->name;
        $dbHost    = $this->config->db->host;
        $dbPort    = $this->config->db->port;
        $dbUser    = $this->config->db->user;
        $dbPWD     = $this->config->db->password;

        $command = "$zdPath -c %s -d %s -n %d -t %s -dns mysql://%s:%s@%s:%s/%s#utf8";
        $genSQL  = "$zdPath -c %s -d %s -n %d -t %s -o %s";
        if($isClear === true)
        {
            /* Truncate table to reset auto increment number. */
            system(sprintf("mysql -u%s -p%s -h%s -P%s %s -e 'truncate %s' 2>/dev/null", $dbUser, $dbPWD, $dbHost, $dbPort, $dbName, $tableName));
            $command .= ' --clear';
        }
        $execYaml   = sprintf($command, $configYaml, $yamlFile, $rows, $tableName, $dbUser, $dbPWD, $dbHost, $dbPort, $dbName);
        $execGenSQL = sprintf($genSQL, $configYaml, $yamlFile, $rows, $tableName, "{$tableSqlDir}/{$tableName}_zd.sql");
        $execDump   = sprintf($dumpCommand, $dbUser, $dbPWD, $dbHost, $dbPort, $dbName, $tableName);
        system($execDump);
        system($execYaml);
        system($execGenSQL);
    }

    /**
     * Restore table data.
     *
     * @param  string    $tableName
     * @access public
     * @return mixed
     */
    public function restoreTable($tableName)
    {
        $tableSql = "{$_SERVER['PWD']}/data/sql/$tableName.sql";
        if(!is_file($tableSql)) return false;

        $dbName = $this->config->db->name;
        $dbHost = $this->config->db->host;
        $dbPort = $this->config->db->port;
        $dbUser = $this->config->db->user;
        $dbPWD  = $this->config->db->password;

        $command     = "mysql -u%s -p%s -h%s -P%s %s < %s";
        $execRestore = sprintf($command, $dbUser, $dbPWD, $dbHost, $dbPort, $dbName, $tableSql);
        system($execRestore);
    }
}

/**
 * Return yaml class
 *
 * @param  string $table
 * @access public
 * @return mixed
 */
function zdTable($table)
{
    return new yaml($table);
}
