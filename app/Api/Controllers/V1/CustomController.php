<?php
/**
 *  FileName: CustomController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/21
 *  Time: 15:27
 */


namespace App\Api\Controllers\V1;
use App\Api\Utils\Response;
use App\Http\Controllers\Controller;
use App\Models\Custom;
use App\Models\Model;
use Dingo\Api\Http\Request;

class CustomController extends Controller
{
    public function index(Request $request){
//        var_dump($request->all());
//        $model = new Custom;
//        echo "<pre>";
//        $model->name = "test";
//        var_dump($model["attributes"]);
//        die();
//        $model = new Custom;
//        echo $model->getTable();
//        echo Custom::getTable();
//        die();
//        $list = Custom::where([["name","like","%有限公司%"]])->offset(2)->limit(1)->orderByRaw("id desc,name")->get(["id","name"]);
        $list = Custom::whereRaw("name like :keyword",[":keyword"=>"%有限公司%"])
//            ->join()
            ->offset(1)->limit(1)->orderByRaw("id desc,name")->get(["id","name"]);
        return Response::success(1,"123123123",["data"=>["list"=>$list]]);
    }

    public function show(){
        return 112322222;
    }
}