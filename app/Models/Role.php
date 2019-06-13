<?php
/**
 *  FileName: Role.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 15:00
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Response;
use App\Models\Model;
use Kernel\Ftoken\TokenConstant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Kernel\Kernel;

class Role extends Model
{
    protected $table = "role";
    static $mergeMenuArr = ["frontend"=>[],"backend"=>[]];

    public static function addAttributes($model)
    {
        $model->data_authority = "self";
        $model->access = serialize($model->access);
        $model->menu = serialize(array_merge(self::$mergeMenuArr,$model->menu));
        $model->create_time = time();
        return $model;
    }

    public static function editAttributes($model)
    {
        $model->access = is_array($model->access)?serialize($model->access):$model->access;
        $model->menu = is_array($model->menu)?serialize(array_merge(self::$mergeMenuArr,$model->menu)):$model->menu;
        return $model;
    }

    public static function updateForData($id, $data)
    {
        $model = parent::updateForData($id, $data);
        $userInfo = config("webconfig.userInfo");
        if ($model && $id == $userInfo["role_id"]) {
            Kernel::token()->invalidate();
            throw new HttpResponseException(Response::fail(TokenConstant::TOKEN_EXPIRE_MESSAGE,401));
        }
    }

    public static function deleteForIds($ids, $field = "id")
    {
        $userInfo = config("webconfig.userInfo");
        if (in_array($userInfo["role_id"], $ids) || in_array(1, $ids)) {
            throw new HttpResponseException(Response::fail("不允许删除超级管理员以及当前角色"));
        } else {
            parent::deleteForIds($ids);
        }
    }

    public static function deleteDataForIds($condition)
    {
        static::whereRaw($condition)->delete();
    }

    public static function getParams(ListRequest $request, $size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request);
        $condition = array("status = 1");
        $userInfo = config("webconfig.userInfo");
        if ($userInfo["role_id"] != 1) {
            $condition[] = "id != 1";
        }
        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}