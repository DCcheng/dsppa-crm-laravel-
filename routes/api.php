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
        $api->post('custom/add', 'App\Api\Controllers\V1\CustomController@add');
        $api->post('custom/update', 'App\Api\Controllers\V1\CustomController@update');
    });

    $api->group(['prefix' => 'v3'], function () use ($api) {
        $api->get('/index', 'App\Api\Controllers\CustomController@index');
    });
});
