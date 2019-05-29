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
        $api->get('customs', 'App\Api\Controllers\V1\CustomController@index');
        $api->post('custom/import', 'App\Api\Controllers\V1\CustomController@import');
        $api->post('custom/add', 'App\Api\Controllers\V1\CustomController@add');
        $api->post('custom/update', 'App\Api\Controllers\V1\CustomController@update');
        $api->post('custom/delete', 'App\Api\Controllers\V1\CustomController@delete');
        $api->post('custom/putonhighseas', 'App\Api\Controllers\V1\CustomController@putonhighseas');
        $api->post('custom/transfer', 'App\Api\Controllers\V1\CustomController@transfer');
        $api->post('custom/receive', 'App\Api\Controllers\V1\CustomController@receive');
        $api->get('custom/getcount', 'App\Api\Controllers\V1\CustomController@getcount');
        $api->get('custom/getlistforgps', 'App\Api\Controllers\V1\CustomController@getlistforgps');
    });

    $api->group(['prefix' => 'v3'], function () use ($api) {
        $api->get('/index', 'App\Api\Controllers\CustomController@index');
    });
});
