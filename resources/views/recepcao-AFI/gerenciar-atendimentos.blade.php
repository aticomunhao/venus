@extends('layouts.app')

@section('title')
    Gerenciar Atendimentos
@endsection

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <?php
    //echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
    ?>


    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR
            ATENDIMENTOS</h4>

        <div class="row mt-3">





            <div class="col-2">
                <a href="/criar-atendimento" class="btn btn-success btn-sm w-100"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">Criar Novo</a>
            </div>

            <div class="col-2">
                <a href="/gerenciar-pessoas" class="btn btn-warning btn-sm w-100"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">Nova Pessoa</a>
            </div>



            <div class="col-1">
                <a href="/gerenciar-atendente-dia" class="btn btn-warning btn-sm w-100"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">Escala AFI</a>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#filtros"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">
                    Filtrar <i class="bi bi-funnel"></i>
                </button>
            </div>

            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">
                    Colunas <i class="bi bi-gear"></i>
                </button>
            </div>





            {{-- Filtro Modal --}}
            <div class="modal fade" id="filtros" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:grey;color:white">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Filtrar Opções</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    </div>
                    <div class="modal-body">


                                <center>
                                    <div class="col-10">



                                        <div class="col">
                                            <label for="assist">Atendido</label>
                                            <input class="form-control pesquisa" type="text" id="assist"
                                                name="assist" value="{{ $assistido }}">
                                        </div>

                                        <div class="col mt-3">
                                            <label for="assist">CPF</label>
                                            <input class="form-control" type="text" maxlength="11"
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                id="cpf" name="cpf" value="{{ $cpf }}">

                                        </div>

                                        <div class="col mt-3 ">
                                            <label for="status">Status</label>
                                            <select class="form-select pesquisa" id="status" name="status"
                                                type="number">
                                                <option value=""></option>
                                                @foreach ($st_atend as $statusz)
                                                    <option {{ $situacao == $statusz->id ? 'selected' : '' }}
                                                        value="{{ $statusz->id }}">{{ $statusz->descricao }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="col  mt-3  mb-3">
                                            <label for="dt_ini">Data início</label>
                                            <input class="form-control" type="date" id="dt_ini" name="dt_ini"
                                                value="{{ $data_inicio ?? now()->toDateString() }}">
                                        </div>

                                    </div>
                                </center>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                <button id="limpar" type="button" class="btn btn-secondary pesq" data-bs-dismiss="modal">Limpar</button>
                                <button class="btn btn-primary pesq" id="confirmar" data-bs-dismiss="modal">Confirmar</button>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- Fim filtro Modal --}}

            {{-- Modal Colunas --}}
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:grey;color:white">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Colunas Visualizadas</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">



                            <div class="col-10 mx-auto d-block">

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="numeroAtendimento">
                                    <label class="form-check-label" for="flexCheckDefault"> Número do Atendimento </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="atendentePreferido">
                                    <label class="form-check-label" for="flexCheckDefault"> Atendente Preferido </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="tipoAtendente">
                                    <label class="form-check-label" for="flexCheckDefault"> Tipo do Atendente </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="horarioChegada">
                                    <label class="form-check-label" for="flexCheckDefault"> Horário de Chegada </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="prioridade">
                                    <label class="form-check-label" for="flexCheckDefault"> Prioridade </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="atendimento" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Atendido </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="representante" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Representante </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="atendente" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Atendente </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="sala" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Sala </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="tipoAtendimento">
                                    <label class="form-check-label" for="flexCheckDefault"> Tipo do Atendimento </label>
                                </div>

                                <div class="col mb-3">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="statusAtendimento" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Status </label>
                                </div>

                            </div>



                        </div>

                    </div>
                </div>
            </div>
{{-- Fim modal Colunas --}}






        </div>

        <hr>
        <div class="row">
            <div class="table">Total Atendidos: {{ $contar }}
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle" >
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col numeroAtendimento">Nr</th>
                            <th class="col atendentePreferido">AFI PREF</th>
                            <th class="col tipoAtendente">TIPO AFI</th>
                            <th class="col horarioChegada">HORÁRIO CHEGADA</th>
                            <th class="col prioridade">PRIOR</th>
                            <th class="col atendimento">ATENDIDO</th>
                            <th class="col representante">REPRESENTANTE</th>
                            <th class="col atendente">ATENDENTE</th>
                            <th class="col sala">SALA</th>
                            <th class="col tipoAtendimento">TIPO</th>
                            <th class="col statusAtendimento">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;" id="tabelaPrincipal">



                    </tbody>
                </table>
            </div class="d-flex justify-content-center">

        </div>
    </div>
    </div>


    <script>
        $(document).ready(function() {
            let atendimentos = @json($lista);
            var intervalPesq = 0


            function ajax() {

                let assist = $('#assist').val() == '' ? null : $('#assist').val()
                let cpf = $('#cpf').val() == '' ? null : $('#cpf').val()
                let status = $('#status').val() == '' ? null : $('#status').val()
                let dt_ini = $('#dt_ini').val() == '' ? null : $('#dt_ini').val()


               $.ajax({
                   type: "GET",
                    url: "/tabela-atendimentos/"+assist+"/"+cpf+"/"+status+"/"+dt_ini,
                    dataType: "json",
                    success: function(response) {

                        atendimentos = response
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            $('#teste').click(function(){
                ajax();
            })

            function colunas() {

                    $('#numeroAtendimento').prop('checked') ? $('.numeroAtendimento').show() : $('.numeroAtendimento').hide()
                    $('#atendentePreferido').prop('checked') ? $('.atendentePreferido').show() : $('.atendentePreferido').hide()
                    $('#tipoAtendente').prop('checked') ? $('.tipoAtendente').show() : $('.tipoAtendente').hide()
                    $('#horarioChegada').prop('checked') ? $('.horarioChegada').show() : $('.horarioChegada').hide()
                    $('#prioridade').prop('checked') ? $('.prioridade').show() : $('.prioridade').hide()
                    $('#atendimento').prop('checked') ? $('.atendimento').show() : $('.atendimento').hide()
                    $('#representante').prop('checked') ? $('.representante').show() : $('.representante').hide()
                    $('#atendente').prop('checked') ? $('.atendente').show() : $('.atendente').hide()
                    $('#sala').prop('checked') ? $('.sala').show() : $('.sala').hide()
                    $('#tipoAtendimento').prop('checked') ? $('.tipoAtendimento').show() : $('.tipoAtendimento').hide()
                    $('#statusAtendimento').prop('checked') ? $('.statusAtendimento').show() : $('.statusAtendimento').hide()

            }

            function tabelas() {
                $('#tabelaPrincipal').html("")
                $.each(atendimentos, function(){

                   const date = Date.parse(this.dh_chegada);
                   const formatter = new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric',hour: '2-digit', minute: '2-digit', second: '2-digit' });
                   const formattedDate = formatter.format(date);

                       let ida = this.ida  == null ? '' : this.ida
                       let nm_4 = this.nm_4 == null ? ' ' : this.nm_4
                       let tipo = this.tipo  == null ? ' ' : this.tipo
                       let dh_chegada = formattedDate
                       let prdesc = this.prdesc  == null ? '' : this.prdesc
                       let nm_1 =  this.nm_1  == null ? '' : this.nm_1
                       let nm_2 = this.nm_2  == null ? '' : this.nm_2
                       let nm_3 = this.nm_3  == null ? '' : this.nm_3
                       let nr_sala = this.nr_sala  == null ? '' : this.nr_sala
                       let afe = this.afe  == null ? '' : this.afe
                       let descricao = this.descricao  == null ? '' : this.descricao



                    if(this.status_atendimento == 3){

                        $('#tabelaPrincipal').append(

                        '<tr class="table-danger">'+


                                   '<td class="numeroAtendimento"> '+ ida  +'</td>' +
                                   '<td class="atendentePreferido">'+nm_4  +'</td>' +
                                   '<td class="tipoAtendente">'+tipo+'</td>' +
                                   '<td class="horarioChegada">'+dh_chegada+'</td>' +
                                  '<td class="prioridade">'+prdesc +'</td>' +
                                   '<td class="atendimento">'+nm_1 +'</td>' +
                                   '<td class="representante">'+nm_2 +'</td>' +
                                   '<td class="atendente">'+nm_3 +'</td>'+
                                   '<td class="sala">'+nr_sala +'</td>'+
                               '<td class="tipoAtendimento">'+afe+'</td>'+
                                  '<td class="statusAtendimento" >'+descricao+'</td>'+
                                   '<td class="">'+

                                       '<a href="/editar-atendimento/' + ida + '" class="tooltips">' +
                                        '<span class="tooltiptext">Editar</span>'+
                                           '<button type="button" class="btn btn-outline-warning btn-sm">' +
                                               '<i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>' +
                                           '</button>' +
                                       '</a>'+

                                       '<a href="/visualizar-atendimentos/' + this.idas + '"class="tooltips">' +
                                        '<span class="tooltiptext">Visualizar</span>'+
                                           '<button type="button" class="btn btn-outline-primary btn-sm">' +
                                               '<i class="bi bi-search" style="font-size: 1rem; color:#000;">' +
                                               '</i>' +
                                           '</button>' +
                                       '</a>' +

                                       '<a href="/cancelar-atendimento/' + ida + '"class="tooltips">' +
                                        '<span class="tooltiptext">Cancelar</span>'+
                                           '<button type="button"class="btn btn-outline-danger btn-sm">' +
                                               '<i class="bi bi-x-circle"style="font-size: 1rem; color:#000;">' +
                                               '</i>' +
                                           '</button>' +
                                       '</a>' +


                                   '</td>'+



                       '</tr>'
                   )

                    }else{
                        $('#tabelaPrincipal').append(

                        '<tr>'+


                                   '<td class="numeroAtendimento"> '+ ida  +'</td>' +
                                   '<td class="atendentePreferido">'+nm_4  +'</td>' +
                                   '<td class="tipoAtendente">'+tipo+'</td>' +
                                   '<td class="horarioChegada">'+dh_chegada+'</td>' +
                                  '<td class="prioridade">'+prdesc +'</td>' +
                                   '<td class="atendimento">'+nm_1 +'</td>' +
                                   '<td class="representante">'+nm_2 +'</td>' +
                                   '<td class="atendente">'+nm_3 +'</td>'+
                                   '<td class="sala">'+nr_sala +'</td>'+
                               '<td class="tipoAtendimento">'+afe+'</td>'+
                                  '<td class="statusAtendimento">'+descricao+'</td>'+
                                   '<td class="">'+

                                       '<a href="/editar-atendimento/' + ida + '" class="tooltips">' +
                                        '<span class="tooltiptext">Editar</span>'+
                                           '<button type="button" class="btn btn-outline-warning btn-sm" >' +
                                               '<i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>' +
                                           '</button>' +
                                       '</a>'+

                                       '<a href="/visualizar-atendimentos/' + this.idas + '" class="tooltips">' +
                                        '<span class="tooltiptext">Visualizar</span>'+
                                           '<button type="button" class="btn btn-outline-primary btn-sm">' +
                                               '<i class="bi bi-search" style="font-size: 1rem; color:#000;">' +
                                               '</i>' +
                                           '</button>' +
                                       '</a>' +

                                       '<a href="/cancelar-atendimento/' + ida + '" class="tooltips">' +
                                        '<span class="tooltiptext">Cancelar</span>'+
                                           '<button type="button"class="btn btn-outline-danger btn-sm" data-tt="tooltip">' +

                                               '<i class="bi bi-x-circle"style="font-size: 1rem; color:#000;">' +
                                               '</i>' +
                                           '</button>' +
                                       '</a>' +


                                   '</td>'+



                       '</tr>'
                   )

                    }


            })}
            function stopPesquisa(){
                clearInterval(intervalPesq)
            }

            tabelas()
            colunas()



            $('.coluna').click(function(){
                colunas();
            })
            $('#limpar').click(function(){
                $('#assist').val("")
                $('#cpf').val("")
                $('#status').prop('selectedIndex', -1)
                $('#dt_ini').val("{{ $data_inicio ?? now()->toDateString() }}")

            })

            $('.pesq').click(function(){
                        ajax()
                        intervalPesq = window.setInterval(function(){[tabelas(), colunas()]}, 500);


            })

           var intervalId = window.setInterval(function(){
              [ajax(), tabelas(), colunas(), stopPesquisa()]
            }, 10000);


        })
    </script>

    <script>
        let hoje = @json($now);
        let assistido = @json($assistido);
        let situacao = @json($situacao);

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


@endsection
