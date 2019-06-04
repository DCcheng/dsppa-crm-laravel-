<?php
/**
 *  FileName: CustomSchemeList.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 15:49
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Models\Model;

class CustomSchemeList extends Model
{
    protected $table = "custom_scheme_list";

    public static function addAttributes($model)
    {
        $time = time();
        $model->total_price = round($model->price * $model->quantity,2);
        $model->create_time = $time;
        $model->delete_time = 0;
        return $model;
    }

    public static function editAttributes($model)
    {
        $model->total_price = round($model->price * $model->quantity,2);
        return $model;
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = array("delete_time = 0");

        $scheme_id = $request->get("scheme_id","");
        if ($scheme_id != "") {
            $params[] = (int)$scheme_id;
            $condition[] = "scheme_id = ?";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}