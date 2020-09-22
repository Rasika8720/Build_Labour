<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Hotjar Tracking Code for www.buildlabour.com.au -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:1697073,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Appetiser') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="/favicon-new.ico" type="image/x-icon">
    <link rel="icon" href="/favicon-new.ico" type="image/x-icon">
</head>
<body>
    <div id="app">
        <div class="errors-content-section">
            <div class="row justify-content-center mb-5">
                <img src="/img/buildlabour-fulllogo.png" srcset="/img/buildlabour-fulllogo@2x.png 2x, /img/buildlabour-fulllogo@3x.png 3x">
            </div>

            @yield('content')

        </div>

    </div>
</body>
</html>
