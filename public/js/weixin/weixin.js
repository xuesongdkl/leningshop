var openid=$('#openid').val();


setInterval(function(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/weixin/sendcustom?openid='+openid+'&pos='+$('#msg_pos').val(),
        type    :   'get',
        dataType:   'json',
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
});