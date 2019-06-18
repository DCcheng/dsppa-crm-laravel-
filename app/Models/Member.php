<?php
/**
 *  FileName: Member.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 13:42
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use App\Api\Utils\Encrypt;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    protected $table = "member";
    protected $primaryKey = 'uid';

    public static function addAttributes($model)
    {
        $password = request()->get("password", "123456");
        list($password, $code) = Encrypt::start($password);
        $model->password = $password;
        $model->code = (string)$code;
        $model->username = $model->truename;
        $model->status = 1;
        $model->create_time = time();
        $model->delete_time = 0;
        return $model;
    }

    public static function editAttributes($model)
    {
        $password = request()->get("password", "");
        if ($password != "") {
            list($password, $code) = Encrypt::start($password);
            $model->password = $password;
            $model->code = (string)$code;
        }
        return $model;
    }

    public static function loginByPassword(Request $request)
    {
        $username = $request->get("username", "");
        $password = $request->get("password", "");
        $model = self::whereRaw("username = ? and status = 1 and delete_time = 0", [$username])->first();
        if ($model) {
            list($password, $code) = Encrypt::start($password, $model->code);
            if ($model->password == $password) {
                return self::login($model);
            } else {
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_LOGIN_PASSWORD_FAIL_CODE . " - " . Constant::SYSTEM_LOGIN_PASSWORD_FAIL_MESSAGE));
            }
        } else {
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
        }
    }

    public static function login($model)
    {
        DB::beginTransaction();
        try {
            //登录成功后的操作
            $model->last_login_ip = request()->getClientIp();
            $model->last_login_time = time();
            $model->save();

            $info = DB::table(DB::raw(self::getTableName() . " as a"))->selectRaw('a.uid,a.truename,a.avatar,a.department_id,a.password,a.code,d.id as role_id,d.title as rolename,d.menu,d.access,d.data_authority')
                ->join(DB::raw(MemberAccess::getTableName() . " as c"), DB::raw("c.uid"), "=", DB::raw("a.uid"))
                ->join(DB::raw(Role::getTableName() . " as d"), DB::raw("d.id"), "=", DB::raw("c.role_id"))
                ->whereRaw("a.uid = ?", [$model->uid])
                ->first();

            if ($info) {
                $info = (array)$info;
                $info["menu"] = unserialize($info["menu"]);
                $info["access"] = unserialize($info["access"]);
                $info["accessUrlArr"] = Access::selectRaw('url')->whereRaw("status = 1 and id in(" . implode(',', $info["access"]) . ")")->get()->toArray();
                $info["accessUrlArr"] = array_unique(self::toOneDimension($info["accessUrlArr"], 'url'));
                DB::commit();
                return $info;
            } else {
                DB::rollBack();
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE));
        }
    }

    public static function getParams(ListRequest $request, $size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request, $size);

        //根据列表类型返回对应的数据
        //在客户列表中也根据访问用户的数据权限分为了，自己、部门和所有
        $userInfo = config("webconfig.userInfo");
        switch ($userInfo["data_authority"]) {
            case "self":
                $condition[] = "a.uid = ? and a.delete_time = 0";
                $params[] = $userInfo['uid'];
                break;
            case "department":
                $condition[] = "a.department_id = ? and a.delete_time = 0";
                $params[] = $userInfo['department_id'];
                break;
            case "all":
                $condition[] = " a.delete_time = 0";
                break;
            default:
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
                break;
        }

        $keyword = $request->get("keyword", "");
        if ($keyword != "") {
            $condition[] = "(a.username like ? or a.attence_num like ?  or a.phone like ?)";
            $params[] = trim($keyword) . "%";
            $params[] = trim($keyword) . "%";
            $params[] = trim($keyword) . "%";
        }

        $start_time = $request->get("start_time", "");
        if ($start_time != "") {
            $params[] = strtotime($start_time);
            $condition[] = " a.last_login_time >= ?";
        }

        $end_time = $request->get("end_time", "");
        if ($end_time != "") {
            $params[] = strtotime($end_time);
            $condition[] = " a.last_login_time <= ?";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}
