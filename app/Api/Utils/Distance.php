<?php
/**
 *  FileName: Distance.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 12:00
 */


namespace App\Api\Utils;


trait Distance
{
    /**
     * 格式化两点之间的距离
     * @param $distance
     * @return string
     */
    public function toDistanceStr($distance)
    {
        if($distance <= 1000){
            $str = $distance."米";
        }else{
            $str = round((int)$distance/1000,1)."公里";
        }
        return $str;
    }
}