<?php
/**
 *  FileName: DepartmentController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 14:49
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\DepartmentRequest;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DepartmentController extends Controller
{
    /**
     * 12.1 - 获取部门列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request){
        list($condition, $params, $arr, $page, $size) = Department::getParams($request);
        list(,$treeArr) = Department::getTree();
        $orderRaw = "sort asc,id";
        $model = DB::table(DB::raw(Department::getTableName()))->selectRaw("id,pid,title,sort");
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
     * 12.2 - 新增部门信息
     * @param MemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(DepartmentRequest $request)
    {
        Department::addForData($request->all());
        return Response::success();
    }

    /**
     * 12.3 - 修改部门信息
     * @param MemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "部门ID"]);
        Department::updateForData($request->get("id"), $request->all());
        return Response::success();
    }

    /**
     * 12.4 - 删除部门信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        Department::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 12.5 - 获取部门树
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree(){
        list($list) = Department::getTree();
        return Response::success(["data"=>$list]);
    }
}