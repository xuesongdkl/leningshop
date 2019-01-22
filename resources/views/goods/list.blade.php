@extends("layouts.bst")
@section('content')
    <h2 style="align-content: center;color: red;">商品列表展示</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>goods_id</td>
                <td>商品名称</td>
                <td>库存</td>
                <td>价格</td>
                <td>添加时间</td>
                <td>操作</td>
            </tr>
        </thead>
        <tbody>
        @foreach($list as $v)
            <tr>
                <td>{{$v['goods_id']}}</td>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['store']}}</td>
                <td>{{$v['price']}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td>
                    <a href="/goods/{{$v['goods_id']}}">商品详情</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$list->links()}}
@endsection