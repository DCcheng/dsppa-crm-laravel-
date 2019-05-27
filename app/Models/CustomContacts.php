<?php
/**
 *  FileName: CustomContacts.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/27
 *  Time: 15:21
 */


namespace App\Models;
use App\Models\Model;

class CustomContacts extends Model
{
    protected $table = "custom_contacts";

    public static function addAttributes($model)
    {
        $time = time();
        $model->email = is_null($model->email)?"":$model->email;
        $model->qq = is_null($model->qq)?"":$model->qq;
        $model->wechat = is_null($model->wechat)?"":$model->wechat;
        $model->job = is_null($model->job)?"":$model->job;
        $model->create_time = $time;
        $model->delete_time = 0;
        return $model;
    }
}