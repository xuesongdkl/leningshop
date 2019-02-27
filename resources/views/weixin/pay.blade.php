{{--<script type="text/javascript" src="/js/jquery-1.12.4.min.js"></script>--}}<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/qrcode.js"></script>
</head>
<body>
    <h1 align="center">微信扫码支付<h1>
    <div align="center" id="qrcode"></div>
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
</script>