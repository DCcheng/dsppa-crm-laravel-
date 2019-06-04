<?php
/**
 *  FileName: Response.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/22
 *  Time: 10:39
 */


namespace App\Api\Utils;

class Response
{
    public static function success($arr = []){
        $result = array_merge(["message" => "OK","status_code"=>200], $arr);
        Log::update(200,$result);
        return response()->json($result, 200);
    }

    public static function fail($msg,$code = 404){
        $result = ["message" => $msg,"status_code" => $code];
        Log::update($code,$result);
        return response()->json($result, $code);
    }
}