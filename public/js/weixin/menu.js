//克隆1
$(document).on('click','#clone',function(){
    var one=$('#one').val();
    var add='<form>' +
        '<select name="" id="">' +
        '<option>一级按钮</option>'+
        '</select>'+'名字：<input type="text" value="'+one+'">'+
        '</form>';
    $('#contain').append(add);
});

//克隆2
$(document).on('click','#clone',function(){
    var two=$('#two').val();
    var url=$('#url').val();
    var key=$('#key').val();
    var add='<form>' +
            '<select>' +
            '<option>二级按钮</option>'+
            '</select>'+
            '二级按钮名字：<input type="text" value='+two+'>'+
            '二级按钮url：<input type="text" value='+url+'>'+
            '二级按钮名字key：<input type="text" value='+key+'>'+
            '</form>';
    $('#contain').append(add);
});

$(document).on('click','#info',function(){
    var one=$('#one').val();
    var two=$('#two').val();
    var url=$('#url').val();
    var key=$('#key').val();
    $.ajax({
        headers :{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     : '/weixin/menu1',
        type    : 'post',
        dataType: 'json',
        data : {one:one,two:two,url:url,key:key},
        success : function(d){
            if(d.errno==0){

            }else{

            }
        }
    })
});