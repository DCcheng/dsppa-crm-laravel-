<?php
/**
 *  FileName: Encrypt.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 14:41
 */


namespace App\Api\Utils;


class Encrypt
{
    public static function start($str, $code = null)
    {
        if ($code == null) {
            $code = rand(1000, 9999);
        }
        $fixedStr = "BCC7C71CF93F9CDBDB88671B701D8A35";
        $arr['code'] = $code;
        $arr['password'] = md5($fixedStr . $str . $code);
        return $arr;
    }
}