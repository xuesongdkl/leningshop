@extends('layouts.bst')

@section('content')
    {{csrf_field()}}
    <button id="first">一级按钮</button>
    <form>
        名字：<input type="text" id="one">
    </form>
    <button id="clone">克隆</button>
    <br>
    <button>二级按钮</button>
    <button id="clone">克隆</button>
    <br>
    按钮类型
    <select>
        <option value="">请选择</option>
        <option value="">一级按钮</option>
        <option value="">二级按钮</option>
    </select>
    <br>
    <div id="new">
        <form>
            二级按钮名字：<input type="text" id="two"><br>
            二级按钮url：<input type="text" id="url"><br>
            二级按钮名字key：<input type="text" id="key"><br>
        </form>
    </div>
    <button id="info">SUBMIT</button>
    <hr>

    <div id="contain">

    </div>
@endsection
@section('footer')
    @parent
    <script src="/js/weixin/menu.js"></script>
@endsection