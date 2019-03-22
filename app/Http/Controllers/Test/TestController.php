<?php

namespace App\Http\Controllers\Test;

use App\Model\UserModel;
use Illuminate\Database\Eloquent\Model;use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class TestController extends Controller
{
    //

    public function abc()
    {
        var_dump($_POST);echo '</br>';
        var_dump($_GET);echo '</br>';
    }

	public function world1()
	{
		echo __METHOD__;
	}


	public function hello2()
	{
		echo __METHOD__;
		header('Location:/world2');
	}

	public function world2()
	{
		echo __METHOD__;
	}

	public function md($m,$d)
	{
		echo 'm: '.$m;echo '<br>';
		echo 'd: '.$d;echo '<br>';
	}

	public function showName($name=null)
	{
		var_dump($name);
	}

	public function query1()
	{
		$list = DB::table('p_users')->get()->toArray();
		echo '<pre>';print_r($list);echo '</pre>';
	}

	public function query2()
	{
		$user = DB::table('p_users')->where('uid', 3)->first();
		echo '<pre>';print_r($user);echo '</pre>';echo '<hr>';
		$email = DB::table('p_users')->where('uid', 4)->value('email');
		var_dump($email);echo '<hr>';
		$info = DB::table('p_users')->pluck('age', 'name')->toArray();
		echo '<pre>';print_r($info);echo '</pre>';
	}

	public function viewTest1(){
		$data=[];
		return view('test.index',$data);
	}

	public function viewTest2(){
		$list=UserModel::all()->toArray();
		$data=[
			'title'=>'XUESONG',
			'list'=>$list
		];
		return view('test.child',$data);
	}

	/**
	 * Cookie 测试
	 * 2019年1月4日13:25:50
	 */
	public function cookieTest1()
	{
		setcookie('cookie1','lening',time()+1200,'/','',false,true);
		echo '<pre>';print_r($_COOKIE);echo '</pre>';
	}

	public function cookieTest2()
	{
		echo '<pre>';print_r($_COOKIE);echo '</pre>';
	}

	public function sessionTest(Request $request)
	{
		$request->session()->put('aaa','aaaaaa');
		echo '<pre>';print_r($request->session()->all());echo '</pre>';
		//echo '<pre>';print_r(Session::all());echo '</pre>';
	}
	public function checkCookie(){
		echo __METHOD__;
	}

	public function curl1(){

		echo "<pre>";print_r($_GET);echo "</pre>";
		echo "<pre>";print_r($_POST);echo "</pre>";
		echo "<pre>";print_r($_FILES);echo "</pre>";
	}


	//CBC算法
	public function int(){
		$now=$_GET['t'];
		$key='love';
		$salt='sssss';
		$method='AES-128-CBC';
		$iv=substr(md5($now.$salt),5,16);
		$json_str=base64_decode($_POST['data']);
		$dec_data=openssl_decrypt($json_str,$method,$key,OPENSSL_RAW_DATA,$iv);
		if(!empty($dec_data)){
			$time=time();
			$response=[
				'errno'   =>   0,
				'msg'     =>   'ok',
				'data'    =>    'this is a secret'
			];
			$iv2=substr(md5($time.$salt),5,16);
			$enc_data=openssl_encrypt(json_encode($response),$method,$key,OPENSSL_RAW_DATA,$iv2);
			$arr=[
				't'    =>   $time,
				'data' =>   base64_encode($enc_data)
			];
			echo json_encode($arr);
		}
	}

	//签名
	public function sign(){
		$data=[
				'name'=>'杜凯龙',
				'sex'=>'男',
				'age'=>19
		];
		$json_data=json_encode($data);

		$priKey = file_get_contents('./key/rsa_private_key.pem');
		$res = openssl_get_privatekey($priKey);

		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

		openssl_sign($json_data, $sign, $res, OPENSSL_ALGO_SHA256);
		$base_sign=base64_encode($sign);
		$info=[
			'data'  =>  $data,
			'sign'  =>  $base_sign
	 	];
		echo json_encode($info);
	}

	public function sign1(){
		$sign=base64_decode($_POST['sign']);
		//验签
		$pub_key=openssl_get_publickey(file_get_contents('./key/rsa_public_key.pem'));
		$rs=openssl_verify($_POST['data'],$sign,$pub_key,OPENSSL_ALGO_SHA256);
		if($rs){
			echo '验签成功';echo '<br>';
			print_r(json_decode($_POST['data'],true));
		}
	}

	public function api(){
		$dat=$_POST;
		$data=json_encode($dat);
		if(!empty($data)){
			$res=[
				'errno'  =>  0,
				'msg'    =>  $data
			];
		}
		return $res;
	}

	//登录
	public function dologin(){
		$u_name=$_POST['u_name'];
//		echo $u_name;die;
		$u_pwd=$_POST['u_pwd'];
		$data=[
				'u_name' =>$u_name,
				'u_pwd'  =>$u_pwd
		];
//		var_dump($data);die;
		$url='http://xpassport.52xiuge.com/user/login';
		$curl = curl_init();                                        //初始化
		curl_setopt($curl, CURLOPT_URL,$url);                       //设置抓取的url
		curl_setopt($curl, CURLOPT_POST, 1);                        //设置post方式提交
		curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);              //设置获取的信息以文件流的形式返回，而不是直接输出
		curl_setopt($curl, CURLOPT_HEADER, 0);                      //设置头文件的信息作为数据流输出
		$rs = curl_exec($curl);
//		var_dump($rs);die;
		$response=json_decode($rs,true);
		return $response;
	}
}
