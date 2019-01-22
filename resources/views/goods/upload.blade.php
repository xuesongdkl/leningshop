@extends('layouts.bst')

@section('content')
    <div class="container">
        <form method="post" action="/upload/pdf" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="file" name="file">
            <input type="submit" value="UPLOAD">
        </form>
    </div>
@endsection

@section('footer')
    @parent
@endsection
