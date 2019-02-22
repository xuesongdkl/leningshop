<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('/goods',GoodsController::class);
    $router->resource('/users',UsersController::class);
    $router->resource('/wx/wx_user',WeixinController::class);     //微信用户管理
    $router->resource('/wx/media',WeixinMediaController::class);   //微信素材管理

    $router->get('/wx/sendmsg','WeixinSendController@sendViewMsgs');    //威胁你消息群发
    $router->post('/wx/sendmsg', 'WeixinSendController@sendMsg');

    $router->resource('/wx/for_media',WeixinForeverMediaController::class);   //微信素材管理

});
