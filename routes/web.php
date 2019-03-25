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

//Route::get('/', function () {
////    echo "<h2 style='color: red;' >欢迎小雪松来到前台首页</h2>";
////    $current_url='http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
////    $data=[
////        'login'   =>  $request->get('is_login'),
////        'current_url'  =>  $current_url
////    ];
////    return view('welcome',$data);
//    return view('welcome');
//});

Route::get('/','Home\IndexController@index')->middleware('check.login.token');



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


Route::get('/test4',function(){
    echo "111";
});
Route::middleware(['log.click'])->group(function(){
    Route::any('/test/abc','Test\TestController@abc');
    Route::get('/test/3',function(){
        echo "111";
    });
    Route::get('/test/cookie1','Test\TestController@cookieTest1');
    Route::get('/test/cookie2','Test\TestController@cookieTest2');
    Route::get('/test/session','Test\TestController@sessionTest');
    Route::get('/test/check_cookie','Test\TestController@checkCookie')->middleware('check.cookie');

    Route::get('/test/update/goods/{goods_id}','Goods\IndexController@updateGoodsInfo');
});



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>500]);


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');

//练习
//Route::match(['get','post'],'/test/abc','Test\TestController@abc');
//Route::any('/test/abc','Test\TestController@abc');
//Route::get('/test1','Test\TestController@viewTest1');
//Route::get('/test2','Test\TestController@viewTest2');

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
//Route::get('/test/cookie1','Test\TestController@cookieTest1');
//Route::get('/test/cookie2','Test\TestController@cookieTest2');
//Route::get('/test/session','Test\TestController@sessionTest');
//Route::get('/test/check_cookie','Test\TestController@checkCookie')->middleware('check.cookie');

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
//文件上传
Route::get('/upload','Goods\IndexController@uploadIndex');
Route::post('/upload/pdf','Goods\IndexController@uploadPdf');

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




////用户登录
//Route::get('/userlogin','User\IndexController@index');
//Route::post('/userlogin','User\IndexController@login');
////忘记密码
//Route::get('/userchangePwd','User\IndexController@fore');
//Route::post('/userchangePwd','User\IndexController@changePwd');


//在线订座
Route::get('/movie','Movie\IndexController@index');

//微信
Route::get('/weixin/refresh_token','Weixin\WeixinController@refreshToken');     //刷新token
Route::get('/weixin/test','Weixin\WeixinController@test');
Route::get('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::post('/weixin/valid','Weixin\WeixinController@validToken');

Route::get('/weixin/create_menu','Weixin\WeixinController@createMenu'); //创建菜单
Route::get('/weixin/sendsgs','Weixin\WeixinController@sendMsgs');//群发消息

Route::get('/form/show','Weixin\WeixinController@formShow');     //表单测试
Route::post('/form/test','Weixin\WeixinController@formTest');     //表单测试



Route::get('/weixin/material/list','Weixin\WeixinController@materialList');     //获取永久素材列表
Route::get('/weixin/material/upload','Weixin\WeixinController@upMaterial');     //上传永久素材
Route::post('/weixin/material','Weixin\WeixinController@materialTest');     //创建菜单


Route::get('/weixin/sendchat','Weixin\WeixinController@sendCustomMsgsView');  //客服接口--发消息
Route::get('/weixin/sendcustom','Weixin\WeixinController@sendCustomMsgs');
Route::post('/weixin/sendcustom','Weixin\WeixinController@msgDb');

//微信支付
Route::get('/weixin/pay/test/{order_id}','Weixin\PayController@test');      //微信支付
Route::get('/weixin/pay/success','Weixin\PayController@success');      //微信支付
Route::post('/weixin/pay/notice','Weixin\PayController@notice');     //微信支付通知回调
Route::post('/weixin/pay/qrpay','Weixin\PayController@qrpay');     //微信支付通知回调

//微信登录
//Route::get('/weixin/login','Weixin\WeixinController@login');     //视图页面
Route::get('/weixin/getcode','Weixin\WeixinController@getCode');     //接收code

//微信jssdk
Route::get('/weixin/jssdk/test','Weixin\WeixinController@jssdkTest');






//考试
Route::get('/weixin/get_token','Weixin\WeixinController@getAccessToken');     //获取access_token
Route::get('/weixin/menu','Weixin\WeixinController@createMenuView');     //创建菜单视图
Route::post('/weixin/menu1','Weixin\WeixinController@createMenuNew');     //创建菜单





//curl
Route::any('/test/curl1','Test\TestController@curl1');

//CBC算法
Route::post('/test/cbc','Test\TestController@int');
//签名
Route::post('/test/sign','Test\TestController@sign');

Route::post('/test/sign1','Test\TestController@sign1');

Route::post('/test/api','Test\TestController@api');


//web登录授权
Route::post('/usera','Test\TestController@dologin');

Route::post('/acenter','User\UserController@acenter');        //app个人中心

