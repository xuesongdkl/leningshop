<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    echo "<h2 style='color: red;' >欢迎小雪松来到前台首页</h2>";
//    return view('welcome');
});

Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world1','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
Route::get('/user/test','User\UserController@test');
Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>500]);


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');


//Route::match(['get','post'],'/test/abc','Test\TestController@abc');
Route::any('/test/abc','Test\TestController@abc');

//练习
Route::get('/test1','Test\TestController@viewTest1');
Route::get('/test2','Test\TestController@viewTest2');

//用户注册
Route::get('/userreg','User\UserController@reg');
Route::post('/userreg','User\UserController@doreg');

//用户登录
Route::get('/userlogin','User\UserController@login');
Route::post('/userlogin','User\UserController@dologin');
Route::get('/center','User\UserController@center');//个人中心
//退出
Route::get('/quit','User\UserController@quit');

//模板引入静态文件
Route::get('/mvc/test1','Mvc\MvcController@test1');
Route::get('/mvc/bst','Mvc\MvcController@bst');

//Cookie
Route::get('/test/cookie1','Test\TestController@cookieTest1');
Route::get('/test/cookie2','Test\TestController@cookieTest2');
Route::get('/test/session','Test\TestController@sessionTest');
Route::get('/test/check_cookie','Test\TestController@checkCookie')->middleware('check.cookie');

//购物车
Route::get('/cart','Cart\IndexController@index')->middleware('check.login.token');
Route::get('/cart/add/{goods_id}','Cart\IndexController@add')->middleware('check.login.token');
Route::post('/cart/add2','Cart\IndexController@add2');//添加
Route::get('/cart/del/{goods_id}','Cart\IndexController@del')->middleware('check.login.token');
//删除商品
Route::post('/cart/del2','Cart\IndexController@del2')->middleware('check.login.token');
//Route::get('/cart/del2/{goods_id}','Cart\IndexController@del2')->middleware('check.login.token');

//商品列表展示
Route::get('/goods/list','Goods\IndexController@list');
//商品详情
Route::get('/goods/{goods_id}','Goods\IndexController@index');

//订单
Route::get('/order/add','Order\IndexController@add');  //下单
Route::get('/order/list','Order\IndexController@list');  //列表展示

//支付订单
//Route::get('/pay/{order_id}','Pay\IndexController@pay');

Route::get('/test','User\UserController@test');


Route::get('/pay/alipay/test','Pay\AlipayController@test');         //测试
Route::get('/pay/o/{order_id}','Pay\AlipayController@pay')->middleware('check.login.token');  //订单支付
Route::post('/pay/alipay/notify','Pay\AlipayController@aliNotify');        //支付宝支付 异步通知回调
Route::get('/pay/alipay/return','Pay\AlipayController@aliReturn');        //支付宝支付 同步通知回调







Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
