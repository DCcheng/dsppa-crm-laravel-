<?php
/**
 *  FileName: CheckInController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/30
 *  Time: 15:01
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\CheckInRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Models\Department;
use App\Models\Member;
use App\Models\CheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Exception;

class CheckInController extends Controller
{
    /**
     * 5.1 - 获取用户打卡列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        $size = $request->get("size", config("webconfig.listSize"));
        list($condition, $params, $arr, $page, $size) = CheckIn::getParams($request,$size);

        $orderRaw = "a.id asc";
        $model = DB::table(DB::raw(CheckIn::getTableName() . " as a"))->selectRaw("a.*,b.title as department_name,c.truename")
            ->join(DB::raw(Department::getTableName() . " as b"), DB::raw("b.id"), "=", DB::raw("a.department_id"))
            ->join(DB::raw(Member::getTableName() . " as c"), DB::raw("a.uid"), "=", DB::raw("c.uid"));

        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }

        $arr["total"] = $model->count();
        list($arr['pageList'], $arr['totalPage']) = Pager::create($arr["total"], $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["create_time"] = $this->toDate($value["create_time"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 5.2 - 增加考勤信息
     * @param CheckInRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(CheckInRequest $request)
    {
        CheckIn::addForData($request->all());
        return Response::success();
    }

    /**
     * 5.3 - 编辑考勤信息
     * @param CheckInRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CheckInRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "考勤ID"]);
        CheckIn::updateForData($request->get("id"), $request->all());
        return Response::success();
    }

    /**
     * 5.4 - 删除考勤信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        CheckIn::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 5.5 - 导出用户考勤表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(ListRequest $request)
    {
        try {
            list($condition, $params) = Checkin::getParams($request);
            $model = DB::table(DB::raw(CheckIn::getTableName() . " as a"))->selectRaw("a.*,b.title as department_name,c.truename")
                ->join(DB::raw(Department::getTableName() . " as b"), DB::raw("b.id"), "=", DB::raw("a.department_id"))
                ->join(DB::raw(Member::getTableName() . " as c"), DB::raw("a.uid"), "=", DB::raw("c.uid"));

            if ($condition != "") {
                $model->whereRaw($condition, $params);
            }
            $list = $model->orderByRaw("a.uid,a.department_id")->get();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $tableTileArr = [
                "A1" => "员工名",
                "B1" => "部门",
                "C1" => "经度",
                "D1" => "纬度",
                "E1" => "打卡地点",
                "F1" => "打卡时间"
            ];
            foreach ($tableTileArr as $key => $value) {
                $sheet->setCellValue($key, $value);
            }
            $rowNum = 2;
            foreach ($list as $key => $value) {
                $value = (array)$value;
                $sheet->setCellValue("A" . $rowNum, (string)$value["truename"]);
                $sheet->setCellValue("B" . $rowNum, (string)$value["department_name"]);
                $sheet->setCellValue("C" . $rowNum, (string)$value["longitude"]);
                $sheet->setCellValue("D" . $rowNum, (string)$value["latitude"]);
                $sheet->setCellValue("E" . $rowNum, (string)$value["address"]);
                $sheet->setCellValue("F" . $rowNum, (string)$this->toDate($value["create_time"]));
            }

            $writer = new Xlsx($spreadsheet);
            $filename = "uploads/temp/CheckIn_" . $this->toDate(time(), "YmdHi") . ".xlsx";
            $writer->save(storage_path('app') . "/" . $filename);
            return Response::success(["data" => ["filename" => $filename]]);
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 5.6 - 获取考勤详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "考勤ID"]);
        $model = Checkin::where("delete_time",0)->find($request->get("id"));
        if($model){
            $data = (array)$model["attributes"];
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}
