<?php
/**
 *  FileName: Maps.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/27
 *  Time: 9:29
 */


namespace Kernel\Maps;

use Kernel\FCurl\Curl;

class Maps
{
    public $key = "3cc53937e45ed9ab6f2419dc4e684950";
    public $curl;

    public static function init()
    {
        $maps = new Maps();
        $maps->curl = new Curl();
        $maps->curl->ssl = true;
        return $maps;
    }

    public static function getGps($address,$city = null)
    {
        $maps = self::init();
        $params = [
            "key"=>$maps->key,
            "address"=>$address
        ];
        if($city != null)
            $params["city"] = $city;
        $url = $maps->setUrl("https://restapi.amap.com/v3/geocode/geo?parameters",$params);
        $result = $maps->curl->get($url);
        $json = json_decode($result,true);
        if($json["status"] == 1 && isset($json['geocodes'][0]['location'])){
            return explode(",",$json['geocodes'][0]['location']);
        }else{
            return ['0','0'];
        }
    }

    public function setUrl($url,$params){
        $paramsArr = array();
        foreach ($params as $key => $param){
            $paramsArr[] = $key."=".$param;
        }
        $url = $url."&".implode("&",$paramsArr);
        return $url;
    }
}