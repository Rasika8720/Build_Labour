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


    <style>
    .sidebar-logo-image {
        width: 100px !important;
        margin: 5px 4px !important;
    }
    </style>
</head>
<body class="admin">
    <div id="app">
        <alert></alert>

        <div class="container-fluid" style="width: 90%">
            <div class="row">
                @if (isset($_COOKIE['bl_token']))
                    <div class="col-md-2 col-sm-2">
                        @include('layouts.sidebar')
                    </div>

                    <div class="col-md-10 col-sm-10 bl-content">
                        @yield('content')
                    </div>
                @else
                    <div class="col-md-12 col-sm-12">
                        @yield('content')
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="/js/app.js"></script>
    <script src="{{ asset('js/admin/datatable.js') }}"></script>
    @stack('scripts')
</body>
</html>
