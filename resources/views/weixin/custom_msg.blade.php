@extends('layouts.bst')

@section('content')
    {{csrf_field()}}
    <div class="container">
        <h3>客服私聊</h3>
        <div class="chat" id="chat_div">
           
            {{--<div>--}}
                {{--<div class="input-group">--}}
                    {{--<textarea name="content" class="form-control" cols="50" rows="10" id="area"></textarea>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
        <hr>
        <form class="form-inline">
            <input type="hidden" value="{{$openid}}" id="openid">
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