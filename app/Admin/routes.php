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
    $router->resource('/wx/wx_user',WeixinController::class);
    $router->resource('/wx/media',WeixinMediaController::class);

    $router->get('/wx/sendmsg','WeixinSendController@sendViewMsgs');
    $router->post('/wx/sendmsg', 'WeixinSendController@sendMsg');
//    $router->resource('/wx/send',WeixinSendController::class);

});
