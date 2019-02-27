
<script src="{{URL::asset('/js/jquery-1.12.4.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.min.js')}}"></script>
<script src="{{URL::asset('/js/qrcode.js')}}"></script>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    {{--<script type="text/javascript" src="/js/jquery.min.js"></script>--}}
    {{--<script type="text/javascript" src="/js/qrcode.js"></script>--}}
</head>
<body>
    <h1 align="center">微信扫码支付<h1>
    <div align="center" id="qrcode"></div>
            <input type="hidden" value="{{$order_id}}" id="order_id">
</body>
</html>
<script>
    var qrcode = new QRCode(document.getElementById('qrcode'), {
        text: "{{$r}}",
        width: 256,
        height: 256,
        colorDark : '#000000',
        colorLight : '#ffffff',
        correctLevel : QRCode.CorrectLevel.H
    })
    setInterval(function(){
        var order_id=$('#order_id').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url     :   '/weixin/pay/qrpay',
            type    :   'post',
            data    :   {order_id:order_id},
            dataType:   'json',
            success :   function(d){
                if(d==1){
                    location.href='/weixin/pay/success';
                }
            }
        });
    },3000)
</script>