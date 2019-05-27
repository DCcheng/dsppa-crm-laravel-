<?php
/**
 *  FileName: AcgYii2.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/14
 *  Time: 14:51
 */


namespace Kernel\acg;
use Yii;
use yii\base\NotSupportedException;
use yii\db\Schema;
use yii\helpers\Inflector;

class AcgYii2
{
    public $controllerFileName;
    public $modelFileName;

    public function run($config){
        if(isset($config["Controller"]) && isset($config["Model"])) {
            try {
                $config["CN_Name"] = isset($config["CN_Name"]) ? $config["CN_Name"] : "模板";

                $controllerArr = explode("\\",$config["Controller"]);
                $controllerName = ucfirst(strtolower(end($controllerArr)))."Controller";
                if(count($controllerArr) == 1) {
                    $controllerNamespace = "api\\controllers";
                }else{
                    array_pop($controllerArr);
                    $controllerNamespace = implode("\\",$controllerArr);
                }

                $modelArr = explode("\\",$config["Model"]);
                $tableName = strtolower(end($modelArr));
                $modelName = ucfirst($tableName);
                if(count($modelArr) == 1) {
                    $modelNamespace = "common\\models";
                }else{
                    array_pop($modelArr);
                    $modelNamespace = implode("\\",$modelArr);
                }

                $this->setController($controllerName, $modelName,$controllerNamespace,$modelNamespace, $config["CN_Name"]);
                $this->setModel($modelName,$tableName,$modelNamespace);
            }catch (Exception $e){
                if(file_exists($this->controllerFileName)){
                    unlink($this->controllerFileName);
                }
                if(file_exists($this->modelFileName)){
                    unlink($this->modelFileName);
                }
                throw new Exception($e->getMessage());
            }
        }else{
            throw new Exception("缺乏必要参数Controller或者Model");
        }
    }

    public function setController($controllerName,$modelName,$namespace,$modelNamespace,$CN_Name){

        $pathArr = explode("\\",Yii::getAlias("@common"));
        array_pop($pathArr);
        $path = implode("\\",$pathArr)."\\".$namespace;
        if(!is_dir($path)){
            mkdir($path,0644,true);
        }
        $this->controllerFileName = $path."\\".$controllerName.".php";
        $tplPath = __DIR__."/yii2";
        $str = file_get_contents($tplPath."/Controller.tpl");
        $str = str_replace("{{Namespace}}",$namespace,$str);
        $str = str_replace("{{Controller}}",$controllerName,$str);
        $str = str_replace("{{ModelNamespace}}",$modelNamespace,$str);
        $str = str_replace("{{Model}}",$modelName,$str);
        $str = str_replace("{{*}}",$CN_Name,$str);
        file_put_contents($this->controllerFileName,"<?php \n".$str."\n?>");
    }

    public function setModel($modelName,$tableName,$namespace){

        $pathArr = explode("\\",Yii::getAlias("@common"));
        array_pop($pathArr);
        $path = implode("\\",$pathArr)."\\".$namespace;
        if(!is_dir($path)){
            mkdir($path,0644,true);
        }
        $this->modelFileName = $path."\\".$modelName.".php";

        $table = Yii::$app->db->getSchema()->getTableSchema('{{%'.$tableName.'}}');

        if(is_null($table)){
            throw new Exception("不存在该数据表");
        }

        list($rules,$lables) = $this->setModelData($table);
        
        $tplPath = __DIR__."/yii2";
        $str = file_get_contents($tplPath."/Model.tpl");

        $str = str_replace("{{Namespace}}",$namespace,$str);
        $str = str_replace("{{Model}}",$modelName,$str);
        $str = str_replace("{{TableName}}",'{{%'.$tableName.'}}',$str);

        $ruleStr = "\n\t\t\t".implode(",\n\t\t\t",$rules)."\n\t\t";
        $str = str_replace("{{rules}}",$ruleStr,$str);

        $lableStr = "\n\t\t\t".implode(",\n\t\t\t",$lables)."\n\t\t";
        $str = str_replace("{{labels}}",$lableStr,$str);

        file_put_contents($this->modelFileName,"<?php \n".$str."\n?>");
    }

    protected function setModelData($table){
        $types = [];
        $lengths = [];
        $labels = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
            if(!empty($column->comment)) {
                $labels[$column->name] = "'" . $column->name . "' => '" . $column->comment . "'";
            }else{
                $label = Inflector::camel2words($column->name);
                $labels[$column->name] = "'" . $column->name . "' => '" . $label . "'";;
            }
        }
        $rules = [];
        foreach ($types as $type => $columns) {
            switch ($type){
                case "required":
                    $message = "{attribute}为必填项";
                    break;
                case "integer":
                    $message = '{attribute}必须为Int类型';
                    break;
                case "boolean":
                    $message = '{attribute}必须为Boolean类型';
                    break;
                case "number":
                    $message = '{attribute}必须为数值类型';
                    break;
            }
            if($message != "") {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type','message' => '$message']";
            }else{
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
            }
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length,'message' => '{attribute}长度不能超过".$length."个字符']";
        }
        $db = Yii::$app->db;
        // Unique indexes rules
        try {
            $uniqueIndexes = array_merge($db->getSchema()->findUniqueIndexes($table), [$table->primaryKey]);
            $uniqueIndexes = array_unique($uniqueIndexes, SORT_REGULAR);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount === 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique','message' => '{attribute}必须是唯一值']";
                    } elseif ($attributesCount > 1) {
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['$columnsList'], 'unique', 'targetAttribute' => ['$columnsList'],'message' => '{attribute}必须是唯一值']";
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            unset($refs[0]);
            $attributes = implode("', '", array_keys($refs));
            $targetAttributes = [];
            foreach ($refs as $key => $value) {
                $targetAttributes[] = "'$key' => '$value'";
            }
            $targetAttributes = implode(', ', $targetAttributes);
            $rules[] = "[['$attributes'], 'exist', 'skipOnError' => true, 'targetClass' => $refClassName::className(), 'targetAttribute' => [$targetAttributes]]";
        }
        return [$rules,$labels];
    }

    protected function isColumnAutoIncremental($table, $columns)
    {
        foreach ($columns as $column) {
            if (isset($table->columns[$column]) && $table->columns[$column]->autoIncrement) {
                return true;
            }
        }

        return false;
    }

    protected function generateClassName($tableName, $useSchemaName = null)
    {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }

        $schemaName = '';
        $fullTableName = $tableName;
        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos) . '_';
            }
            $tableName = substr($tableName, $pos + 1);
        }

        $db = Yii::$app->db;
        $patterns = [];
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }

        if ($this->standardizeCapitals) {
            $schemaName = ctype_upper(preg_replace('/[_-]/', '', $schemaName)) ? strtolower($schemaName) : $schemaName;
            $className = ctype_upper(preg_replace('/[_-]/', '', $className)) ? strtolower($className) : $className;
            $this->classNames[$fullTableName] = Inflector::camelize(Inflector::camel2words($schemaName.$className));
        } else {
            $this->classNames[$fullTableName] = Inflector::id2camel($schemaName.$className, '_');
        }

        if ($this->singularize) {
            $this->classNames[$fullTableName] = Inflector::singularize($this->classNames[$fullTableName]);
        }

        return $this->classNames[$fullTableName];
    }
}