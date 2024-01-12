<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://kit.fontawesome.com/a944918be8.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        function Atualizar() {
            window.location.reload();
        }
    </script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])


    <!-- Estilo para o dropdown-->
    <style>
        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>

</head>

<body>

    @include('layouts/sidebar')

    @yield('content')

    <!-- footerScript -->
    @yield('footerScript')

    <!-- App js-->

</body>

</html>