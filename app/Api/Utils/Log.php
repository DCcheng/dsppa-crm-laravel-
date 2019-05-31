<?php
/**
 *  FileName: Log.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 10:07
 */


namespace App\Api\Utils;

use Illuminate\Http\Request;

class Log
{
    /**
     * @param Request $request
     */
    public static function create(Request $request){
        $actionName = explode("\\",$request->route()->getActionName());
        list($controllerName,$methodName) = explode("@",end($actionName));
        $model = \App\Models\Log::addForData([
            "uid" => config("webconfig.userInfo")["uid"],
            "method" => $request->method(),
            "controller" => $controllerName,
            "action" => $methodName,
            "ip" => $request->getClientIp(),
            "user_agent" => $request->headers->get("user-agent"),
            "params" => json_encode($request->query->all()),
            "datas" => json_encode($request->request->all()),
            "description" => ""
        ]);
        config(["webconfig.logID"=>$model->id]);
    }

    /**
     * @param $code
     * @param $result
     */
    public static function update($code,$result){
        $logId = config("webconfig.logID");
        if ($logId != 0) {
            \App\Models\Log::updateForData($logId, [
                "responses_code" => (string)$code,
                "responses_json" => json_encode($result),
                "responses_time" => ceil(microtime(true) * 1000),
            ]);
        }
    }
}