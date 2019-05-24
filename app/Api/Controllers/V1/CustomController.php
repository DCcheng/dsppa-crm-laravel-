<?php
/**
 *  FileName: CustomController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/21
 *  Time: 15:27
 */


namespace App\Api\Controllers\V1;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Api\Utils\Timestamp;
use App\Http\Controllers\Controller;
use App\Models\Custom;
use Exception;
use Illuminate\Support\Facades\DB;
use Dingo\Api\Http\Request;
class CustomController extends Controller
{
    public function index(Request $request){
        try {

//            Validation::validate($_GET, [
//                [["type"], "required"],
//                [["type", "keyword", "start_time", "end_time"], "string"],
//                [["no_follow_up_days", "size", "page"], "number"]
//            ]);

            list($condition, $params, $arr, $page, $size) = Custom::getParams(15);
            $time = time();
            $model = DB::table(DB::raw(Custom::getTableName()." as a"))->selectRaw("a.*")->whereRaw($condition, $params);
            list($arr['pageList'],$arr['totalPage']) = Pager::create($model->count(), $size);
            $list = $model->forPage($page,$size)->orderByRaw("a.id desc,a.name")->get();
            foreach ($list as $key => $value) {
                $value = (array)$value;
                $value["key"] = $time . "_" . $value["id"];
                $value["follow_up_time"] =  Timestamp::toDateAgo($value['follow_up_time'],$time);
                $value["create_time"] = Timestamp::toDate($value["create_time"]);
                $list[$key] = $value;
            }
            $arr['list'] = $list;
            return Response::success(1,"获取客户列表",["data"=>$arr]);
        } catch (Exception $e) {
            return Response::error($e->getCode(), $e->getMessage());
        }
    }
//
//    public function index(Request $request){
//
//        $model = DB::table(DB::raw(Custom::getTableName()." as a"))
//            ->leftJoin(DB::raw(Custom::getTableName()." as b"),function($join){
//                $join->whereRaw("a.id = b.id");
//            })
//            ->selectRaw("a.id,a.name");
////            ->whereRaw("(a.name like :keyword) and a.id = :id",[":keyword"=>'%有限公司%',":keyword2"=>'%有限公司%',":id"=>52])
//
//        $count = $model->count();
//        $pageList = Pager::create($count,2);
//        $list = $model->orderByRaw("a.id desc,a.name")->forPage($request->get("page"),2)->get();
//        return Response::success(1,"123123123",["data"=>["list"=>$list,"pagelist"=>$pageList]]);
//    }

    public function show(){
        return 112322222;
    }
}