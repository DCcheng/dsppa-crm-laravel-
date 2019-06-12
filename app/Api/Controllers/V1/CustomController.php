<?php
/**
 *  FileName: CustomController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/21
 *  Time: 15:27
 */

namespace App\Api\Controllers\V1;

use App\Api\Utils\Constant;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Api\Controllers\Controller;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Requests\CustomRequest;
use App\Models\Custom;
use App\Models\CustomContacts;
use App\Models\Department;
use App\Models\Member;
use App\Models\Uploads;
use App\Api\Utils\ReadFile;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Kernel\Kernel;

class CustomController extends Controller
{
    /**
     * 1.1 - 获取客户列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        $this->validate($request, ['type' => 'required|string'], [], ["type" => "获取列表类型"]);
        list($condition, $params, $arr, $page, $size) = Custom::getParams($request);
        $field = $request->get("field", "desc");
        $order = $request->get("order", "id");
        if (in_array($field, ["follow_up_time", "create_time", "id"]) && in_array($order, ["desc", "asc"])) {
            $orderRaw = "a." . $field . " " . $order;
        } else {
            $orderRaw = "a.id desc";
        }

        $time = time();
        $model = DB::table(DB::raw(Custom::getTableName() . " as a"))->selectRaw("a.id,identify,a.name,cid,level,province,city,area,address,intention,keyword,source,fax,longitude,latitude,discount,follow_up_time,a.create_time,b.name as contacts_name,b.phone")
            ->join(DB::raw(CustomContacts::getTableName()." as b"),DB::raw("b.custom_id"),"=",DB::raw("a.id"))->distinct(DB::raw("a.id"));
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->groupBy(DB::raw("a.id"))->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["key"] = $time . "_" . $value["id"];
            $value["follow_up_time"] = $this->toDateAgo($value['follow_up_time'], $time);
            $value["create_time"] = $this->toDate($value["create_time"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 1.2 - 下载客户上传模板
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(Request $request)
    {
        $filename = "uploads/CustomTemplate.xlsx";
        return Response::success(["data" => ["filename" => $filename]]);
    }

    /**
     * 1.3 - 导入客户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        try {
            $this->validate($request, ['file' => 'required|file'], [], ["file" => "上传文件"]);
            list($file_id, $filename) = Uploads::uploadFile($request, "file", false);
            $row = 2;
            $data = ReadFile::readExcel(storage_path('app') . "/" . $filename, "custom");
            Uploads::cleanFile("id = $file_id");
            DB::beginTransaction();
            try {
                foreach ($data as $key => $value) {
                    $row++;
                    $serial = Kernel::serial();
                    $contactsData = [
                        "person_name" => $value["person_name"],
                        "phone" => $value["phone"],
                        "sex" => $value["sex"]
                    ];
                    $data = array_diff_key($value, $contactsData);
                    $data["identify"] = $serial->get();
                    $model = Custom::addForData($data);
                    CustomContacts::addForData([
                        "custom_id" => $model->id,
                        "name" => $contactsData["person_name"],
                        "phone" => $contactsData["phone"],
                        "sex" => $contactsData["sex"],
                        "job" => "",
                        "is_person_in_charge" => 1
                    ]);
                    $serial->set($model->identify);
                }
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
     * 1.4 - 添加客户信息
     * @param CustomRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add(CustomRequest $request)
    {
        $serial = Kernel::serial();
        $contactsData = [
            "person_name" => $request->get("person_name"),
            "phone" => $request->get("phone"),
            "sex" => $request->get("sex"),
            "job" => $request->get("job"),
            "charge_status" => $request->get("charge_status")
        ];
        $data = array_diff_key($request->all(), $contactsData);
        $data["identify"] = $serial->get();
        DB::beginTransaction();
        try {
            $model = Custom::addForData($data);
            foreach ($contactsData["person_name"] as $key => $value) {
                CustomContacts::addForData([
                    "custom_id" => $model->id,
                    "name" => $value,
                    "phone" => $contactsData["phone"][$key],
                    "sex" => $contactsData["sex"][$key],
                    "job" => $contactsData["job"][$key],
                    "is_person_in_charge" => $contactsData["charge_status"][$key]
                ]);
            }
            $serial->set($model->identify);
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 1.5 - 编辑客户信息
     * @param CustomRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer', "contacts_id" => "required|array"], [], ["id" => "客户主键", "contacts_id" => "联系人ID"]);
        $contactsData = [
            "contacts_id" => $request->get("contacts_id"),
            "person_name" => $request->get("person_name"),
            "phone" => $request->get("phone"),
            "sex" => $request->get("sex"),
            "job" => $request->get("job"),
            "charge_status" => $request->get("charge_status")
        ];
        $data = array_diff_key($request->all(), $contactsData);
        DB::beginTransaction();
        try {
            $model = Custom::updateForData($data["id"], $data);
            foreach ($_POST["contacts_id"] as $key => $value) {
                if ($value == 0) {
                    CustomContacts::addForData([
                        "custom_id" => $model->id,
                        "name" => $contactsData["person_name"][$key],
                        "phone" => $contactsData["phone"][$key],
                        "sex" => $contactsData["sex"][$key],
                        "job" => $contactsData["job"][$key],
                        "is_person_in_charge" => $contactsData["charge_status"][$key]
                    ]);
                } else {
                    CustomContacts::updateForData($contactsData["contacts_id"][$key], [
                        "name" => $contactsData["person_name"][$key],
                        "phone" => $contactsData["phone"][$key],
                        "sex" => $contactsData["sex"][$key],
                        "job" => $contactsData["job"][$key],
                        "is_person_in_charge" => $contactsData["charge_status"][$key]
                    ]);
                }
            }
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 1.6 - 废弃客户信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        Custom::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 1.7 - 把客户投放到公海池
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putonhighseas(IdsRequest $request)
    {
        Custom::updateForIds($request->get("ids"), ["uid" => 0, "in_high_seas" => 1]);
        return Response::success();
    }

    /**
     * 1.8 - 把客户转移到其他销售经理
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(IdsRequest $request)
    {
        $this->validate($request, ['uid' => 'required|integer'], [], ["uid" => "用户ID"]);
        Custom::updateForIds($request->get("ids"), ["uid" => $request->get("uid")]);
        return Response::success();
    }

    /**
     * 1.9 - 领取客户
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function receive(IdsRequest $request)
    {
        Custom::updateForIds($request->get("ids"), ["uid" => config("webconfig.userInfo")["uid"], "in_high_seas" => 0]);
        return Response::success();
    }

    /**
     * 1.10 - 获取客户统计信息
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getcount(ListRequest $request)
    {
        list($condition, $params, $arr) = Custom::getCountParams($request);
        $time = time();
        $total = 0;
        $model = DB::table(DB::raw(Custom::getTableName() . " as a"))->selectRaw("a.uid,b.truename,c.title as department_name,count(a.id) as total")
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
     * 1.11 - 根据当前GPS获取客户列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getlistforgps(ListRequest $request)
    {
        $this->validate($request, ['longitude' => 'required|numeric', 'latitude' => 'required|numeric'], [], ["longitude" => "经度", "latitude" => "纬度"]);
        $request->merge(["type" => "list"]);
        list($condition, $params, $arr, $page, $size) = Custom::getParams($request);
        $time = time();
        $model = DB::table(DB::raw(Custom::getTableName() . " as a"))->selectRaw("a.id,a.identify,a.name,a.address,Round(6378.138 * 2 * ASIN(SQRT(POW(SIN((" . $request->get("latitude") . " * PI() / 180 - latitude * PI() / 180) / 2),2) + COS(" . $request->get("latitude") . " * PI() / 180) * COS(latitude * PI() / 180) * POW(SIN( ( " . $request->get("longitude") . " * PI() / 180 - longitude * PI() / 180 ) / 2 ), 2 ) ) ) * 1000) as `distance`");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw("distance asc")->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["key"] = $time . "_" . $value["id"];
            $value["distance"] = $this->toDistanceStr($value["distance"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 1.12 - 获取客户详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "客户ID"]);
        $model = Custom::where("delete_time",0)->find($request->get("id"),["id","identify","name","cid","level","province","city","area","address","intention","keyword","source","fax","longitude","latitude","discount","follow_up_time","create_time"]);
        if($model){
            $data = (array)$model["attributes"];
            $data["follow_up_time"] = $this->toDateAgo($data['follow_up_time'], time());
            $contactInfo = CustomContacts::whereRaw("custom_id = ? and delete_time = 0",[$data["id"]])->first(["name","phone"]);
            $data["contacts_name"] = $contactInfo->name;
            $data["phone"] = $contactInfo->phone;
            $data["create_time"] = $this->toDate($data["create_time"]);
            return Response::success(["data"=>$data]);
        }else{
            return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
        }
    }
}