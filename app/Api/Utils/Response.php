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
    public static function success($code,$msg,$arr = []){

        $result = array_merge(["ret" => $code, "msg" => $msg], $arr);
        return response()->json($result, 200);
    }

    public static function fail($code,$msg){

        return response()->json(["ret" => $code, "msg" => $msg], 404);
    }

    public static function error($code,$msg,$status = 404){

        return response()->json(["ret" => $code, "msg" => $msg], $status);
    }
}