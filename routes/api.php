<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your Api!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version(['v1', 'v2', 'v3'], function ($api) {
    $api->group(['prefix' => 'v1'], function () use ($api) {
        $api->post("public/login", 'App\Api\Controllers\V1\PublicController@login');
    });
    $api->group(['middleware' => ['initApi'],'prefix' => 'v1'], function () use ($api) {
        //1.客户管理模块
        $api->get('customs', 'App\Api\Controllers\V1\CustomController@index');
        $api->get('customs/download', 'App\Api\Controllers\V1\CustomController@download');
        $api->post('customs/import', 'App\Api\Controllers\V1\CustomController@import');
        $api->post('customs/add', 'App\Api\Controllers\V1\CustomController@add');
        $api->post('customs/update', 'App\Api\Controllers\V1\CustomController@update');
        $api->post('customs/delete', 'App\Api\Controllers\V1\CustomController@delete');
        $api->post('customs/putonhighseas', 'App\Api\Controllers\V1\CustomController@putonhighseas');
        $api->post('customs/transfer', 'App\Api\Controllers\V1\CustomController@transfer');
        $api->post('customs/receive', 'App\Api\Controllers\V1\CustomController@receive');
        $api->get('customs/getcount', 'App\Api\Controllers\V1\CustomController@getcount');
        $api->get('customs/getlistforgps', 'App\Api\Controllers\V1\CustomController@getlistforgps');
        $api->get('customs/show', 'App\Api\Controllers\V1\CustomController@show');

        //2.客户方案卡
        $api->get('scheme', 'App\Api\Controllers\V1\CustomSchemeController@index');
        $api->post('scheme/add', 'App\Api\Controllers\V1\CustomSchemeController@add');
        $api->post('scheme/update', 'App\Api\Controllers\V1\CustomSchemeController@update');
        $api->post('scheme/delete', 'App\Api\Controllers\V1\CustomSchemeController@delete');
        $api->get('schemelist', 'App\Api\Controllers\V1\CustomSchemeListController@index');
        $api->get('schemelist/download', 'App\Api\Controllers\V1\CustomSchemeListController@download');
        $api->post('schemelist/import', 'App\Api\Controllers\V1\CustomSchemeListController@import');
        $api->post('schemelist/add', 'App\Api\Controllers\V1\CustomSchemeListController@add');
        $api->post('schemelist/update', 'App\Api\Controllers\V1\CustomSchemeListController@update');
        $api->post('schemelist/delete', 'App\Api\Controllers\V1\CustomSchemeListController@delete');

        //3.客户根据
        $api->get('followup', 'App\Api\Controllers\V1\CustomFollowUpController@index');
        $api->get('followup/files', 'App\Api\Controllers\V1\CustomFollowUpController@files');
        $api->post('followup/add', 'App\Api\Controllers\V1\CustomFollowUpController@add');
        $api->post('followup/update', 'App\Api\Controllers\V1\CustomFollowUpController@update');
        $api->post('followup/delete', 'App\Api\Controllers\V1\CustomFollowUpController@delete');
        $api->get('followup/getcount', 'App\Api\Controllers\V1\CustomFollowUpController@getcount');

        //4.客户联系人模块
        $api->get('contacts', 'App\Api\Controllers\V1\CustomContactsController@index');
        $api->post('contacts/add', 'App\Api\Controllers\V1\CustomContactsController@add');
        $api->post('contacts/update', 'App\Api\Controllers\V1\CustomContactsController@update');
        $api->post('contacts/delete', 'App\Api\Controllers\V1\CustomContactsController@delete');
        $api->get('contacts/show', 'App\Api\Controllers\V1\CustomContactsController@show');

        //5.用户打卡考勤
        $api->get('checkin', 'App\Api\Controllers\V1\CheckInController@index');
        $api->post('checkin/add', 'App\Api\Controllers\V1\CheckInController@add');
        $api->post('checkin/update', 'App\Api\Controllers\V1\CheckInController@update');
        $api->post('checkin/delete', 'App\Api\Controllers\V1\CheckInController@delete');
        $api->get('checkin/export', 'App\Api\Controllers\V1\CheckInController@export');
        $api->get('checkin/show', 'App\Api\Controllers\V1\CheckInController@show');

        //6.知识库
        $api->get('training', 'App\Api\Controllers\V1\TrainingFileController@index');
        $api->post('training/add', 'App\Api\Controllers\V1\TrainingFileController@add');
        $api->post('training/update', 'App\Api\Controllers\V1\TrainingFileController@update');
        $api->post('training/delete', 'App\Api\Controllers\V1\TrainingFileController@delete');
        $api->get('training/show', 'App\Api\Controllers\V1\TrainingFileController@show');

        //7.分类接口
        $api->get('category', 'App\Api\Controllers\V1\CategoryController@index');
        $api->get('category/all', 'App\Api\Controllers\V1\CategoryController@all');
        $api->post('category/add', 'App\Api\Controllers\V1\CategoryController@add');
        $api->post('category/update', 'App\Api\Controllers\V1\CategoryController@update');
        $api->post('category/delete', 'App\Api\Controllers\V1\CategoryController@delete');
        $api->get('category/tree', 'App\Api\Controllers\V1\CategoryController@tree');
        $api->get('category/show', 'App\Api\Controllers\V1\CategoryController@show');

        //8.日志接口
        $api->get('log', 'App\Api\Controllers\V1\LogController@index');
        $api->post('log/add', 'App\Api\Controllers\V1\LogController@add');
        $api->post('log/update', 'App\Api\Controllers\V1\LogController@update');
        $api->post('log/delete', 'App\Api\Controllers\V1\LogController@delete');

        //9.功能访问节点接口
        $api->get('access', 'App\Api\Controllers\V1\AccessController@index');
        $api->post('access/add', 'App\Api\Controllers\V1\AccessController@add');
        $api->post('access/update', 'App\Api\Controllers\V1\AccessController@update');
        $api->post('access/delete', 'App\Api\Controllers\V1\AccessController@delete');
        $api->get('access/tree', 'App\Api\Controllers\V1\AccessController@tree');
        $api->get('access/show', 'App\Api\Controllers\V1\AccessController@show');

        //10.菜单接口
        $api->get('menu', 'App\Api\Controllers\V1\MenuController@index');
        $api->post('menu/add', 'App\Api\Controllers\V1\MenuController@add');
        $api->post('menu/update', 'App\Api\Controllers\V1\MenuController@update');
        $api->post('menu/delete', 'App\Api\Controllers\V1\MenuController@delete');
        $api->get('menu/tree', 'App\Api\Controllers\V1\MenuController@tree');
        $api->get('menu/leftmenu', 'App\Api\Controllers\V1\MenuController@leftmenu');

        //11.角色接口
        $api->get('role', 'App\Api\Controllers\V1\RoleController@index');
        $api->post('role/add', 'App\Api\Controllers\V1\RoleController@add');
        $api->post('role/update', 'App\Api\Controllers\V1\RoleController@update');
        $api->post('role/delete', 'App\Api\Controllers\V1\RoleController@delete');
        $api->get('role/trees', 'App\Api\Controllers\V1\RoleController@trees');
        $api->get('role/all', 'App\Api\Controllers\V1\RoleController@all');
        $api->get('role/show', 'App\Api\Controllers\V1\RoleController@show');

        //12.组织架构接口
        $api->get('department', 'App\Api\Controllers\V1\DepartmentController@index');
        $api->post('department/add', 'App\Api\Controllers\V1\DepartmentController@add');
        $api->post('department/update', 'App\Api\Controllers\V1\DepartmentController@update');
        $api->post('department/delete', 'App\Api\Controllers\V1\DepartmentController@delete');
        $api->get('department/tree', 'App\Api\Controllers\V1\DepartmentController@tree');
        $api->post('department/transfer','App\Api\Controllers\V1\DepartmentController@transfer');
        $api->get('department/all', 'App\Api\Controllers\V1\DepartmentController@all');

        //13.用户接口
        $api->get('member', 'App\Api\Controllers\V1\MemberController@index');
        $api->post('member/add', 'App\Api\Controllers\V1\MemberController@add');
        $api->post('member/update', 'App\Api\Controllers\V1\MemberController@update');
        $api->post('member/delete', 'App\Api\Controllers\V1\MemberController@delete');
        $api->post('member/changepassword', 'App\Api\Controllers\V1\MemberController@changepassword');
        $api->get("public/logout", 'App\Api\Controllers\V1\PublicController@logout');
        $api->post('member/transferdepartment', 'App\Api\Controllers\V1\MemberController@transferDepartment');
        $api->get("member/departmentall", 'App\Api\Controllers\V1\MemberController@departmentall');
        $api->post('member/transferdata', 'App\Api\Controllers\V1\MemberController@transferData');

        //14.文件管理
        $api->post("public/uploadfile", 'App\Api\Controllers\V1\PublicController@uploadfile');
        $api->get("public/cleanfile", 'App\Api\Controllers\V1\PublicController@cleanfile');
    });

    $api->group(['prefix' => 'v3'], function () use ($api) {
        $api->get('/index', 'App\Api\Controllers\CustomController@index');
    });
});
