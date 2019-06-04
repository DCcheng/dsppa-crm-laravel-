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
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\Log;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * 8.1 - 获取日志接口
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request){
        list($condition, $params, $arr, $page, $size) = Log::getParams($request);
        $orderRaw = "a.id desc";
        $model = DB::table(DB::raw(Log::getTableName()." as a"))->selectRaw("a.*,b.truename")
            ->join(DB::raw(Member::getTableName()." as b"),DB::raw("a.uid"),"=",DB::raw("b.uid"));
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }
}