<?php
/**
 *  FileName: CustomController.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/21
 *  Time: 15:27
 */


namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Custom;

class CustomController extends Controller
{
    public function index(){
        $list = Custom::all();
        return count($list);;
    }

    public function show(){
        return 112322222;
    }
}