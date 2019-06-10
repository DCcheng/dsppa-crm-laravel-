<?php 
/**
 *  FileName: TrainingFile.php
 *  Description :
 *  Author: DC
 *  Date: 2019-06-10
 *  Time: 02:08
 */

namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class TrainingFile extends Model
{
    protected $table = "training_file";

    public static function addAttributes($model)
    {
        $model->create_time = time();
        $model->delete_time = 0;
        return $model;
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = [];

        $keyword = $request->get('keyword', "");
        if ($keyword != "") {
            $condition[] = "(a.name like ?)";
            $params[] = "%".trim($keyword) . "%";
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}
?>