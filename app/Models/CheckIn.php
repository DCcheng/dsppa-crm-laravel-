<?php
/**
 *  FileName: CheckIn.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/30
 *  Time: 15:25
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckIn extends Model
{
    protected $table = "checkin";

    public static function addAttributes($model)
    {
        $userInfo = config("webconfig.userInfo");
        $model->uid = $userInfo["uid"];
        $model->department_id = $userInfo["department_id"];
        $model->create_time = time();
        $model->delete_time = 0;
        return $model;
    }

    public static function getParams(ListRequest $request, $size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request, $size);
        $condition = [];
        $userInfo = config("webconfig.userInfo");
        switch ($userInfo["data_authority"]) {
            case "self":
                $condition[] = "a.uid = :uid and a.delete_time = 0";
                $params[":uid"] = $userInfo['uid'];
                break;
            case "department":
                $condition[] = "a.department_id = :department_id and a.delete_time = 0";
                $params[":department_id"] = $userInfo['department_id'];
                break;
            case "all":
                $condition[] = "a.delete_time = 0";
                break;
            default:
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
                break;
        }

        $keyword = $request->get("keyword","");
        if ($keyword != "") {
            $condition[] = "c.truename like :keyword";
            $params[':keyword'] = trim($keyword) . "%";
        }

        $department_name = $request->get("department_name","");
        if ($department_name != "") {
            $condition[] = "b.title like :department_name";
            $params[':department_name'] = trim($department_name) . "%";
        }

        $start_time = $request->get("start_time","");
        if ($start_time != "") {
            $params[':start_time'] = strtotime($start_time);
            $condition[] = " a.create_time >= :start_time";
        }

        $end_time = $request->get("end_time","");
        if ($end_time != "") {
            $params[':end_time'] = strtotime($end_time);
            $condition[] = " a.create_time <= :end_time";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}