<?php
/**
 *  FileName: FException.php
 *  Description :
 *  Author: DC
 *  Date: 2019/4/9
 *  Time: 10:29
 */


namespace Kernel\Exception;
use Exception;
use Throwable;

class FException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
//        $strCode = $code != 0?$code." - ":"";
//        $message = $strCode.$message;
        parent::__construct($message, $code, $previous);
    }

//    public function getMessage(){
//        $strCode = $this->code != 0?$this->code." - ":"";
//        $message = "Error : ".$strCode.$this->message;
//        return $message;
//    }

    public function errorMessage(){
        $errorMsg = '<b>ErrorCode : '.$this->getCode().' <br/> ErrorMseeage : '.$this->getMessage().' <br/> ErrorFile : '.$this->getFile().' <br/> ErrorLine : '.$this->getLine().'</b>';
        return $errorMsg;
    }
}