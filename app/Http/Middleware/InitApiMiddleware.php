<?php

namespace App\Http\Middleware;

use Closure;

class InitApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $json = '{"uid":"1","truename":"DC","avatar":null,"department_id":"1","password":"e080faa8b87bfc8cab2a6a24aab312a2","code":"3015","role_id":"1","rolename":"\u8d85\u7ea7\u7ba1\u7406\u5458","menu":["15","16","17","18","19","20","21","22","12","3","7","10","13"],"access":["39","38","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59","60","61","62","63","64","65","66","67","68","69","70","71","72","73","74","75","76","77","21","22","37","23","24","25","26","78","79","11","1","2","3","4","9","12","5","6","7","8","10","20","15","14","16","17","18","19","80","81","82","83","84","85","89","90","91","92","93","94","95","96","97","86","87","88"],"data_authority":"all","accessUrlArr":{"0":"access\/getlist","1":"access\/add","2":"access\/update","3":"access\/delete","4":"menu\/getlist","5":"menu\/add","6":"menu\/update","7":"menu\/delete","8":"9.5 - \u83b7\u53d6\u8282\u70b9\u5206\u7c7b\u6811","9":"menu\/getcategory","10":"*\/*","12":"role\/getlist","14":"role\/add","15":"role\/update","16":"role\/delete","17":"role\/gettrees","18":"menu\/getmenu","20":"category\/getlist","21":"category\/add","22":"category\/update","23":"category\/delete","24":"category\/getcategory","25":"category\/getalllist","26":"custom\/getlist","28":"custom\/download","29":"custom\/import","30":"custom\/add","31":"custom\/update","32":"custom\/delete","33":"custom\/putonhighseas","34":"custom\/transfer","35":"custom\/receive","36":"custom\/getcount","38":"customscheme\/getlist","39":"customscheme\/add","40":"customscheme\/update","41":"customscheme\/delete","43":"customscheme\/download","44":"customscheme\/import","49":"customfollowup\/getlist","50":"customfollowup\/getfilelist","51":"customfollowup\/add","52":"customfollowup\/update","53":"customfollowup\/delete","55":"customcontacts\/getlist","56":"customcontacts\/add","57":"customcontacts\/update","58":"customcontacts\/delete","60":"checkin\/getlist","61":"checkin\/add","62":"checkin\/update","63":"checkin\/delete","64":"checkin\/export","67":"log\/getlist","69":"department\/getlist","70":"department\/add","71":"department\/update","72":"department\/delete","73":"department\/getcategory","75":"public\/uploadfile","76":"public\/cleanfile","78":"member\/getlist","79":"member\/add","80":"member\/update","81":"member\/delete","82":"member\/changepassword","83":"public\/login","84":"public\/logout","85":"member\/transfer"},"exp":1564101419}';
        config(["webconfig.userInfo"=>json_decode($json,true)]);
        return $next($request);
    }
}
