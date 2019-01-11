@extends("layouts.bst")
@section('content')
    <h2 class="form-signin-heading" style="margin-left: 100px;color:red;">请登录</h2>
    <form class="form-horizontal" action="/userlogin" method="post" style="margin-top: 30px">
        {{csrf_field()}}
        <div class="form-group" >
            <label for="inputEmail3" class="col-sm-2 control-label">账号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="u_name" style="width: 300px" placeholder="请输入账号">
            </div>
        </div>
        <div class="form-group" >
            <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="u_pwd" style="width: 300px" placeholder="***">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <input type="checkbox" value="remember-me"> Remember me
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-info">登 录</button>
            </div>
        </div>
    </form>
@endsection