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

        //4.客户联系人模块
        $api->get('contacts', 'App\Api\Controllers\V1\CustomContactsController@index');
        $api->post('contacts/add', 'App\Api\Controllers\V1\CustomContactsController@add');
        $api->post('contacts/update', 'App\Api\Controllers\V1\CustomContactsController@update');
        $api->post('contacts/delete', 'App\Api\Controllers\V1\CustomContactsController@delete');

        //5.用户打卡考勤
        $api->get('checkin', 'App\Api\Controllers\V1\CheckInController@index');
        $api->post('checkin/add', 'App\Api\Controllers\V1\CheckInController@add');
        $api->post('checkin/update', 'App\Api\Controllers\V1\CheckInController@update');
        $api->post('checkin/delete', 'App\Api\Controllers\V1\CheckInController@delete');
        $api->get('checkin/export', 'App\Api\Controllers\V1\CheckInController@export');
    });

    $api->group(['prefix' => 'v3'], function () use ($api) {
        $api->get('/index', 'App\Api\Controllers\CustomController@index');
    });
});
