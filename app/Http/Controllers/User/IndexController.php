<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UsersModel;

class IndexController extends Controller
{
    //登录页面
    public function index(){
        return view('user.login');
    }

    public function login(Request $request){
        $name=$request->input('name');
        $pwd=$request->input('password');
        $res=UsersModel::where(['name'=>$name,'pwd'=>$pwd])->first();
        if($res){
            echo "登录成功";
        }else{
            echo "登录失败";
            header("refresh:1;url='/userlogin'");
        }
    }

    //忘记密码 修改密码
    public function fore(){
        return view('user.fore');
    }

    public function changePwd(Request $request){
        $name=$request->input('name');
        $userInfo=UsersModel::where(['name'=>$name])->first();
        $id=$userInfo['id'];
        $newpwd=$request->input('password');
        $res=UsersModel::where(['id'=>$id])->update(['pwd'=>$newpwd]);
        if($res){
            echo "修改密码成功";
        }else{
            echo "修改失败，请重新修改";
            header("refresh:1;url='/userchangePwd'");
        }
    }
}