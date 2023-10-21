<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title><!--{{ config('app.name', 'Laravel') }}-->

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

        <!-- Scripts -->
        @vite(['resources/js/app.css', 'resources/js/app.js'])

<<<<<<< HEAD
                
        <!-- CSS da aplicação -->
        <script src="/js/scripts.js"></script>
=======
                   
>>>>>>> 6f0aee47df0578d8ac080ca648318a232f85ae81

    </head>
        <body>

            @include('layouts/sidebar')

            @yield('content')

            <!-- footerScript -->
            @yield('footerScript')

            <!-- App js-->
            
        </body>
        
</html>
