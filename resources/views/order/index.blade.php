@extends("layouts.bst")
@section('content')
    <h2 style="align-content: center;color: red;">订单列表展示</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <td>order_id</td>
            <td>订单号</td>
            <td>订单总金额</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $v)
            <tr>
                <td>{{$v['order_id']}}</td>
                <td>{{$v['order_sn']}}</td>
                <td>{{$v['order_amount']}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td>
                    @if($v['is_pay']==0&&$v['is_delete']==0)
                        <a href="/pay/o/{{$v['order_id']}}">支付宝支付</a>||
                        <a href="/weixin/pay/test/{{$v['order_sn']}}">微信支付</a>
                    @elseif($v['is_pay']==1)
                        <a href="">已支付</a>
                    @elseif($v['is_delete']==1)
                        <a href="">已删除</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection