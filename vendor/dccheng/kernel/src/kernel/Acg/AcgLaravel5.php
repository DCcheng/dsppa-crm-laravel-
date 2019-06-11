<?php
/**
 *  FileName: AcgLaravel.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/6
 *  Time: 10:13
 */


namespace Kernel\Acg;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class AcgLaravel5 implements AcgInterface
{
    public $table;
    public $modelNamespace = "App\\Models";
    public $requestNamespace = "App\\Http\\Requests";
    public $controllerNamespace = "App\\Http\\Controllers";
    public $modelName = "";
    public $requestName = "";
    public $controllerName = "";

    public function run($config)
    {
        try {
            $this->table = $config["Table"];
            if (isset($config["requestNamespace"])) {
                $this->requestNamespace = $config["requestNamespace"];
            }
            if (isset($config["modelNamespace"])) {
                $this->modelNamespace = $config["modelNamespace"];
            }
            if (isset($config["controllerNamespace"])) {
                $this->controllerNamespace = $config["controllerNamespace"];
            }
            $tableArr = explode("_", $this->table);
            $this->modelName = "";
            foreach ($tableArr as $value) {
                $this->modelName .= ucfirst(strtolower($value));
            }

            $this->requestName = $this->modelName . "Request";
            $this->controllerName = $this->modelName . "Controller";

            $this->setController();
            $this->setRequest();
            $this->setModel();
        }catch (Exception $exception){
            if(file_exists($this->getPath($this->controllerNamespace)."\\".$this->controllerName.".php")){
                unlink($this->getPath($this->controllerNamespace)."\\".$this->controllerName.".php");
            }
            if(file_exists($this->getPath($this->modelNamespace)."\\".$this->modelName.".php")){
                unlink($this->getPath($this->modelNamespace)."\\".$this->modelName.".php");
            }
            if(file_exists($this->getPath($this->requestNamespace)."\\".$this->requestName.".php")){
                unlink($this->getPath($this->requestNamespace)."\\".$this->requestName.".php");
            }
            throw new Exception($exception->getMessage());
        }
    }

    public function setController(){
        $this->setTpl($this->controllerNamespace,$this->controllerName,"Controller.tpl",function($str) {
            $str = str_replace("{{requestNamespace}}", $this->requestNamespace, $str);
            $str = str_replace("{{requestName}}", $this->requestName, $str);
            $str = str_replace("{{modelNamespace}}", $this->modelNamespace, $str);
            $str = str_replace("{{modelName}}", $this->modelName, $str);
            return $str;
        });
    }

    public function setModel(){
        $this->setTpl($this->modelNamespace,$this->modelName,"Model.tpl",function($str) {
            $str = str_replace("{{table}}", $this->table, $str);
            return $str;
        });
    }

    public function setRequest()
    {
        $this->setTpl($this->requestNamespace,$this->requestName,"Request.tpl",function($str){
            $columns = DB::select("SHOW FULL COLUMNS FROM crm_$this->table");
            $rules = [];
            $attributes = [];
            foreach ($columns as $column) {
                if ($column->Key != "PRI") {
                    $rules[] = "'" . $column->Field . "' => '" . $this->setRule($column) . "'";
                    $attributes[] = "'" . $column->Field . "' => '" . $this->setAttributes($column) . "'";
                }
            }

            $ruleStr = "\n\t\t\t" . implode(",\n\t\t\t", $rules) . "\n\t\t";
            $str = str_replace("{{rules}}", $ruleStr, $str);

            $lableStr = "\n\t\t\t" . implode(",\n\t\t\t", $attributes) . "\n\t\t";
            $str = str_replace("{{attributes}}", $lableStr, $str);
            return $str;
        });
    }

    public function setAttributes($column)
    {
        return $column->Comment;
    }

    public function setRule($column)
    {
        $rules = [];
        //必填项
        if ($column->Null == "NO" && strpos($column->Field, '_time') === false) {
            $rules[] = "required";
        }
        //唯一值
        if ($column->Key == "UNI") {
            $rules[] = "unique:" . $this->table;
        } else if ($column->Key == "MUL") {
            $rules[] = "unique:" . $this->table . "," . $column->Field . ",NULL,id,delete_time,0";
        }
        //数据类型
        list($type) = explode("(", $column->Type);
        preg_match_all("/(?:\()(.*)(?:\))/i", $column->Type, $result);
        switch ($type) {
            case "text":
            case "enum":
                $rules[] = "string";
                break;
            case "varchar":
            case "char":
                $rules[] = "string|max:" . $result[1][0];
                break;
            case "int":
            case "tinyint":
                if (strpos($column->Field, '_time') !== false) {
                    $rules[] = "date";
                } else {
                    $rules[] = "integer";
                }
                break;
            case "float":
            case "double":
                $rules[] = "numeric";
                break;
        }
        return implode("|", $rules);
    }

    public function setTpl($namespace,$classname,$tpl,$callback){
        $path = $this->getPath($namespace);
        if (!is_dir($path)) {
            mkdir($path, 0644, true);
        }
        $tplPath = __DIR__ . "/laravel5";
        $str = file_get_contents($tplPath . "/".$tpl);
        $str = str_replace("{{Namespace}}", $namespace, $str);
        $str = str_replace("{{className}}", $classname, $str);
        $str = str_replace("{{date}}", date("Y-m-d", time()), $str);
        $str = str_replace("{{time}}", date("H:i", time()), $str);
        $str = $callback($str);
        $filename = $path . "\\" . $classname . ".php";
        file_put_contents($filename, "<?php \n" . $str . "\n?>");
    }

    public function getPath($namespace){
        $path = app_path() . "\\" . preg_replace('/^App\\\/','',$namespace);
        return $path;
    }
}