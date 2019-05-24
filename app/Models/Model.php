<?php
/**
 *  FileName: Model.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/22
 *  Time: 13:50
 */


namespace App\Models;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public static $model;

    public static function init(){
        self::$model = Container::getInstance()->make(static::class);
    }

    public static function getTableName(){
        self::init();
        return DB::connection()->getTablePrefix().self::$model->getTable();
    }

    public static function getParams($size = 15)
    {
        $page = request()->get("page",1);
        $size = request()->get("size",$size);
        $arr = $condition = $params = [];
        $condition = implode(" and ", $condition);
        return [$condition, $params, $arr, $page, $size];
    }

//    public static function addForData(){
//        DB::table('custom')->insert([
//            'name' => 'john@example.com',
//            'uid' => 0,
//            "department_id"=>2,
//            'identify'=>1,
//            'cid'=>1,
//            'level'=>1,
//            'province'=>1,
//            'city'=>1,
//            'area'=>1,
//            'address'=>123123,
//            'discount'=>"0.9"
//        ]);
//    }
}