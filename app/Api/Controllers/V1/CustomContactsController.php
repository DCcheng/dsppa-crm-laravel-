<?php
/**
 *  FileName: CustomContactsController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/30
 *  Time: 11:57
 */

namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Utils\Response;
use App\Api\Requests\CustomContactsRequest;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Models\CustomContacts;
use Illuminate\Support\Facades\DB;

class CustomContactsController extends Controller
{
    /**
     * 4.1 - 获取客户联系人列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr) = CustomContacts::getParams($request);
        $orderRaw = "id asc";

        $model = DB::table(DB::raw(CustomContacts::getTableName()))->selectRaw("id,name,sex,phone,email,qq,wechat,job,is_person_in_charge,create_time");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $list = $model->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["create_time"] = $this->toDate($value["create_time"]);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 4.2 - 新增客户联系人
     * @param CustomContactsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(CustomContactsRequest $request)
    {
        CustomContacts::addForData($request->all());
        return Response::success();
    }

    /**
     * 4.3 - 修改客户联系人
     * @param CustomContactsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomContactsRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "联系人ID"]);
        CustomContacts::updateForData($request->get("id"), $request->all());
        return Response::success();
    }

    /**
     * 4.4 - 删除客户联系人信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        CustomContacts::deleteForIds($request->get("ids"));
        return Response::success();
    }
}