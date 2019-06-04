<?php

namespace App\Http\Middleware;

use App\Api\Utils\Constant;
use App\Api\Utils\Log;
use App\Api\Utils\Response;
use Closure;
use Kernel\Ftoken\Token;
use Kernel\Ftoken\TokenException;

class InitApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $userInfo = Token::validate();
            config(["webconfig.userInfo" => $userInfo]);
            Log::create($request);
            $actionName = explode("\\", $request->route()->getActionName());
            list($controllerName, $methodName) = explode("@", end($actionName));
            $url = $controllerName . "/" . $methodName;
            if (!in_array($url, $userInfo["accessUrlArr"])) {
                return Response::fail(Constant::SYSTEM_NO_ACTION_AUTHORITY_CODE . " - " . Constant::SYSTEM_NO_ACTION_AUTHORITY_MESSAGE, 403);
            }
            return $next($request);
        } catch (TokenException $exception) {
            return Response::fail($exception->getCode() . " - " . $exception->getMessage(), 401);
        }
    }
}
