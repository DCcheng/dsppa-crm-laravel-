<?php
/**
 *  FileName: TrainingFileController.php
 *  Description :
 *  Author: DC
 *  Date: 2019-06-10
 *  Time: 02:08
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\TrainingFileRequest;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingFile;
use Exception;

class TrainingFileController extends Controller
{
    /**
     * 6.1 - 获取培训资料列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr, $page, $size) = TrainingFile::getParams($request);

        $orderRaw = "id desc";
        $model = DB::table(DB::raw(TrainingFile::getTableName()))->selectRaw("*");
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

    /**
     * 6.2 - 新增培训资料
     * @param TrainingFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(TrainingFileRequest $request)
    {
        DB::beginTransaction();
        try {
            list($file_id) = Uploads::uploadFile($request, "file");
            $uploadInfo = Uploads::find($file_id);
            $uploadInfo->status = 1;
            $uploadInfo->save();
            $data = [
                "upload_id" => $file_id,
                "cid" => $request->get("cid"),
                "type" => $uploadInfo->type,
                "name" => $uploadInfo->name,
                "filename" => $uploadInfo->filename,
                "convername" => $uploadInfo->convername,
                "size" => $uploadInfo->size,
            ];
            TrainingFile::addForData($data);
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 6.3 - 更新培训资料
     * @param TrainingFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TrainingFileRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "培训资料ID"]);
        DB::beginTransaction();
        try {
            list($file_id) = Uploads::uploadFile($request, "file");
            $uploadInfo = Uploads::find($file_id);
            $uploadInfo->status = 1;
            $uploadInfo->save();
            $model = TrainingFile::find($request->get("id"));
            if (!$model) {
                DB::rollBack();
                return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
            }
            $oldUploadFileID = $model->upload_id;
            $model["attributes"] = [
                "upload_id" => $file_id,
                "cid" => $request->get("cid"),
                "type" => $uploadInfo->type,
                "name" => $uploadInfo->name,
                "filename" => $uploadInfo->filename,
                "convername" => $uploadInfo->convername,
                "size" => $uploadInfo->size,
            ];
            $model->save();
            Uploads::cleanFile("id = $oldUploadFileID");
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 6.4 - 删除培训资料
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        TrainingFile::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 6.5 - 获取培训资料详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "培训资料ID"]);
        $model = TrainingFile::find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}

?>