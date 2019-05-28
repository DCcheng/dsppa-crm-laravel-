<?php
/**
 *  FileName: Custom.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/21
 *  Time: 16:33
 */


namespace App\Models;
use App\Api\Utils\Constant;
use App\Models\Model;
use Illuminate\Http\Request;
use Kernel\Maps\Maps;
use Exception;

class Custom extends Model
{
    protected $table = "custom";

    public static function addAttributes($model)
    {

        $model->uid = 1;
        $model->department_id = 1;
        $model->in_high_seas = 0;
        list($model->longitude,$model->latitude) = Maps::getGps($model->address,$model->city);
        $time = time();
        $model->cid = (int)$model->cid;
        $model->discount = (int)$model->discount;
        $model->follow_up_time = $time;
        $model->create_time = $time;
        $model->delete_time = 0;
        return $model;
    }

    public static function getParams(Request $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = array();

        //根据列表类型返回对应的数据
        //其中包含了客户列表、公海池、回收站
        //在客户列表中也根据访问用户的数据权限分为了，自己、部门和所有
        $type = $request->get('type',"");
//        switch ($type) {
//            case "list":
//                switch (Yii::$app->params["userInfo"]["data_authority"]) {
//                    case "self":
//                        $condition[] = "a.uid = :uid and a.in_high_seas = 0 and a.delete_time = 0";
//                        $params[":uid"] = Yii::$app->params["userInfo"]['uid'];
//                        break;
//                    case "department":
//                        $condition[] = "a.department_id = :department_id and a.in_high_seas = 0 and a.delete_time = 0";
//                        $params[":department_id"] = Yii::$app->params["userInfo"]['department_id'];
//                        break;
//                    case "all":
//                        $condition[] = "a.in_high_seas = 0 and a.delete_time = 0";
//                        break;
//                    default:
//                        throw new Exception(Constant::SYSTEM_DATA_EXCEPTION_MESSAGE, Constant::SYSTEM_DATA_EXCEPTION_CODE);
//                        break;
//                }
//                break;
//            case "high_seas":
//                $condition[] = "a.department_id = :department_id and a.in_high_seas = 1 and a.delete_time = 0";
//                $params[":department_id"] = Yii::$app->params["userInfo"]['department_id'];
//                break;
//            case "trash":
//                $condition[] = "a.in_high_seas = 1 and a.delete_time > 0";
//                break;
//            default:
//                throw new Exception(Constant::SYSTEM_DATA_EXCEPTION_MESSAGE, Constant::SYSTEM_DATA_EXCEPTION_CODE);
//                break;
//        }

        //获取公司名以及编号中包含对应关键字的客户档案
        $keyword = $request->get('keyword',"");
        if ($keyword != "") {
            $condition[] = "(a.name like :name or a.identify like :identify)";
            $params[':name'] = trim($keyword) . "%";
            $params[':identify'] = trim($keyword) . "%";
        }

        //获取对应省份的客户档案
        $province = $request->get('province',"");
        if ($province != "") {
            $condition[] = "a.province = :province";
            $params[':province'] = trim($province);
        }

        //获取对应城市的客户档案
        $city = $request->get('city',"");
        if ($city != "") {
            $condition[] = "a.city = :city";
            $params[':city'] = trim($city);
        }

        //获取对应区/县的客户档案
        $area = $request->get('area',"");
        if ($area != "") {
            $condition[] = "a.area = :area";
            $params[':area'] = trim($area);
        }

        //获取什么时间段后建立的客户档案
        $start_time = $request->get('start_time',"");
        if ($start_time != "") {
            $params[':start_time'] = strtotime($start_time);
            $condition[] = "a.create_time >= :start_time";
        }

        //获取什么时间段前建立的客户档案
        $end_time = $request->get('end_time',"");
        if ($end_time != "") {
            $params[':end_time'] = strtotime($end_time);
            $condition[] = "a.create_time <= :end_time";
        }

        //获取几天前根据的记录
        $no_follow_up_days = $request->get('no_follow_up_days',"");
        if ($no_follow_up_days != "") {
            $params[':no_follow_up_days'] = strtotime(date("Y-m-d"), time()) - ((int)$no_follow_up_days * 3600 * 24);
            $condition[] = "a.follow_up_time <= :no_follow_up_days";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}