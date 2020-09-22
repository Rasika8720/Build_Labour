<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-147131520-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-147131520-1');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MBRDV5K');</script>
    <!-- End Google Tag Manager -->

    <script>
        window.addEventListener('load',function(){
            for (var i=0;i<ga.getAll().length;i++)
            {
                if(ga.getAll()[i].get('trackingId') == 'UA-147131520-1')
                {
                    var param =   ga.getAll()[i].get('linkerParam');
                }
            }
        })
    </script>

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
<!-- Google Tag Manager (noscript) -->

<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-
MBRDV5K" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <div id="app">
        <!-- <app></app> -->

        <div class="auth-content-section">
            <div class="row justify-content-center">
                <img src="/img/buildlabour-fulllogo.png" srcset="/img/buildlabour-fulllogo@2x.png 2x, /img/buildlabour-fulllogo@3x.png 3x">
            </div>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="form-group ta-center">
                        <br><br>
                        @if (! isset($_COOKIE['bl_token']))
                            <button type="submit" onclick="location.href='/login'" style="width:220px">
                                Login
                            </button>
                            <br><br>
                            <button type="submit" onclick="location.href='/register'" style="width:220px">
                                Find Work
                            </button>
                            <br><br>
                            <button type="submit" onclick="location.href='/company/register'" style="width:220px">
                                I Need Workers
                            </button>
                        @else
                            <button type="submit" onclick="location.href='/login'">
                                My Profile
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
