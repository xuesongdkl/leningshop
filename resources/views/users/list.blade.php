@extends("layouts.bst")
@section('content')
    <h2 style="align-content: center;color: red;">用户信息</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>Uid</td><td>用户名</td><td>邮箱</td><td>年龄</td><td>注册时间</td>
            </tr>
        </thead>
        <tbody>
        @foreach($list as $v)
            <tr>
                <td>{{$v['uid']}}</td><td>{{$v['name']}}</td><td>{{$v['email']}}</td><td>{{$v['age']}}</td><td>{{$v['reg_time']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection