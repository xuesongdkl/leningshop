<script src="/js/jquery-1.12.4.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <h3>客服私聊</h3>
    <div class="chat" id="chat_div">

    </div>
    <hr>
    <form class="form-inline">
        <input type="hidden" value="{{$user_info['openid']}}" class="openid">
        <input type="hidden" value="1" class="msg_pos">

        <input type="text" class="form-control" id="msg">
        <button type="submit" class="btn btn-primary" id="send_msg_btn">Send Msg</button>
    </form>
</div>
<script>
    var openid=$('.openid').val();
    var msg_pos=$('.msg_pos').val();
    setInterval(function(){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url     :   '{{url("admin/wx/wx_user/test1")}}',
            type    :   'post',
            dataType:   'json',
            data    :{openid:openid,msg_pos:msg_pos},
            success :   function(d){
                if(d.errno==0){  //服务器响应正常
                    //数据填充
                    var msg_str='<blockquote>'+ d.data.openid+'<p>'+ d.data.msg+'</p>'+'</blockquote>';
                    $("#chat_div").append(msg_str);
                    $("#msg_pos").val(d.data.id);
                }else{

                }
            }
        });
    },5000);

    //客服发送消息
    $("#send_msg_btn").click(function(e){
        e.preventDefault();
        var msg = $("#msg").val().trim();
        var msg_str = '<p style="color: mediumpurple"> '+msg+'</p>';
        $("#chat_div").append(msg_str);
        $("#msg").val("");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url     :   "{{url('admin/wx/wx_user/test')}}",
            type    :   'post',
            data    :    {msg:msg,openid:openid,pos:2},
            dataType:   'json',
            success :   function(d){
                if(d.errno==0){  //服务器响应正常

                }else{

                }
            }
        });
    });

</script>