<html>
    <head>
        <title>Lening-@yield('title')</title>
    </head>
    <body>
        @section('header')
            <p style="color: blue;">This is Mother Header.</p>
        @show

        <div class="container">
            @yield('content')
        </div>

        @section('footer')
            <p style="color: blue;">This is Mother Footer.</p>
        @show
    </body>
</html>
