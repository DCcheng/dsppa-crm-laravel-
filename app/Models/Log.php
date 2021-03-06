<?php
/**
 *  FileName: Log.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 9:39
 */

namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class Log extends Model
{
    protected $table = "log";

    public static function addAttributes($model)
    {
        $model->responses_code = "200";
        $model->responses_json = json_encode([]);
        $model->responses_time = 0;
        $model->create_time = ceil(microtime(true) * 1000);
        $model->delete_time = 0;
        return $model;
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $userInfo = config("webconfig.userInfo");
        //根据列表类型返回对应的数据
        //在客户列表中也根据访问用户的数据权限分为了，自己、部门和所有
        switch ($userInfo["data_authority"]) {
            case "self":
                $condition[] = "a.uid = ? and a.delete_time = 0";
                $params[] = $userInfo['uid'];
                break;
            case "department":
                $condition[] = "b.department_id = ? and a.delete_time = 0";
                $params[] = $userInfo['department_id'];
                break;
            case "all":
                $condition[] = " a.delete_time = 0";
                break;
            default:
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE ." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
                break;
        }

        $start_time = $request->get('start_time', "");
        if ($start_time != "") {
            $params[] = strtotime($start_time);
            $condition[] = "a.create_time >= ?";
        }

        $end_time = $request->get('end_time', "");
        if ($end_time != "") {
            $params[] = strtotime($end_time);
            $condition[] = "a.create_time <= ?";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}