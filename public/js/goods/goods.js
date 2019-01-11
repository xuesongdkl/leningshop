$("#add_cart_btn").click(function(e){
    e.preventDefault();
    var num = $("#goods_num").val();
    var goods_id = $("#goods_id").val();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/cart/add2',
        type    :   'post',
        data    :   {goods_id:goods_id,num:num},
        dataType:   'json',
        success :   function(d){
            if(d.error==301){
                window.location.href=d.url;
            }else{
                alert(d.msg);
                window.location.href="/cart";
            }
        }
    });
});
//删除
$('.del_goods').click(function(e){
    e.preventDefault();
    //alert(111);
    var goods_id=$(this).attr('goods_id');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url :"/cart/del2",
        type:'post',
        data:{goods_id:goods_id},
        dataType:   'json',
        success:function(u){
            if(u.error==301){
                window.location.href=d.url;
            }else{
                alert(u.msg);
                window.location.href="/cart";
            }
        }
    });
});