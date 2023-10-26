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
=======
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/gerenciar-atendimentos">Recepção Atendimento Fraterno</a></li>
                <li><a class="dropdown-item" href="#">Recepção Geral</a></li>
                <li><a class="dropdown-item" href="gerenciar-atendentes">Gerenciar Atendentes</a></li>
                <li><a class="dropdown-item" href="/atendendo">Atender o assistido</a></li>
                <li><a class="dropdown-item" href="/fato">Atender o assistido</a></li>

            </ul>
        </div>


        <!-- CSS da aplicação -->
        <script src="/js/scripts.js"></script>
>>>>>>> 5031948abba0a38b57ffd7d19ca902a2ee3dcefb

    </head>
        <body>

            @include('layouts/sidebar')

            @yield('content')

            <!-- footerScript -->
            @yield('footerScript')

            <!-- App js-->
            
        </body>

</html>
