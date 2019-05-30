<?php
/**
 *  FileName: CustomSchemeController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/30
 *  Time: 11:50
 */


namespace App\Api\Controllers\V1;

use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Api\Controllers\Controller;
use App\Http\Requests\IdsRequest;
use App\Http\Requests\ListRequest;
use App\Models\Custom;
use Illuminate\Support\Facades\DB;

class CustomSchemeController extends Controller
{
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr, $page, $size) = Custom::getParams($request);
        $field = $request->get("field", "desc");
        $order = $request->get("order", "id");
        if (in_array($field, ["follow_up_time", "create_time", "id"]) && in_array($order, ["desc", "asc"])) {
            $orderRaw = "a." . $field . " " . $order;
        } else {
            $orderRaw = "a.id desc";
        }

        $time = time();
        $model = DB::table(DB::raw(Custom::getTableName() . " as a"))->selectRaw("a.*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["key"] = $time . "_" . $value["id"];
            $value["follow_up_time"] = $this->toDateAgo($value['follow_up_time'], $time);
            $value["create_time"] = $this->toDate($value["create_time"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }
}