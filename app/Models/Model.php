<?php
/**
 *  FileName: Model.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/22
 *  Time: 13:50
 */


namespace App\Models;

use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public static $model;
    public $timestamps = false;

    public static function init()
    {
        self::$model = Container::getInstance()->make(static::class);
    }

    public static function getTableName()
    {
        self::init();
        return DB::connection()->getTablePrefix() . self::$model->getTable();
    }

    public static function addAttributes($model)
    {
        return $model;
    }

    public static function addForData($data)
    {
        $model = new static();
        try {
            $model["attributes"] = $data;
            $model = static::addAttributes($model);
            $model->save();
            return $model;
        } catch (Exception $e) {
            if (config("app.debug")) {
                throw new HttpResponseException(Response::fail($e->getMessage()));
            } else {
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE));
            }
        }
    }

    public static function editAttributes($model)
    {
        return $model;
    }

    public static function updateForData($id, $data)
    {
        $model = static::find($id);
        if ($model) {
            try {
                $model["attributes"] = $data;
                $model = static::editAttributes($model);
                $model->save();
                return $model;
            } catch (Exception $e) {
                if (config("app.debug")) {
                    throw new HttpResponseException(Response::fail($e->getMessage()));
                } else {
                    throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE));
                }
            }
        } else {
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
        }
    }

    public static function getParams(Request $request, $size = 15)
    {
        $page = request()->get("page", 1);
        $size = request()->get("size", $size);
        $arr = $condition = $params = [];
        $condition = implode(" and ", $condition);
        return [$condition, $params, $arr, $page, $size];
    }
}