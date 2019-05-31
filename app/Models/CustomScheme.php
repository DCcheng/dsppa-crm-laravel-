<?php
/**
 *  FileName: CustomScheme.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 9:02
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use App\Models\Custom;
use App\Models\CustomContacts;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class CustomScheme extends Model
{
    protected $table = "custom_scheme";

    public static function addAttributes($model)
    {
        $customInfo = DB::table(DB::raw(Custom::getTableName()." as a"))->selectRaw("a.name,a.discount,b.name as person_to_contact,b.phone")
            ->join(DB::raw(CustomContacts::getTableName() . " as b"), DB::raw("b.custom_id"), "=", DB::raw("a.id"))
            ->whereRaw("a.id = :id", [":id" => $model->custom_id])->orderByRaw("b.create_time")->first();
        if ($customInfo) {
            $model->custom_name = is_null($model->custom_name) ? $customInfo->name : $model->custom_name;
            $model->person_to_contact = is_null($model->person_to_contact) ? $customInfo->person_to_contact : $model->person_to_contact;
            $model->phone = is_null($model->phone) ? $customInfo->phone : $model->phone;
            $time = time();
            $model->discount = is_null($model->discount) ? $customInfo->discount : $model->discount;
            $model->action_time = is_null($model->action_time) ? $time : $model->action_time;
            $model->create_time = $time;
            $model->delete_time = 0;
            return $model;
        } else {
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
        }
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = array("delete_time = 0");

        $custom_id = $request->get("custom_id","");
        if ($custom_id != '') {
            $params[':custom_id'] = (int)$custom_id;
            $condition[] = "custom_id = :custom_id";
        }

        //获取公司名以及编号中包含对应关键字的客户方案卡
        $keyword = $request->get("keyword","");
        if ($keyword != "") {
            $condition[] = "(project_name like :keyword)";
            $params[':keyword'] = trim($keyword) . "%";
        }

        $start_time = $request->get('start_time', "");
        if ($start_time != "") {
            $params[':start_time'] = strtotime($start_time);
            $condition[] = "a.create_time >= :start_time";
        }

        $end_time = $request->get('end_time', "");
        if ($end_time != "") {
            $params[':end_time'] = strtotime($end_time);
            $condition[] = "a.create_time <= :end_time";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}