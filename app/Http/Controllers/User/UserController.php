<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Model\UserModel;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    //

	public  function test(){
		$url='http://xuesong.52self.cn';
		$client=new Client([
				'base_uri'=>$url,
				'timeout'=>2.0,
		]);

		$response=$client->request('GET','order.php');
		echo $response->getBody();

	}
	public function user($uid)
	{
		echo $uid;
	}


	public function add()
	{
		$data = [
			'name'      => str_random(5),
			'age'       => mt_rand(20,99),
			'email'     => str_random(6) . '@gmail.com',
			'reg_time'  => time()
		];

		$id = UserModel::insertGetId($data);
		var_dump($id);
	}


	//注册
	public function reg(){
		return view('users.reg');
	}

	public function doreg(Request $request){
		$name=$request->input('u_name');
		$where=[
				'name'=>$name
		];
		$userinfo=UserModel::where($where)->first();
		if($userinfo) {
			echo "该用户已存在";
			header("refresh:1;url=/userreg");
		}
		$pwd1 = $request->input('u_pwd1');
		$pwd2 = $request->input('u_pwd2');
		if($pwd1 !== $pwd2){
			echo "密码不一致";
			header("refresh:1;url=/userreg");
		}
		$pwd = password_hash($pwd1,PASSWORD_BCRYPT);
		$data=[
				'name'=>$name,
				'password'=>$pwd,
				'email'=>$request->input('u_email'),
				'age'=>$request->input('u_age'),
				'reg_time'=>time()
		];
		$uid=UserModel::insertGetId($data);
		if($uid){
			setcookie('uid',$uid,time()+86400,'/','shop.lening.com',false,true);
			//header("refresh:2;url=/userlogin");
			echo "注册成功,正在跳转";die;
		}else{
			echo "注册失败";die;
			//header("refresh:2;url=/userreg");
		}
	}

	//登录
	public function login(){
		return view('users.login');
	}

	public function dologin(Request $request){
		$name=$request->input('u_name');
		$pwd=$request->input('u_pwd');
		$url=$request->input('redirect');
		$res=UserModel::where(['name'=>$name])->first();
		if($res){
			if(password_verify($pwd,$res->password)){

				$token = substr(md5(time().mt_rand(1,99999)),10,10);
				setcookie('name',$res->name,time()+86400,'/','',false,true);
				setcookie('uid',$res->uid,time()+86400,'/','',false,true);
				setcookie('token',$token,time()+86400,'/','',false,true);
				$request->session()->put('uid',$res->uid);
				$request->session()->put('p_token',$token);
//				header('refresh:1;url="$url"');
				echo "登录成功";die;
			}else{
				echo "账号或者密码错误";die;
			}
		}else{
			echo "用户不存在";die;
		}
	}
	public function center(Request $request){

		if($_COOKIE['token'] != $request->session()->get('p_token')) {
			die('非法请求');
		}else {
			echo "正常请求";
		}

/*		echo 'p_token: '.$request->session()->get('p_token'); echo '</br>';
		echo '<pre>';print_r($_COOKIE);echo '</pre>';
		die;
*/
		if(empty($_COOKIE['name'])){
			header('refresh:1;url=/userlogin');
			echo "请先登录";
			die;
		}else{
			$list=UserModel::all()->toArray();
			$data=[
					'list'=>$list
			];
			return view('users.list',$data);
		}
	}

	//退出
	public function quit(){
		setcookie('uid',null);
		setcookie('token',null);
		setcookie('name',null);
		request()->session()->pull('uid',null);
		request()->session()->pull('p_token',null);
		echo "请重新登录,正在跳转";
		header('Refresh:1;url=/userlogin');
	}



	//api个人中心
	public function acenter(Request $request){
		$uid=$request->input('uid');
		$token=$request->input('token');
		if(empty($uid)||empty($token)){
			$response=[
				'errno'  =>   40003,
				'msg'    =>    '请先登录'
			];
		}else{
			$key='str:u:token:'.$uid;
			$r_token=Redis::hGet($key,'app');
			if($r_token==$token){
				$response=[
					'errno'  =>  0,
					'msg'    =>  'ok'
				];
			}else{
				$response=[
					'errno'   =>   40001,
					'msg'     =>   '请重新登录'
				];
			}
		}
		return $response;
	}
}
