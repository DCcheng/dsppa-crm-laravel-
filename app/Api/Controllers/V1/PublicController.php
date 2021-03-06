<?php
/**
 *  FileName: PublicController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 14:31
 */


namespace App\Api\Controllers\V1;


use App\Api\Controllers\Controller;
use App\Api\Utils\Log;
use App\Api\Utils\Response;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\Uploads;
use Exception;
use Kernel\Kernel;

class PublicController extends Controller
{
    /**
     * 13.6 - 用户登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $this->validate($request, ["username" => "required", "password" => "required"], [], ["username" => "用户名", "password" => "密码"]);
            $userInfo = Member::loginByPassword($request);
            config(["webconfig.userInfo" => $userInfo]);
            list($token, $exp) = Kernel::token()->create($userInfo);
            Log::create($request);
            return Response::success(["data" => ["token" => $token, "exp" => $exp]]);
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 13.7 - 注销用户登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            Kernel::token()->invalidate();
            return Response::success();
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 13.12 - 用户刷新令牌
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        try {
            list($token, $exp) = Kernel::token()->refresh();
            return Response::success(["data" => ["token" => $token, "exp" => $exp]]);
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 14.1 - 上传文件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadfile(Request $request)
    {
        try {
            list($file_id, $filename) = Uploads::uploadFile($request);
            return Response::success(["data" => ["file_id" => $file_id, "filename" => $filename]]);
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }

    /**
     * 14.2 - 清理废弃文件
     * @return \Illuminate\Http\JsonResponse
     */
    public function cleanfile()
    {
        try {
            Uploads::cleanFile();
            return Response::success();
        } catch (Exception $exception) {
            return Response::fail($exception->getMessage());
        }
    }
}
