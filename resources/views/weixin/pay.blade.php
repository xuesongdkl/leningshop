@extends('layouts.bst')
@section('title')
@endsection
@section('header')
@endsection
@section('content')
    <input type="hidden" value="{{$curl}}" id="code_url">
    <input type="hidden" value="{{$order_id}}" id="order_id">
    <div id="code" align="center"></div>
    <div style="color: red;padding-left: 500px;padding-top: 50px;">请使用微信扫一扫付款</div>
@endsection
@section('footer')
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
{{--<script src="/bootstrap/js/jquery.qrcode.min.js"></script>--}}
<script>
    $(function(){
        var code_url=$('#code_url').val()
        console.log(code_url)
        $("#code").qrcode({
            render: "canvas", //table方式
            width: 200, //宽度
            height:200, //高度
            text:code_url //任意内容
        });
    });
    var issuccess=function(){
        var order_id=$('#order_id').val();
        $.post(
            '/weixin/pay/issuccess',
            {order_id:order_id},
            function(msg) {
                if(msg==1){
                    location='/weixin/pay/success'
                }
            }
        )
    };
    var s=setInterval(function(){
        issuccess();
    },300)
</script>