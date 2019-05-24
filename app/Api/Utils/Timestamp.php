<?php
/**
 *  FileName: Timestamp.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/24
 *  Time: 13:53
 */


namespace App\Api\Utils;


class Timestamp
{
    public static function toDate($timestamp, $format = "Y-m-d H:i")
    {
        return date($format, $timestamp);
    }

    public static function toDateAgo($timestamp, $now, $format = "Y-m-d H:i")
    {
        $time = (int)$now - (int)$timestamp;
        if($time <= 3540){
            $str = ceil($time / 60)."分钟前";
        }else if($time <= 82800){
            $str = ceil($time / (60*60))."小时前";
        }else if($time <= 1209600){
            $str = ceil($time / (60*60*24))."天前";
        }else{
            $str = self::toDate($timestamp,$format);
        }
        return $str;
    }
}