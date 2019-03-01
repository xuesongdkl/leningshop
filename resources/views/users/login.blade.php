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
        <h3 class="form-signin-heading" style="margin-left: 100px;">
            <a href="https://open.weixin.qq.com/connect/qrconnect?appid=wxe24f70961302b5a5&amp;redirect_uri=http%3a%2f%2fmall.77sc.com.cn%2fweixin.php%3fr1%3dhttp%3a%2f%2fxsdkl.52self.cn%2fweixin%2fgetcode&amp;response_type=code&amp;scope=snsapi_login&amp;state=STATE#wechat_redirect">微信登录</a>
        </h3>
    </form>

@endsection