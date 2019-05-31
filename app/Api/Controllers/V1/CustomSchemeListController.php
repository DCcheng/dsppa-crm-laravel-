<?php
/**
 *  FileName: CustomSchemeListController.phproller.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 15:47
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\CustomSchemeListRequest;
use App\Api\Utils\ReadFile;
use App\Api\Utils\Response;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Models\CustomSchemeList;
use App\Models\Product;
use App\Models\Uploads;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CustomSchemeListController extends Controller
{
    /**
     * 2.5 - 获取方案卡清单
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        $this->validate($request, ["scheme_id" => 'required|integer'], [], ["scheme_id" => "方案卡ID"]);
        list($condition, $params, $arr) = CustomSchemeList::getParams($request);
        $model = DB::table(DB::raw(CustomSchemeList::getTableName()))->selectRaw("id,serial_num,product_name,product_model,price,quantity,unit,total_price,tip,create_time");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $list = $model->orderByRaw("id desc")->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["create_time"] = $this->toDate($value["create_time"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 2.6 - 下载方案清单上传模板
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(Request $request)
    {
        $filename = "uploads/CustomSchemeListTemplate.xlsx";
        return Response::success(["data" => ["filename" => $filename]]);
    }

    /**
     * 2.7 - 导入方案卡清单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        try {
            $this->validate($request, ['scheme_id' => 'required|integer', 'file' => 'required|file'], [], ["scheme_id" => "方案卡ID", "file" => "上传文件"]);
            list($file_id, $filename) = Uploads::uploadFile($request, "file", false);
            $row = 2;
            $data = ReadFile::readExcel(storage_path('app') . "/" . $filename, "scheme-list");
            Uploads::cleanFile("id = $file_id");
            DB::beginTransaction();
            try {
                $rowArr = [];
                $time = time();
                $maxSerialNum = CustomSchemeList::whereRaw("scheme_id = :scheme_id", [":scheme_id" => $request->get("scheme_id")])->max("serial_num");
                $maxSerialNum = is_null($maxSerialNum) ? 0 : (int)$maxSerialNum;
                foreach ($data as $key => $value) {
                    $row++;
                    $maxSerialNum++;
                    $rowArr[] = "('" . implode("','", ["brand" => $value["brand"], "product_name" => $value["product_name"], "product_model" => $value["product_model"], "create_time" => $time]) . "')";
                    $value["scheme_id"] = $_POST["scheme_id"];
                    $value["serial_num"] = $maxSerialNum;
                    unset($value["brand"]);
                    CustomSchemeList::addForData($value);
                }
                //更新产品库
                $sql = "replace into " . Product::getTableName() . " (brand, product_name,product_model,create_time) values" . implode(",", $rowArr);
                DB::insert($sql);
                DB::commit();
                return Response::success();
            } catch (HttpResponseException $exception) {
                DB::rollBack();
                $msg = $exception->getResponse()->getData()->message;
                $msg = $row == 2 ? $msg : "第" . $row . "行 - （" . $msg . "）";
                return Response::fail($msg);
            }
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 2.8 - 新增方案卡清单信息
     * @param CustomSchemeListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(CustomSchemeListRequest $request)
    {
        CustomSchemeList::addForData($request->all());
        return Response::success();
    }

    /**
     * 2.9 - 更新方案卡清单信息
     * @param CustomSchemeListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomSchemeListRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "产品ID"]);
        CustomSchemeList::updateForData($request->get("id"), $request->all());
        return Response::success();
    }

    /**
     * 2.10 - 删除方案卡清单
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        CustomSchemeList::deleteForIds($request->get("ids"));
        return Response::success();
    }
}