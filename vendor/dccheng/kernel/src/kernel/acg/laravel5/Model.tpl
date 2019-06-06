/**
 *  FileName: {{className}}.php
 *  Description :
 *  Author: DC
 *  Date: {{date}}
 *  Time: {{time}}
 */

namespace {{Namespace}};

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class {{className}} extends Model
{
    protected $table = "{{table}}";

    public static function addAttributes($model)
    {
        //$model->delete_time = 0;
        return $model;
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = [];

        //$keyword = $request->get('keyword', "");
        //if ($keyword != "") {
        //    $condition[] = "(a.name like ? or a.identify like ?)";
        //    $params[] = trim($keyword) . "%";
        //    $params[] = trim($keyword) . "%";
        //}

        //$end_time = $request->get('end_time', "");
        //if ($end_time != "") {
        //    $params[] = strtotime($end_time);
        //   $condition[] = "a.create_time <= ?";
        //}

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}