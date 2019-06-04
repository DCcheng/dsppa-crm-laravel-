<?php
/**
 *  FileName: CustomFollowUpFile.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 11:50
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomFollowUpFile extends Model
{
    protected $table = "custom_follow_up_file";

    public static function addAttributes($model)
    {
        $time = time();
        $model->create_time = $time;
        $model->delete_time = 0;
        return $model;
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = array("delete_time = 0");
        $custom_id = $request->get("custom_id","");
        if ($custom_id == "")
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE ." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
        $condition[] = "custom_id = ? and delete_time = 0";
        $params[] = $custom_id;
        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}