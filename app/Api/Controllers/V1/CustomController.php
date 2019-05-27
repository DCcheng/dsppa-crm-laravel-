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
use App\Models\CustomContacts;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Kernel\serial\Serial;

class CustomController extends Controller
{
    public function index(Request $request)
    {
        try {
//            Validation::validate($_GET, [
//                [["type"], "required"],
//                [["type", "keyword", "start_time", "end_time"], "string"],
//                [["no_follow_up_days", "size", "page"], "number"]
//            ]);
//            $this->validate($request, [
//                'type' => 'required'
//            ]);

            list($condition, $params, $arr, $page, $size) = Custom::getParams();
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
                $value["follow_up_time"] = Timestamp::toDateAgo($value['follow_up_time'], $time);
                $value["create_time"] = Timestamp::toDate($value["create_time"]);
                $list[$key] = $value;
            }
            $arr['list'] = $list;
            return Response::success(1, "获取客户列表", ["data" => $arr]);
        } catch (Exception $e) {
            return Response::error($e->getCode(), $e->getMessage());
        }
    }

    public function show(Request $request)
    {

        return 112322222;
    }

    public function add(Request $request)
    {
        try {
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
            return Response::success(1, "添加客户成功");
        } catch (Exception $exception) {
            return Response::error($exception->getCode(), $exception->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $contactsData = [
                "contacts_id"=>$request->get("contacts_id"),
                "person_name" => $request->get("person_name"),
                "phone" => $request->get("phone"),
                "sex" => $request->get("sex"),
                "job" => $request->get("job"),
                "charge_status" => $request->get("charge_status")
            ];
            $data = array_diff_key($request->all(), $contactsData);
            DB::transaction(function() use ($data,$contactsData){
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
            return Response::success(1, "修改客户成功");
        } catch (Exception $exception) {
            return Response::error($exception->getCode(), $exception->getMessage());
        }
    }

//    public function
}