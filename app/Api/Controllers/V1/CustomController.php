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
use App\Api\Controllers\Controller;
use App\Models\Custom;
use App\Models\CustomContacts;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Kernel\serial\Serial;
use App\Http\Requests\CustomRequest;

class CustomController extends Controller
{
    //1.1 - 获取客户列表
    public function index(Request $request)
    {
        list($condition, $params, $arr, $page, $size) = Custom::getParams($request);
        $time = time();
        $model = DB::table(DB::raw(Custom::getTableName() . " as a"))->selectRaw("a.*");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw("a.id desc")->get();
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

    //1.4 - 添加客户信息
    public function add(CustomRequest $request)
    {
        $serial = new Serial();
        $contactsData = [
            "person_name" => $request->get("person_name"),
            "phone" => $request->get("phone"),
            "sex" => $request->get("sex"),
            "job" => $request->get("job"),
            "charge_status" => $request->get("charge_status")
        ];
        $data = array_diff_key($request->all(), $contactsData);
        $data["identify"] = $serial->get();
        $model = DB::transaction(function () use ($data, $contactsData) {
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
            return $model;
        });
        $serial->set($model->identify);
        return Response::success();
    }

    //1.5 - 编辑客户信息
    public function update(CustomRequest $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            "contacts_id" => "required|array"
        ],[],["id"=>"客户主键","contacts_id"=>"联系人ID"]);
        $contactsData = [
            "contacts_id" => $request->get("contacts_id"),
            "person_name" => $request->get("person_name"),
            "phone" => $request->get("phone"),
            "sex" => $request->get("sex"),
            "job" => $request->get("job"),
            "charge_status" => $request->get("charge_status")
        ];
        $data = array_diff_key($request->all(), $contactsData);
        DB::transaction(function () use ($data, $contactsData) {
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
        });
        return Response::success();
    }

    //1.6 - 废弃客户信息
    public function delete(Request $request)
    {

    }
}