<?php
/**
 *  FileName: ProductController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 16:48
 */


namespace App\Api\Controllers\V1;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Response;
use App\Models\Product;

class ProductController
{
    /**
     * 2.11 - 获取产品列表
     * @param ListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListRequest $request)
    {
        list($condition, $params, $arr) = Product::getParams($request);
        $model = Product::selectRaw("id,product_name,product_model,brand");
        if ($condition != "") {
            $model->whereRaw($condition, $params);
        }
        $list = $model->orderBy("id desc")->get();
        $arr['list'] = $list;
        return Response::success(["data" => $arr]);
    }
}