<?php
/**
 *  FileName: AcgFactory.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/14
 *  Time: 14:50
 */


namespace Kernel\acg;
use Exception;

class AcgFactory
{
    public static $app;
    public $yii;
    public $laravel;
    public $obj = [];
    public static function run(){
        $acg = new AcgFactory();
        $acg->yii = $acg->init("Yii2");
        $acg->laravel = $acg->init("Laravel5");
        self::$app = $acg;
    }

    public function init($type){
        $type = ucwords(strtolower($type));
        if(!isset($this->obj[$type])) {
            if (!in_array($type, ["Yii2","Laravel5"])) {
                throw new Exception("不支持该框架类型");
            }
            $className = "Kernel\acg\Acg" . $type;
            return  new $className();
        }else{
            return $this->obj[$type];
        }
    }
}