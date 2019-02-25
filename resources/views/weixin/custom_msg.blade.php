@extends('layouts.bst')

@section('content')
    {{csrf_field()}}
    <div class="container">
        <h2>
            <img src="{{$user['headimgurl']}}" alt="头像" width="52px" class="img-rounded">
            &nbsp;&nbsp;{{$user['nickname']}}
        </h2>
        <div class="chat" id="chat_div">

        </div>
        <hr>
        <form class="form-inline">
            <input type="hidden" value="{{$user['openid']}}" id="openid">
            <input type="hidden" value="1" id="msg_pos">

            <input type="text" class="form-control" id="msg">
            <button type="submit" class="btn btn-primary" id="send_msg_btn">Send Msg</button>
        </form>
    </div>
@endsection

@section('footer')
    @parent
    <script src="{{URL::asset('/js/weixin/weixin.js')}}"></script>
@endsection