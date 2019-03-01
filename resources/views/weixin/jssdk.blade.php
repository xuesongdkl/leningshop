@extends('layouts.bst')

@section('content')
    <div class="container">
        <h2>JSSDK</h2>
        <button id="btn1">选择照片</button>
        <button id="btn2">微信扫一扫</button>
        <button id="btn3">微信上传图片</button>
        <button id="btn4">获取本地图片接口</button>
        <button id="btn5">开始录音</button>
    </div>

@endsection

@section('footer')
    @parent
    <script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
    <script>
        wx.config({
            debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: "{{$jsconfig['appid']}}", // 必填，公众号的唯一标识
            timestamp: {{$jsconfig['timestamp']}}, // 必填，生成签名的时间戳
            nonceStr: "{{$jsconfig['noncestr']}}", // 必填，生成签名的随机串
            signature: "{{$jsconfig['sign']}}",// 必填，签名
            jsApiList: ['chooseImage','uploadImage','getLocalImgData','startRecord','scanQRCode'] // 必填，需要使用的JS接口列表
        });

        wx.ready(function(){
            $('#btn1').click(function(){
                //照片
                wx.chooseImage({
                    count: 9, // 默认9
                    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                    success: function (res) {
                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    }
                });
            });
            //微信扫一扫
            $('#btn2').click(function(){
                wx.scanQRCode({
                    needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                    scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                    success: function (res) {
                        var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                    }
                });
            });
            //微信上传图片
            $('#btn3').click(function(){
                wx.uploadImage({
                    localId: '', // 需要上传的图片的本地ID，由chooseImage接口获得
                    isShowProgressTips: 1, // 默认为1，显示进度提示
                    success: function (res) {
                        var serverId = res.serverId; // 返回图片的服务器端ID
                    }
                })
            });
            //获取本地图片接口
            $('#btn4').click(function(){
                wx.getLocalImgData({
                    localId: '', // 图片的localID
                    success: function (res) {
                        var localData = res.localData; // localData是图片的base64数据，可以用img标签显示
                    }
                });
            });
            //录音接口
            $('#btn5').click(function() {
                wx.startRecord();
            });
        });
    </script>
@endsection
