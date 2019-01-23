@extends('layouts.bst');

@section('content')
    <h2>用户登录</h2>
    <form action="/userlogin" method="post">
        {{csrf_field()}}
        用户名：<input type="text" name="name">
        密码：<input type="password" name="password">
        <a href="/userchangePwd">忘记密码？</a>
        <input type="submit" value="登  录">
    </form>
@endsection