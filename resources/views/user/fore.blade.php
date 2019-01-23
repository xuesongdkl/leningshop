@extends('layouts.bst');

@section('content')
    <h2>忘记密码</h2>
    <form action="/userchangePwd" method="post">
        {{csrf_field()}}
        用户名：<input type="text" name="name">
        新密码：<input type="password" name="password">
        <input type="submit" value="提 交">
    </form>
@endsection