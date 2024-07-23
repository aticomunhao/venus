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

    <link rel="icon" href="{{ URL::asset('/images/Venus2.png')}}" type="image/icon type">
    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- Link ke jQuery dari CDN -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Link ke DataTables dari CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Import CSS --}}
    <link href="{{ asset('css/tooltips.css') }}" rel="stylesheet">





</head>

<body>

        @include('layouts/sidebar')
        @yield('content')

        <div class="modal fade" id="modalLogin" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#DC4C64;color:white">
                        <h1 class="modal-title fs-5" id="inativarLabel">Alerta de inatividade</h1>
                    </div>
                    <br />
                <div class="modal-body">
                    <center>
                        <span>Atenção, clique em ESTOU AQUI! para não ser deslogado</span><br />
                        <span id="tempoLogout"style="color:#DC4C64; font-weight: bold;"></span>
                    </center>
                </div>
                <div class="modal-footer">
                  <button type="button"  id="confirm" class="btn btn-primary" data-bs-dismiss="modal" >Estou Aqui!</button>
                </div>
              </div>
            </div>
          </div>

        <!-- footerScript -->
        @yield('footerScript')


    <!-- App js-->

</body>
<script>
let session
let redirect
    function toast(){
        $('.modal').modal('hide');
        $('#modalLogin').modal('show');
    }

    function checkSession(){
        $.ajax({
            type: "GET",
             url: "/usuario/sessao",
             dataType: "json",
             success: function(response) {

                session=response

             },
             error: function(xhr) {
                 console.log(xhr.responseText);
             }
         });
    }


    function time(){
        checkSession()
        total -= 1
        $('#tempoLogout').html(total + 's')
        if(total < 1 && redirect == 0){
                if(session == 0){
                    window. location. replace("/login/valida")
                }else{
                        document.getElementById('logout-form').submit();
                        redirect = 1
                }

        }
    }

    function setTime(){
        total = 30
        redirect = 0
        $('#tempoLogout').html(total + 's')
        var intervalIdTime = window.setInterval(function(){
            [time()]
        }, 1000);





       $('#confirm').click(function(){
        clearInterval(intervalIdTime)
       })

    }

    var intervalId = window.setInterval(function(){
        [toast(), setTime()]
      }, 600000);



</script>
<script>
    $("body").on("submit", "form", function() {
        $(this).submit(function() {
            return false;
        });
        $(':submit').html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Carregando...')
        return true;
    });
</script>
</html>
