@extends("layouts.bst")
@section('content')
    <h2 style="align-content: center;color: red;">购物车展示</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>商品名称</td>
                <td>购买数量</td>
                <td>添加时间</td>
                <td>操作</td>
            </tr>
        </thead>
        <tbody>
        @foreach($list as $v)
            <tr>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['num']}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                {{--<td>--}}
                    {{--<a href="/cart/del2/{{$v['goods_id']}}" class="btn btn-info del_goods">删除</a>--}}

                {{--</td>--}}
                <td>
                    <button goods_id="{{$v['goods_id']}}" class="btn btn-info del_goods" >删除</button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="/order/add" id="submit_order" class="btn btn-info"> 提交订单 </a>
                    {{--<button >提交订单</button>--}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
@section('footer')
    @parent
    <script src="{{URL::asset('/js/goods/goods.js')}}"></script>
@endsection