<?php
/**
 *  FileName: CustomContacts.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/27
 *  Time: 15:21
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Models\Model;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomContacts extends Model
{
    protected $table = "custom_contacts";

    public static function addAttributes($model)
    {
        $time = time();
        $model->email = is_null($model->email) ? "" : $model->email;
        $model->qq = is_null($model->qq) ? "" : $model->qq;
        $model->wechat = is_null($model->wechat) ? "" : $model->wechat;
        $model->job = is_null($model->job) ? "" : $model->job;
        $model->is_person_in_charge = 0;
        $model->create_time = $time;
        $model->delete_time = 0;
        return $model;
    }

    public static function deleteForIds($ids, $field = "id")
    {
        $ids = is_array($ids) ? implode(",", $ids) : $ids;
        $info = self::whereRaw($field . " in (" . $ids . ")")->get()->first();
        if (!$info) {
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
        }
        $total = CustomContacts::whereRaw("custom_id = :custom_id and delete_time = 0", [":custom_id" => $info->custom_id])->count();
        if ($total <= 1) {
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_NOT_ALLOW_DELETE_ALL_DATA_CODE . " - " . Constant::SYSTEM_NOT_ALLOW_DELETE_ALL_DATA_MESSAGE));
        }
        parent::deleteForIds($ids, $field);
    }

    public static function getParams(ListRequest $request, $size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request, $size);
        $condition = array("delete_time = 0");

        $custom_id = $request->get("custom_id", "");
        if ($custom_id != "") {
            $params[] = (int)$custom_id;
            $condition[] = "custom_id = ?";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}