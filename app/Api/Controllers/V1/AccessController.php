<?php
/**
 *  FileName: AccessController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 16:31
 */


namespace App\Api\Controllers\V1;


use App\Api\Controllers\Controller;
use App\Api\Requests\AccessRequest;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessController extends Controller
{
    /**
     * 9.1 - 获取功能访问节点列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr, $page, $size) = Access::getParams($request);
        list(, $treeArr) = Access::getTree();

        $orderRaw = "controller,sort";
        $model = DB::table(DB::raw(Access::getTableName()))->selectRaw("*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["content"] = $treeArr[$value["pid"]]["text"];
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 9.2 - 新增功能节点信息
     * @param AccessRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(AccessRequest $request){
        Access::addForData($request->all());
        return Response::success();
    }

    /**
     * 9.3 - 更新功能节点信息
     * @param AccessRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AccessRequest $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "访问节点ID"]);
        Access::updateForData($request->get("id"),$request->all());
        return Response::success();
    }

    /**
     * 9.4 - 删除功能节点信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request){
        Access::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 9.5 - 获取节点分类树
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree(){
        list($list) = Access::getTree();
        return Response::success(["data"=>$list]);
    }

    /**
     * 9.6 - 获取节点详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "访问节点ID"]);
        $model = Access::find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}