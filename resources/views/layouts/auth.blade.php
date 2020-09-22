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

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}?ref={{ strtotime("now") }}" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="/favicon-new.ico" type="image/x-icon">
    <link rel="icon" href="/favicon-new.ico" type="image/x-icon">
</head>
<body>
    <div id="app" class="center-screen">
        <alert></alert>

        <div class="auth-content-section">
            <div class="row justify-content-center">
                <img src="/img/buildlabour-fulllogo.png" srcset="/img/buildlabour-fulllogo@2x.png 2x, /img/buildlabour-fulllogo@3x.png 3x">
            </div>

            <div class="container">
                <div class="row justify-content-center mt-5">
                    <div class="form-card">
                        <div class="form-card-body">

                            @yield('content')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/app.js"></script>

    @stack('scripts')

</body>
</html>
