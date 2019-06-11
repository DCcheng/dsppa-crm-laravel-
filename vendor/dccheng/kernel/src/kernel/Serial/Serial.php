<?php
/**
 * Created by PhpStorm.
 * User: DC
 * Date: 2017/8/22
 * Time: 10:00
 */

namespace Kernel\serial;
use Exception;
class Serial
{
    private $file = "";
    private $filename = "";
    public $modelDate = false;
    public $model = "Y";
    public $lenght = 7;
    private $str = "";
    private $code = "";
    const FILE_PATH = "temp";
    const CODE_PREFIX = "";
    const ERROR_CODE = 0;

    private function init($filename)
    {
        $this->filename = base64_encode($filename);
        $this->file = __DIR__."/".self::FILE_PATH."/".$this->filename;
        $this->createDir(self::FILE_PATH);
        if(!file_exists($this->file)) {
            $str = $this->setStr(0);
            file_put_contents($this->file,$str);
        }
    }

    public function set($str,$filename = "Identify"){
        if($this->code == $str){
            file_put_contents($this->file,$this->str);
        }else{
            throw new Exception("随机码不一致，无法更新缓存随机码",self::ERROR_CODE);
        }
    }

    public function get($filename = "Identify"){
        $this->init($filename);
        $data = include($this->file);
        if($this->modelDate) {
            if ($data["prefix"] !== date($this->model)) {
                $this->str = $this->setStr();
                $this->code = date($this->model) . str_pad("1", $this->lenght, '0', STR_PAD_LEFT);
            } else {
                $number = $data['number'] + 1;
                $this->str = $this->setStr($number);
                $this->code = $data['prefix'] . str_pad($number, $this->lenght, '0', STR_PAD_LEFT);
            }
        }else{
            $number = $data['number'] + 1;
            $this->str = $this->setStr($number);
            $this->code = $data['prefix'] . str_pad($number, $this->lenght, '0', STR_PAD_LEFT);
        }
        return $this->code;
    }

    private function setStr($number = 1){
        if($this->modelDate){
            $prefix = date($this->model);
        }else{
            $prefix = self::CODE_PREFIX;
        }
        $str = "<?php return ['prefix'=>'".$prefix."',";
        $number = str_pad($number,$this->lenght,'0',STR_PAD_LEFT);
        $str .= "'number'=>'".$number."']; ?>";
        return $str;
    }
    //创建目录
    private function createDir($path){
        if (!file_exists($path)){
            $this->createDir(dirname($path));
            mkdir($path, 0777);
        }
    }
}