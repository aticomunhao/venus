<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Ícone do sistema e títulos --}}
    <link rel="icon" href="{{ URL::asset('/images/Venus2.png') }}" type="image/icon type">
    <title>@yield('title')</title>



    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])

    {{-- CDN Jquery --}}

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> 


    {{-- Import CSS Tooltips --}}
    <link href="{{ asset('css/tooltips.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery.js') }}"></script>




</head>

<body>

    @include('layouts/sidebar')
    @yield('content')

    <div class="modal fade" id="modalLogin" data-bs-keyboard="false" tabindex="-1" aria-labelledby="inativarLabel"
        aria-hidden="true" data-bs-backdrop="static">
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
                    <button type="button" id="confirm" class="btn btn-primary" data-bs-dismiss="modal">Estou
                        Aqui!</button>
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

    function toast() {
        $('.modal').modal('hide');
        $('#modalLogin').modal('show');
    }

    function checkSession() {
        $.ajax({
            type: "GET",
            url: "/usuario/sessao",
            dataType: "json",
            success: function(response) {

                session = response

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }


    function time() {
        checkSession()
        total -= 1
        $('#tempoLogout').html(total + 's')
        if (total < 1 && redirect == 0) {
            if (session == 0) {
                window.location.replace("/login/valida")
            } else {
                document.getElementById('logout-form').submit();
                redirect = 1
            }

        }
    }

    function setTime() {
        total = 30
        redirect = 0
        $('#tempoLogout').html(total + 's')
        var intervalIdTime = window.setInterval(function() {
            [time()]
        }, 1000);





        $('#confirm').click(function() {
            clearInterval(intervalIdTime)
        })

    }

    var intervalId = window.setInterval(function() {
        [toast(), setTime()]
    }, 600000);
</script>
<script>
    $("body").on("submit", "form", function() {
        $(this).submit(function() {
            return false;
        });
        $(':submit').html(
            '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Carregando...')
        return true;
    });
</script>
<script>
    $(document).ready(function() {



        let atendimentos = @json($lista ?? []);
        let motivo = @json($motivo ?? '');

        if (!motivo || motivo.length === 0) {
            // Tratar o caso em que motivo está vazio
            console.log("Motivo está vazio.");
        } else {
            // Caso contrário, continue com o processamento
            console.log("Motivo:", motivo);
        }
        var intervalPesq = 0


        $('#status').prop('selectedIndex', -1)

        function ajax() {

            let assist = $('#assist').val() == '' ? null : $('#assist').val()
            let cpf = $('#cpf').val() == '' ? null : $('#cpf').val()
            let status = $('#status').val() == '' ? null : $('#status').val()
            let dt_ini = $('#dt_ini').val() == '' ? null : $('#dt_ini').val()


            $.ajax({
                type: "GET",
                url: "/tabela-atendimentos/" + assist + "/" + cpf + "/" + status + "/" + dt_ini,
                dataType: "json",
                success: function(response) {

                    atendimentos = response
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        function colunas() {

            $('#numeroAtendimento').prop('checked') ? $('.numeroAtendimento').show() : $('.numeroAtendimento')
                .hide()
            $('#atendentePreferido').prop('checked') ? $('.atendentePreferido').show() : $(
                '.atendentePreferido').hide()
            $('#tipoAtendente').prop('checked') ? $('.tipoAtendente').show() : $('.tipoAtendente').hide()
            $('#horarioChegada').prop('checked') ? $('.horarioChegada').show() : $('.horarioChegada').hide()
            $('#prioridade').prop('checked') ? $('.prioridade').show() : $('.prioridade').hide()
            $('#atendimento').prop('checked') ? $('.atendimento').show() : $('.atendimento').hide()
            $('#representante').prop('checked') ? $('.representante').show() : $('.representante').hide()
            $('#atendente').prop('checked') ? $('.atendente').show() : $('.atendente').hide()
            $('#sala').prop('checked') ? $('.sala').show() : $('.sala').hide()
            $('#tipoAtendimento').prop('checked') ? $('.tipoAtendimento').show() : $('.tipoAtendimento').hide()
            $('#statusAtendimento').prop('checked') ? $('.statusAtendimento').show() : $('.statusAtendimento')
                .hide()

        }

        function tabelas() {
            if ($('.modal').hasClass('show')) {

            } else {
                $('#tabelaPrincipal').html("")
                $.each(atendimentos, function() {

                    const date = Date.parse(this.dh_chegada);
                    const formatter = new Intl.DateTimeFormat('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                    const formattedDate = formatter.format(date);

                    let ida = this.ida == null ? '' : this.ida
                    let nm_4 = this.nm_4 == null ? ' ' : this.nm_4
                    let tipo = this.tipo == null ? ' ' : this.tipo
                    let dh_chegada = formattedDate
                    let prdesc = this.prdesc == null ? '' : this.prdesc
                    let nm_1 = this.nm_1 == null ? '' : this.nm_1
                    let nm_2 = this.nm_2 == null ? '' : this.nm_2
                    let nm_3 = this.nm_3 == null ? '' : this.nm_3
                    let nr_sala = this.nr_sala == null ? '' : this.nr_sala
                    let afe = this.afe == null ? '' : this.afe
                    let descricao = this.descricao == null ? '' : this.descricao



                    if (this.status_atendimento == 3) {

                        $('#tabelaPrincipal').append(

                            '<tr class="table-danger">' +

                            //Colunas com informações
                            '<td class="numeroAtendimento">' + ida + '</td>' +
                            '<td class="atendentePreferido">' + nm_4 + '</td>' +
                            '<td class="tipoAtendente">' + tipo + '</td>' +
                            '<td class="horarioChegada">' + dh_chegada + '</td>' +
                            '<td class="prioridade">' + prdesc + '</td>' +
                            '<td class="atendimento">' + nm_1 + '</td>' +
                            '<td class="representante">' + nm_2 + '</td>' +
                            '<td class="atendente">' + nm_3 + '</td>' +
                            '<td class="sala">' + nr_sala + '</td>' +
                            '<td class="tipoAtendimento">' + afe + '</td>' +
                            '<td class="statusAtendimento" >' + descricao + '</td>' +
                            '<td class="">' +

                            //Botões de ação
                            '<a href="/editar-atendimento/' + ida + '" class="tooltips">' +
                            '<span class="tooltiptext">Editar</span>' +
                            '<button type="button" class="btn btn-outline-warning btn-sm">' +
                            '<i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>' +
                            '</button>' +
                            '</a>' +

                            '<a href="/visualizar-atendimentos/' + this.idas +
                            '"class="tooltips">' +
                            '<span class="tooltiptext">Visualizar</span>' +
                            '<button type="button" class="btn btn-outline-primary btn-sm">' +
                            '<i class="bi bi-search" style="font-size: 1rem; color:#000;">' +
                            '</i>' +
                            '</button>' +
                            '</a>' +

                            //botão modal cancelar
                            '<button type="button"class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modal' +
                            ida + '">' +
                            '<span class="tooltiptext">Cancelar</span>' +
                            '<i class="bi bi-x-circle"style="font-size: 1rem; color:#000;">' +
                            '</i>' +
                            '</button>' +

                            '<form action="/cancelar-atendimento/' + ida + '">' +
                            '<div class="modal fade" id="modal' + ida +
                            '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">' +
                            '<div class="modal-dialog">' +
                            '<div class="modal-content">' +
                            '<div class="modal-header" style="background-color:#DC4C64;color:white">' +
                            '<h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar Cancelamento</h1>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                            '</div>' +
                            '<div class="modal-body">' +

                            '<label for="recipient-name" class="col-form-label" style="font-size:17px">' +
                            'Tem certeza que deseja inativar:' +
                            '<br />' +
                            '<span style="color:#DC4C64; font-weight: bold;">' + nm_1 + '</span>' +
                            '&#63;' +
                            '</label>' +
                            '<br />' +

                            '<center>' +
                            '<div class="mb-2 col-10">' +
                            '<label class="col-form-label">Insira o motivo da ' +
                            '<span style="color:#DC4C64">inativação:</span></label>' +
                            '<select class="form-select" name="motivo" required>' +
                            '<option value="' + motivo[0].id + '">' + motivo[0].descricao +
                            '</option>' +
                            '<option value="' + motivo[1].id + '">' + motivo[1].descricao +
                            '</option>' +
                            '</select>' +
                            '</div>' +
                            '</center>' +
                            '</div>' +
                            '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>' +
                            '<button type="submit" class="btn btn-primary">Confirmar</button>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</form>' +
                            '</td>' +
                            '</tr>'
                        )

                    } else {
                        $('#tabelaPrincipal').append(

                            '<tr>' +
                            '<td class="numeroAtendimento"> ' + ida + '</td>' +
                            '<td class="atendentePreferido">' + nm_4 + '</td>' +
                            '<td class="tipoAtendente">' + tipo + '</td>' +
                            '<td class="horarioChegada">' + dh_chegada + '</td>' +
                            '<td class="prioridade">' + prdesc + '</td>' +
                            '<td class="atendimento">' + nm_1 + '</td>' +
                            '<td class="representante">' + nm_2 + '</td>' +
                            '<td class="atendente">' + nm_3 + '</td>' +
                            '<td class="sala">' + nr_sala + '</td>' +
                            '<td class="tipoAtendimento">' + afe + '</td>' +
                            '<td class="statusAtendimento">' + descricao + '</td>' +
                            '<td class="">' +

                            '<a href="/editar-atendimento/' + ida + '" class="tooltips">' +
                            '<span class="tooltiptext">Editar</span>' +
                            '<button type="button" class="btn btn-outline-warning btn-sm" >' +
                            '<i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>' +
                            '</button>' +
                            '</a>' +

                            '<a href="/visualizar-atendimentos/' + this.idas +
                            '" class="tooltips">' +
                            '<span class="tooltiptext">Visualizar</span>' +
                            '<button type="button" class="btn btn-outline-primary btn-sm">' +
                            '<i class="bi bi-search" style="font-size: 1rem; color:#000;">' +
                            '</i>' +
                            '</button>' +
                            '</a>' +


                            '<button type="button"class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modal' +
                            ida + '">' +
                            '<span class="tooltiptext">Cancelar</span>' +
                            '<i class="bi bi-x-circle"style="font-size: 1rem; color:#000;">' +
                            '</i>' +
                            '</button>' +

                            '<form action="/cancelar-atendimento/' + ida + '">' +
                            '<div class="modal fade" id="modal' + ida +
                            '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">' +
                            '<div class="modal-dialog">' +
                            '<div class="modal-content">' +
                            '<div class="modal-header" style="background-color:#DC4C64;color:white">' +
                            '<h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar Cancelamento</h1>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                            '</div>' +
                            '<div class="modal-body">' +

                            '<label for="recipient-name" class="col-form-label" style="font-size:17px">' +
                            'Tem certeza que deseja inativar:' +
                            '<br />' +
                            '<span style="color:#DC4C64; font-weight: bold;">' + nm_1 + '</span>' +
                            '&#63;' +
                            '</label>' +
                            '<br />' +

                            '<center>' +
                            '<div class="mb-2 col-10">' +
                            '<label class="col-form-label">Insira o motivo da ' +
                            '<span style="color:#DC4C64">inativação:</span></label>' +
                            '<select class="form-select" name="motivo" required>' +
                            '<option value="' + motivo[0].id + '">' + motivo[0].descricao +
                            '</option>' +
                            '<option value="' + motivo[1].id + '">' + motivo[1].descricao +
                            '</option>' +
                            '</select>' +
                            '</div>' +
                            '</center>' +
                            '</div>' +
                            '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>' +
                            '<button type="submit" class="btn btn-primary">Confirmar</button>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</form>' +
                            '</td>' +
                            '</tr>'
                        )
                    }


                })
            }
        }

        function stopPesquisa() {
            clearInterval(intervalPesq)
        }

        tabelas()
        colunas()

        $('.coluna').click(function() {
            colunas();
        })
        $('#limpar').click(function() {
            $('#assist').val("")
            $('#cpf').val("")
            $('#status').prop('selectedIndex', -1)
            $('#dt_ini').val("{{ $data_inicio ?? now()->toDateString() }}")

        })

        $('.pesq').click(function() {
            ajax()
            intervalPesq = window.setInterval(function() {
                [tabelas(), colunas()]
            }, 500);


        })



        var intervalId = window.setInterval(function() {
            [ajax(), tabelas(), colunas(), stopPesquisa()]
        }, 10000);


    })
</script>

<script>
    let hoje = @json($now ?? []);
    let assistido = @json($assistido ?? []);
    let situacao = @json($situacao ?? []);

    if (assistido != null || situacao != null) {
        $('#dt_ini').val("")
    }
    $('.pesquisa').change(function() {
        let assis = $('#assist').val()
        let status = $('#status').prop('selectedIndex')

        if (assis == '' && status == 0) {
            $('#dt_ini').val(hoje)

        } else {
            $('#dt_ini').val("")

        }

    })
</script>

</html>
