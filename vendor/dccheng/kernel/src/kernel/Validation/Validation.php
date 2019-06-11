<?php
/**
 *  FileName: Validation.php
 *  Description :
 *  Author: DC
 *  Date: 2019/4/30
 *  Time: 9:07
 */
namespace Kernel\Validation;
use Exception;

class Validation
{
    public $params;
    private $errorArr = array();
    const ErrorCode = 0;

    public function validate($params,$rules,$is_html = true){
        $this->params = $params;
        foreach ($rules as $k => $r){
            switch ($r[1]){
                case 'required':
                    $this->validateRequired($r);
                    break;
                case 'string':
                    $this->validateString($r);
                    break;
                case 'number':
                    $this->validateNumber($r);
                    break;
                case 'array':
                    $this->validateArray($r);
                    break;
                default:
                    throw new Exception("不能正确验证".$r[1]."数据格式",self::ErrorCode);
                    break;
            }
        }
        if(count($this->errorArr) > 0){
            if($is_html) {
                $errorStr = implode("<br/>", $this->errorArr);
            }else{
                $errorStr = implode("\n\t", $this->errorArr);
            }
            throw new Exception($errorStr,self::ErrorCode);
        }
    }

    public function validateRequired($r){
        $message = isset($r["message"])?$r["message"]:"{attribute}为必填项";
        if(is_array($r[0])){
            foreach ($r[0] as $key => $value){
                if(!isset($this->params[$value]) && !isset($this->errorArr[$value]))
                    $this->errorArr[$value] = str_replace("{attribute}",$value,$message);
            }
        }else{
            if(!isset($this->params[$r[0]]) && !isset($this->errorArr[$r[0]]))
                $this->errorArr[$r[0]] = str_replace("{attribute}",$r[0],$message);
        }
    }

    public function validateString($r){
        $message = isset($r["message"])?$r["message"]:"{attribute}必须为字符串类型";
        if(is_array($r[0])){
            foreach ($r[0] as $key => $value){
                if(isset($this->params[$value]) && !is_string($this->params[$value]) && !isset($this->errorArr[$value]))
                    $this->errorArr[$value] = str_replace("{attribute}",$value,$message);
            }
        }else{
            if(isset($this->params[$r[0]]) && !is_string($this->params[$r[0]]) && !isset($this->errorArr[$r[0]]))
                $this->errorArr[$r[0]] = str_replace("{attribute}",$r[0],$message);
        }
    }

    public function validateNumber($r){
        $message = isset($r["message"])?$r["message"]:"{attribute}必须为数值类型";
        if(is_array($r[0])){
            foreach ($r[0] as $key => $value){
                if(isset($this->params[$value]) && !is_numeric($this->params[$value]) && !isset($this->errorArr[$value]))
                    $this->errorArr[$value] = str_replace("{attribute}",$value,$message);
            }
        }else{
            if(isset($this->params[$r[0]]) && !is_numeric($this->params[$r[0]]) && !isset($this->errorArr[$r[0]]))
                $this->errorArr[$r[0]] = str_replace("{attribute}",$r[0],$message);
        }
    }

    public function validateArray($r){
        $message = isset($r["message"])?$r["message"]:"{attribute}必须为数组类型";
        if(is_array($r[0])){
            foreach ($r[0] as $key => $value){
                if(isset($this->params[$value]) && !is_array($this->params[$value]) && !isset($this->errorArr[$value]))
                    $this->errorArr[$value] = str_replace("{attribute}",$value,$message);
            }
        }else{
            if(isset($this->params[$r[0]]) && !is_array($this->params[$r[0]]) && !isset($this->errorArr[$r[0]]))
                $this->errorArr[$r[0]] = str_replace("{attribute}",$r[0],$message);
        }
    }
}
