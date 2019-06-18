<?php
/**
 *  FileName: MenuController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 8:58
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\AccessRequest;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Requests\MenuRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * 10.1 - 获取菜单列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        $size = $request->get("size", config("webconfig.listSize"));
        list($condition, $params, $arr, $page, $size) = Menu::getParams($request,$size);
        list(, $treeArr) = Menu::getTree();

        $orderRaw = "id desc";
        $model = DB::table(DB::raw(Menu::getTableName()))->selectRaw("*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $arr["total"] = $model->count();
        list($arr['pageList'], $arr['totalPage']) = Pager::create($arr["total"], $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["content"] = $treeArr[$value["pid"]]["text"];
            $value["create_time"] = $this->toDate($value["create_time"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 10.2 - 新增菜单信息
     * @param MenuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(MenuRequest $request){
        Menu::addForData($request->all());
        return Response::success();
    }

    /**
     * 10.3 - 更新菜单信息
     * @param MenuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MenuRequest $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "菜单ID"]);
        Menu::updateForData($request->get("id"),$request->all());
        return Response::success();
    }

    /**
     * 10.4 - 删除菜单信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request){
        Menu::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 10.5 - 获取菜单分类树
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree(){
        list($list) = Menu::getTree();
        return Response::success(["data"=>$list]);
    }

    /**
     * 10.6 - 获取第一二级访问菜单(根据用户权限划分)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leftmenu(Request $request){
        $this->validate($request, ['type' => 'required|string'], [], ["id" => "菜单类型"]);
        $userInfo = config("webconfig.userInfo");
        $type = $request->get("type","");
        if(!isset($userInfo["menu"][$type])){
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
        $ids = implode(",", $userInfo["menu"]);
        $list = Menu::getChildrenList("pid = ? and id in ($ids)  and status = 1 and delete_time = 0", [0]);
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["childrenList"] = Menu::getChildrenList("pid = ? and id in ($ids)  and status = 1 and delete_time = 0", [$value["id"]]);
            $list[$key] = $value;
        }
        return Response::success(["data"=>$list]);
    }

    /**
     * 10.7 - 获取菜单详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "菜单ID"]);
        $model = Menu::where("delete_time",0)->find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}
