<?php
/**
 *  FileName: RoleController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 9:54
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Requests\RoleRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Models\Menu;
use App\Models\Access;

use App\Api\Utils\Response;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * 11.1 - 获取角色列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr, $page, $size) = Role::getParams($request);
        $orderRaw = "id desc";
        $model = DB::table(DB::raw(Role::getTableName()))->selectRaw("*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 11.7 - 获取某个角色信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "角色ID"]);
        $model = Role::find($request->get("id"));
        if($model){
            list(, , $menuList) = Menu::getTree();
            list(, , $accessList) = Access::getTree();
            $data = (array)$model["attributes"];
            $data["menu"] = unserialize($data["menu"]);
            $data["menu_tree"] = $this->setTree($menuList, $data["menu"]);
            $data["menu"] = implode(",", $data["menu"]);
            $data["access"] = unserialize($data["access"]);
            $data["access_tree"] = $this->setTree($accessList, $data["access"]);
            $data["access"] = implode(",", $data["access"]);
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }

    /**
     * 11.6 - 获取所有角色列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(ListRequest $request)
    {
        list($condition, $params) = Role::getParams($request);
        $orderRaw = "id desc";
        $model = DB::table(DB::raw(Role::getTableName()))->selectRaw("id,title");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $list = $model->orderByRaw($orderRaw)->get();
        return Response::success(["data" => $list]);
    }

    /**
     * 11.2 - 新增角色信息
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(RoleRequest $request)
    {
        Role::addForData($request->all());
        return Response::success();
    }

    /**
     * 11.3 - 更新角色信息
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "角色ID"]);
        Role::updateForData($request->get("id"), $request->all());
        return Response::success();
    }

    /**
     * 11.4 - 删除角色信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        Role::deleteForIds($request->get("ids"));
        return Response::success();
    }

    public function setTree($list, $arr)
    {
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["selected"] = in_array($value["id"], $arr) ? true : false;
            $value["children"] = ($value["children"] != false) ? $this->setTree($value["children"], $arr) : false;
            $list[$key] = $value;
        }
        return $list;
    }

    /**
     * 11.5 - 获取菜单及访问控制节点树
     * @return \Illuminate\Http\JsonResponse
     */
    public function trees()
    {
        list(, , $menuList) = Menu::getTree();
        list(, , $accessList) = Access::getTree();
        return Response::success(["data" => ["menu" => $menuList, "access" => $accessList]]);
    }
}