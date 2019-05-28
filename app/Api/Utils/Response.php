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
        return response()->json($result, 200);
    }

    public static function fail($msg){
        return response()->json(["message" => $msg,"status_code" => 404], 404);
    }

    public static function error($msg,$status = 404){
        return response()->json(["message" => $msg,"status_code" => $status], $status);
    }
}