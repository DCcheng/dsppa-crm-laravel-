<?php
/**
 *  FileName: CustomFollowUpRecord.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 11:49
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomFollowUpRecord extends Model
{
    protected $table = 'custom_follow_up_record';

    public static function addAttributes($model)
    {
        $userInfo = config("webconfig.userInfo");
        $time = time();
        $model->uid = $userInfo["uid"];
        $model->department_id = $userInfo["department_id"];
        $model->longitude = is_null($model->longitude)?"":$model->longitude;
        $model->latitude = is_null($model->latitude)?"":$model->latitude;
        $model->address = is_null($model->address)?"":$model->address;
        $model->create_time = $time;
        $model->delete_time = 0;
        return $model;
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = array();

        //根据列表类型返回对应的数据
        //其中包含了客户、用户、部门、所有
        $userInfo = config("webconfig.userInfo");
        $type = $request->get("type","");
        $custom_id = $request->get("custom_id","");
        switch ($type) {
            case "custom":
                if ($custom_id == "")
                    throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE ." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
                $condition[] = "custom_id = ? and delete_time = 0";
                $params[] = $custom_id;
                break;
            case "user":
                $condition[] = "uid = ? and delete_time = 0";
                $params[] = $userInfo['uid'];
                break;
            case "department":
                $condition[] = "department_id = ? and delete_time = 0";
                $params[] = $userInfo['department_id'];
                break;
            case "all":
                $condition[] = "delete_time = 0";
                break;
            default:
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE ." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
                break;
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }

    public static function getCountParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr) = parent::getParams($request,$size);
        $condition = array();

        $userInfo = config("webconfig.userInfo");
        switch ($userInfo["data_authority"]) {
            case "self":
                $condition[] = "a.uid = ? and a.delete_time = 0";
                $params[] = $userInfo['uid'];
                break;
            case "department":
                $condition[] = "a.department_id = ? and a.delete_time = 0";
                $params[] = $userInfo['department_id'];
                break;
            case "all":
                $condition[] = "a.delete_time = 0";
                break;
            default:
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE ." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
                break;
        }

        $cid = $request->get('cid', "");
        if ($cid != "") {
            $condition[] = "a.cid = ?";
            $params[] = trim($cid);
        }

        //获取什么时间段后建立的客户跟进记录
        $start_time = $request->get('start_time', "");
        if ($start_time != "") {
            $params[] = strtotime($start_time);
            $condition[] = "a.create_time >= ?";
        }

        //获取什么时间段前建立的客户跟进记录
        $end_time = $request->get('end_time', "");
        if ($end_time != "") {
            $params[] = strtotime($end_time);
            $condition[] = "a.create_time <= ?";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr);
    }
}