<?php
/**
 *  FileName: Product.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 16:39
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use App\Models\Custom;
use App\Models\CustomContacts;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $table = "product";

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = array("delete_time = 0");

        $keyword = $request->get("keyword","");
        if ($keyword != "") {
            $condition[] = "(product_name like ?) or (product_model like ?)  or (brand like ?)";
            $params[] = "%".trim($keyword) . "%";
            $params[] = "%".trim($keyword) . "%";
            $params[] = "%".trim($keyword) . "%";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}