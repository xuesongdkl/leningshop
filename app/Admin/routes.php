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

    $router->get('/wx/sendmsg','WeixinSendController@sendViewMsgs');    //微信消息群发
    $router->post('/wx/sendmsg', 'WeixinSendController@sendMsg');

    $router->resource('/wx/for_media',WeixinForeverMediaController::class);   //微信永久素材
    $router->post('/wx/for_media','WeixinForeverMediaController@formTest');
    $router->get('/wx/wx_user/create?user_id={user_id}','WeixinController@create');
    $router->post('/wx/wx_user/test','WeixinController@msgDb');
    $router->post('/wx/wx_user/test1','WeixinController@sendCustomMsgs');


});
