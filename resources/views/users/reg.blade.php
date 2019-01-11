@extends("layouts.bst")
@section('content')
    <h2 class="form-signin-heading" style="margin-left: 100px;color:red;">用户注册</h2>
<form class="form-horizontal" method="post" action="/userreg" style="margin-top: 30px">
    {{csrf_field()}}
    <div class="form-group" >
        <label for="inputEmail3" class="col-sm-2 control-label">账号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="u_name" style="width: 300px"  placeholder="请输入账号">
        </div>
    </div>
    <div class="form-group" >

        <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="u_pwd1" style="width: 300px"  placeholder="***">
        </div>
    </div>
    <div class="form-group" >
        <label for="inputPassword3" class="col-sm-2 control-label">确认密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="u_pwd2" style="width: 300px"  placeholder="***">
        </div>
    </div>
    <div class="form-group" >
        <label for="inputEmail3" class="col-sm-2 control-label">邮箱</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="u_email" style="width: 300px"  placeholder="@">
        </div>
    </div>
    <div class="form-group" >
        <label for="inputEmail3" class="col-sm-2 control-label">年龄</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="u_age" style="width: 300px"  placeholder="age">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-info">注 册</button>
        </div>
    </div>
</form>
@endsection
