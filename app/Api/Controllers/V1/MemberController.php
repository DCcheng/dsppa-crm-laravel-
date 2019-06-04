<?php
/**
 *  FileName: MemberController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 11:10
 */


namespace App\Api\Controllers\V1;

use App\Api\Controllers\Controller;
use App\Api\Requests\IdsRequest;
use App\Api\Requests\ListRequest;
use App\Api\Requests\MemberRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Encrypt;
use App\Api\Utils\Pager;
use App\Api\Utils\Response;
use App\Models\CustomFollowUpRecord;
use App\Models\Department;
use App\Models\Member;
use App\Models\MemberAccess;
use App\Models\Role;
use App\Models\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class MemberController extends Controller
{
    /**
     * 13.1 - 获取用户列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr, $page, $size) = Member::getParams($request);
        $time = time();
        $orderRaw = "a.uid desc";
        $model = DB::table(DB::raw(Member::getTableName() . " as a"))->selectRaw("a.uid,a.truename,a.phone,a.attence_num,a.last_login_time,a.last_login_ip,b.title as department_name,d.title as role_name")
            ->join(DB::raw(Department::getTableName() . " as b"), DB::raw("b.id"), "=", DB::raw("a.department_id"))
            ->join(DB::raw(MemberAccess::getTableName() . " as c"), DB::raw("c.uid"), "=", DB::raw("a.uid"))
            ->join(DB::raw(Role::getTableName() . " as d"), DB::raw("d.id"), "=", DB::raw("c.role_id"));
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }

        list($arr['pageList'], $arr['totalPage']) = Pager::create($model->count(), $size);
        $list = $model->forPage($page, $size)->orderByRaw($orderRaw)->get();
        foreach ($list as $key => $value) {
            $value = (array)$value;
            $value["last_login_time"] = $this->toDateAgo($value["last_login_time"], $time);
            $list[$key] = $value;
        }
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }

    /**
     * 13.2 - 新增用户信息
     * @param MemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(MemberRequest $request)
    {
        Member::addForData($request->all());
        return Response::success();
    }

    /**
     * 13.3 - 更新用户信息
     * @param MemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MemberRequest $request)
    {
        $this->validate($request, ['id' => 'required|integer'], [], ["id" => "用户ID"]);
        Member::updateForData($request->get("id"), $request->all());
        return Response::success();
    }

    /**
     * 13.4 - 删除用户信息
     * @param IdsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(IdsRequest $request)
    {
        Member::deleteForIds($request->get("ids"));
        return Response::success();
    }

    /**
     * 13.5 - 修改用户密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changepassword(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'password' => 'required|string',
            'o_password' => 'required|string'], [], ["id" => "用户ID", "password" => "新密码", "o_password" => "旧密码"]);
        $userInfo = config("webconfig.userInfo");
        list("password" => $password) = Encrypt::start($request->get("o_password"), $userInfo['code']);
        if ($password == $userInfo["password"]) {
            Member::updateForData($request->get("id"), ["password" => $request->get("password")]);
            return Response::success();
        } else {
            return Response::fail("所输入的密码与原始密码不一致");
        }
    }

    /**
     * 13.8 - 把用户转移到某个部门
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request)
    {
        $this->validate($request, [
            'uid' => 'required|integer',
            'department_id' => 'required|integer',
            'type' => 'required|string'
        ], [], ["uid" => "用户ID", "department_id" => "部门ID", "type" => "转移类型"]);

        DB::beginTransaction();
        try {
            $type = $request->get("type");
            $uid = (int)$request->get("uid");
            $department_id = $request->get("department_id");
            switch ($type) {
                case "take_away":
                    Member::updateForData($uid, ["department_id" => $department_id]);
                    Custom::whereRaw("uid = ?", [$uid])->update(["department_id" => $department_id]);
                    CustomFollowUpRecord::whereRaw("uid = ?", [$uid])->update(["department_id" => $department_id]);
                    break;
                case "to_member":
                case "to_high_seas":
                    $this->validate($request, ['to_uid' => 'required|integer'], [], ["to_uid" => "目标用户ID"]);
                    Member::updateForData($uid, ["department_id" => $department_id]);
                    $in_high_seas = $type == "to_high_seas" ? 1 : 0;
                    $to_uid = $type == "to_high_seas" ? 0 : $request->get("to_uid");
                    Custom::whereRaw("uid = ?", [$uid])->update(["uid" => $to_uid, "in_high_seas" => $in_high_seas]);
                    CustomFollowUpRecord::whereRaw("uid = ?", [$uid])->update(["uid" => $to_uid]);
                    break;
                default:
                    DB::rollBack();
                    return Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE." - ".Constant::SYSTEM_DATA_EXCEPTION_MESSAGE);
                    break;
            }
            DB::commit();
            return Response::success();
        } catch (Exception $exception) {
            DB::rollBack();
            return Response::fail($exception->getCode()." - ".$exception->getMessage());
        }
    }
}