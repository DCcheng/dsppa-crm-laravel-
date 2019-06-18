<?php
/**
 *  FileName: LogController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 16:42
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\Log;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * 8.1 - 获取日志接口
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request){
        $size = $request->get("size", config("webconfig.listSize"));
        list($condition, $params, $arr, $page, $size) = Log::getParams($request,$size);
        $orderRaw = "a.id desc";
        $model = DB::table(DB::raw(Log::getTableName()." as a"))->selectRaw("a.*,b.truename")
            ->join(DB::raw(Member::getTableName()." as b"),DB::raw("a.uid"),"=",DB::raw("b.uid"));
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $arr["total"] = $model->count();
        list($arr['pageList'], $arr['totalPage']) = Pager::create($arr["total"], $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 8.2 - 获取日志详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "日志ID"]);
        $model = Log::where("delete_time",0)->find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["params"] = json_decode($data["params"],true);
            $data["datas"] = json_decode($data["datas"],true);
            $data["responses_json"] = json_decode($data["responses_json"],true);
            $data["create_time"] = $this->toDate(ceil($data["create_time"]/1000));
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}
