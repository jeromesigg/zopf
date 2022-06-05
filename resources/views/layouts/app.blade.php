<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Zopfaktion">
    <meta name="author" content="Jérôme Sigg v/o Amigo">
    <meta name="robots" content="all,follow">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">

    <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>

    <!-- Styles -->

    <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Smartsupp Live Chat script -->
    <script type="text/javascript">
        @if(isset($smartsupp_token))
            var _smartsupp = _smartsupp || {};
            _smartsupp.key = @json($smartsupp_token);
            window.smartsupp||(function(d) {
            var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
            s=d.getElementsByTagName('script')[0];c=d.createElement('script');
            c.type='text/javascript';c.charset='utf-8';c.async=true;
            c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
            })(document);
        @endif
    </script>

    @yield('styles')
</head>
<body>
<div id="app" class="page mainpage">
    @include('includes/topnav')


    <main class="py-4">
        @yield('content')
    </main>
    <footer class="main-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 text-left">
                    <p>Finde weitere Lösungen auf <a href="https://www.cevi.tools">cevi.tools</a></p>
                </div>
                <div class="col-sm-4 text-center">
                    <p>Made by Amirli, {{config('app.version')}}</p>
                </div>
                <div class="col-sm-4 text-right">
                    <p>Finde uns auch auf <a href="https://github.com/cevi/qualifikation">Github</a></p>
                </div>
            </div>
        </div>
    </footer>
</div>
    <!-- jQuery -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
