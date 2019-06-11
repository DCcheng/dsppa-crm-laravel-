<?php
/**
 *  FileName: CustomSchemeController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/30
 *  Time: 11:50
 */


namespace App\Api\Controllers\V1;

use App\Api\Requests\CustomSchemeRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Api\Controllers\Controller;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Models\CustomScheme;
use App\Models\CustomSchemeList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomSchemeController extends Controller
{
    /**
     * 2.1 - 获取客户方案卡列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        $this->validate($request, ["custom_id" => "required|integer"], [], ["custom_id" => "客户ID"]);
        list($condition, $params, $arr, $page, $size) = CustomScheme::getParams($request);
        $orderRaw = "a.id desc";
        $time = time();
        $model = DB::table(DB::raw(CustomScheme::getTableName() . " as a"))->selectRaw("a.*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["key"] = $time . "_" . $value["id"];
            $value["cname"] = "";
            $value["action_time"] = $this->toDate($value['action_time']);
            $value["create_time"] = $this->toDate($value["create_time"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 2.2 - 新增客户方案卡
     * @param CustomSchemeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(CustomSchemeRequest $request){
        CustomScheme::addForData($request->all());
        return Response::success();
    }

    /**
     * 2.3 - 修改客户方案卡
     * @param CustomSchemeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomSchemeRequest $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "方案卡ID"]);
        CustomScheme::updateForData($request->get("id"),$request->all());
        return Response::success();
    }

    /**
     * 2.4 - 删除客户方案卡
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request){
        CustomScheme::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 2.12 - 获取方案卡详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "方案卡ID"]);
        $model = CustomScheme::where("delete_time",0)->find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["create_time"] = $this->toDate($data["create_time"]);
            $data["list"] = CustomSchemeList::whereRaw("scheme_id = ? and delete_time = 0",[$data["id"]])->get();
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}