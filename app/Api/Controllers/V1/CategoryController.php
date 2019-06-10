<?php
/**
 *  FileName: CategoryController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 15:57
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\CategoryRequest;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * 7.1 - 获取分类列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr, $page, $size) = Category::getParams($request);
        list(, $treeArr) = Category::getTree();

        $orderRaw = "sort asc,id desc";
        $model = DB::table(DB::raw(Category::getTableName()))->selectRaw("*");
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
     * 7.2 - 获取所有分类列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(ListRequest $request)
    {
        list($condition, $params, $arr, $page, $size) = Category::getParams($request);
        list(, $treeArr) = Category::getTree();

        $orderRaw = "sort asc,id desc";
        $model = DB::table(DB::raw(Category::getTableName()))->selectRaw("*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
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
     * 7.3 - 新增分类信息
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(CategoryRequest $request){
        Category::addForData($request->all());
        return Response::success();
    }

    /**
     * 7.4 - 更新分类信息
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "分类ID"]);
        Category::updateForData($request->get("id"),$request->all());
        return Response::success();
    }

    /**
     * 7.5 - 删除分类信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request){
        Category::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 7.6 - 获取分类树
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree(){
        list($list) = Category::getTree();
        return Response::success(["data"=>$list]);
    }

    /**
     * 7.7 - 获取分类详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "分类ID"]);
        $model = Category::find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}