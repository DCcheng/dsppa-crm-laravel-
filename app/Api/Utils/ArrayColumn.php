<?php
/**
 *  FileName: ArrayColumn.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 12:18
 */


namespace App\Api\Utils;


trait ArrayColumn
{
    public static function toOneDimension($arr,$key){
        $result = [];
        foreach ($arr as $value){
            $value = (array)$value;
            $result[] = $value[$key];
        }
        return $result;
    }
}