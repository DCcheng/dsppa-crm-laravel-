<?php
/**
 *  FileName: CustomFollowUpController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 11:55
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\CustomFollowUpRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Models\CustomFollowUpRecord;
use App\Models\CustomFollowUpFile;
use App\Models\Category;
use App\Models\Department;
use App\Models\Member;
use App\Models\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CustomFollowUpController extends Controller
{
    /**
     * 3.1 - 获取跟进记录列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        $this->validate($request, ["custom_id" => "required|integer"], [], ["custom_id" => "客户ID"]);
        $size = $request->get("size", config("webconfig.listSize"));
        list($condition, $params, $arr, $page, $size) = CustomFollowUpRecord::getParams($request,$size);
        $time = time();
        $orderRaw = "create_time desc";
        $model = DB::table(DB::raw(CustomFollowUpRecord::getTableName()))->selectRaw("*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $arr["total"] = $model->count();
        list($arr['pageList'], $arr['totalPage']) = Pager::create($arr["total"], $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["key"] = $time . "_" . $value["id"];
            $value["create_time"] = $this->toDateAgo($value["create_time"], $time);
            $value["files"] = DB::table(DB::raw(CustomFollowUpFile::getTableName()))->selectRaw("type,name,filename,convername")->whereRaw("record_id = :record_id and delete_time = 0", [":record_id" => $value["id"]])->get();
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 3.2 - 获取资料文件列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function files(ListRequest $request)
    {
        $this->validate($request, ["custom_id" => "required|integer"], [], ["custom_id" => "客户ID"]);
        list($condition, $params, $arr) = CustomFollowUpFile::getParams($request);
        $orderRaw = "create_time desc";
        $model = DB::table(DB::raw(CustomFollowUpFile::getTableName()))->selectRaw("cid,type,name,filename,convername,create_time");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $fileList = $model->orderByRaw($orderRaw)->get();

        $cids = implode(",", $this->toOneDimension($fileList, "cid"));
        $list = DB::table(DB::raw(Category::getTableName()))->selectRaw("id,title")->whereRaw("id in ($cids)")->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            foreach ($fileList as $k => $v) {
                $v = (array)$v;
                if ($v["cid"] == $value["id"]) {
                    $v["create_time"] = $this->toDate($v["create_time"]);
                    $value["files"][] = $v;
                }
            }
            $value["file_totle"] = count($value["files"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 3.3 - 新增跟进记录
     * @param CustomFollowUpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(CustomFollowUpRequest $request)
    {
        $ids = $request->get("ids", "");
        $data = array_diff_key($request->all(), ["ids" => ""]);

        DB::beginTransaction();
        try {
            $model = CustomFollowUpRecord::addForData($data);
            if ($ids != "") {
                $ids = implode(",", $ids);
                $uploadFileList = Uploads::whereRaw("id in ($ids)")->get();
                foreach ($uploadFileList as $key => $value) {
                    CustomFollowUpFile::addForData([
                        "custom_id" => $model->custom_id,
                        "record_id" => $model->id,
                        "upload_id" => $value->id,
                        "cid" => $model->cid,
                        "type" => $value->type,
                        "name" => $value->name,
                        "filename" => $value->filename,
                        "convername" => $value->convername,
                        "size" => $value->size
                    ]);
                }
                Uploads::updateForIds($ids, ["status" => 1]);
            }
            Custom::updateForData($model->custom_id, ["follow_up_time" => time()]);
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 3.4 - 更新跟进记录
     * @param CustomFollowUpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomFollowUpRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "记录ID"]);
        $ids = $request->get("ids", "");
        $data = array_diff_key($request->all(), ["ids" => ""]);

        DB::beginTransaction();
        try {
            $model = CustomFollowUpRecord::updateForData($request->get("id"), $data);
            if ($ids != "") {

                //重置已经添加的文件数据
                $oldFileList = CustomFollowUpFile::whereRaw("record_id = :record_id and delete_time = 0", [":record_id" => $model->id])->get()->toArray();
                $oldUploadIds = implode(",", $this->toOneDimension($oldFileList, "upload_id"));
                Uploads::updateForIds($oldUploadIds, ["status" => 0]);
                CustomFollowUpFile::whereRaw("record_id = :record_id and delete_time = 0", [":record_id" => $model->id])->delete();

                //重新添加文件数据
                $ids = implode(",", $ids);
                $uploadFileList = Uploads::whereRaw("id in ($ids)")->get();
                foreach ($uploadFileList as $key => $value) {
                    CustomFollowUpFile::addForData([
                        "custom_id" => $model->custom_id,
                        "record_id" => $model->id,
                        "upload_id" => $value->id,
                        "cid" => $model->cid,
                        "type" => $value->type,
                        "name" => $value->name,
                        "filename" => $value->filename,
                        "convername" => $value->convername,
                        "size" => $value->size
                    ]);
                }
                Uploads::updateForIds($ids, ["status" => 1]);
            }
            Custom::updateForData($model->custom_id, ["follow_up_time" => time()]);
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 3.5 - 删除客户跟进记录
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        DB::beginTransaction();
        try {
            CustomFollowUpRecord::deleteForIds($request->get("ids"));
            CustomFollowUpFile::deleteForIds($request->get("ids"), "record_id");
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 3.6 - 获取客户跟进记录统计信息
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getcount(ListRequest $request)
    {
        list($condition, $params, $arr) = CustomFollowUpRecord::getCountParams($request);
        $time = time();
        $total = 0;
        $model = DB::table(DB::raw(CustomFollowUpRecord::getTableName() . " as a"))->selectRaw("a.uid,b.truename,c.title as department_name,count(a.id) as total")
            ->join(DB::raw(Member::getTableName() . " as b"), DB::raw("b.uid"), "=", DB::raw("a.uid"))
            ->join(DB::raw(Department::getTableName() . " as c"), DB::raw("b.department_id"), "=", DB::raw("c.id"));
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $list = $model->groupBy(DB::raw('a.uid,b.truename,c.title'))->orderByRaw("total desc,a.uid asc")->get();
        $rowNum = 0;$prevTotal = 0;$prevNum = 1;
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $rowNum++;
            if($key == 0 || $prevTotal != $value["total"]) {
                $value["row_num"] = $rowNum;
                $prevNum = $rowNum;
            }else{
                $value["row_num"] = $prevNum;
            }
            $value["key"] = $time . "_" . $value["uid"];
            $total += (int)$value["total"];
            $prevTotal = $value["total"];
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        $arr["total"] = $total;
        return Response::success(["data" => $arr]);
    }

    /**
     * 3.7 - 获取跟进记录详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "跟进记录ID"]);
        $model = CustomFollowUpRecord::where("delete_time",0)->find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}
