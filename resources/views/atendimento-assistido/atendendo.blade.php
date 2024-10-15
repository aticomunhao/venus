@extends('layouts.app')

@section('title')
    Atendimento Fraterno Individual
@endsection

@section('content')
    <div class="container-xxl" ;>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            ATENDIMENTO FRATERNO INDIVIDUAL</h4>
        <div class="col-12">
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">Data
                            <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                value="{{ date('d/m/Y', strtotime($now)) }}" type="text" name="data" id=""
                                disabled>
                        </div>
                        <div class="col-3">Grupo
                            <input class="form-control"
                                style="text-align:left; font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                value="{{ $grupo }}" name="nome" id="" type="text" disabled>
                        </div>

                        <div class="col-2">Cod AFI
                            <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;"
                                type="text" name="id_atendene" id="" value="{{ $atendente }}" disabled>
                        </div>

                        <div class="col-3">Nome do Atendente
                            <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                value="{{ $nome }}" name="nome_usuario" id="" type="text" disabled>
                        </div>
                        <div class="col-2">Fila de espera
                            <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                value="" name="nome_usuario" type="text" disabled id="id_pessoas_para_atender">
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row" style="text-align:right;">
                <div class="col-6">
                    <a href="/meus-atendimentos"><input class="btn btn-light btn-sm me-md-2"
                            style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                            value="Meus atendimentos"></a>
                </div>
                <div class="col-6">
                    <a href="/atender"><input class="btn btn-success btn-sm me-md-2" type="button"
                            value="Atender agora"></a>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="table">
                            <table
                                class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                                <thead style="text-align: center;">
                                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                        <th class="col">NR</th>
                                        <th class="col">HORÁRIO CHEGADA</th>
                                        <th class="col">ATENDIDO</th>
                                        <th class="col">REPRESENTANTE</th>
                                        <th class="col">STATUS</th>
                                        <th class="col">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px; color:#000000; text-align:center;">
                                    @foreach ($assistido as $assistidos)
                                        <tr>
                                            <td scope="">{{ $assistidos->idat }}</td>
                                            <td scope="">{{ date('d/m/Y G:i:s', strtotime($assistidos->dh_chegada)) }}
                                            </td>
                                            <td scope="">{{ $assistidos->nm_1 }}</td>
                                            <td scope="">{{ $assistidos->nm_2 }}</td>
                                            <td scope="">{{ $assistidos->descricao }}</td>
                                            <td scope="">
                                                <a href="/historico/{{ $assistidos->idat }}/{{ $assistidos->idas }}"><button
                                                        type="button" class="btn btn-outline-primary btn-sm tooltips"><span
                                                            class="tooltiptext">Analisar</span><i class="bi bi-search"
                                                            style="font-size: 1rem; color:#000;"></i></button></a>
                                                <a href="/fim-analise/{{ $assistidos->idat }}"><button type="button"
                                                        class="btn btn-outline-warning btn-sm tooltips"><span
                                                            class="tooltiptext"
                                                            style="width:150px; margin-left:-75px">Chamar assistido</span><i
                                                            class="bi bi-bell"
                                                            style="font-size: 1rem; color:#000;"></i></button></a>
                                                <a href="/iniciar-atendimento/{{ $assistidos->idat }}"><button
                                                        type="button" class="btn btn-outline-success btn-sm tooltips"><span
                                                            class="tooltiptext">Iniciar</span><i class="bi bi-check-circle"
                                                            style="font-size: 1rem; color:#000;"></i></button></a>
                                                <a href="/tratar/{{ $assistidos->idat }}/{{ $assistidos->idas }}"><button
                                                        type="button" class="btn btn-outline-warning btn-sm tooltips"><span
                                                            class="tooltiptext">Tratamento</span><i class="bi bi-bandaid"
                                                            style="font-size: 1rem; color:#000;"></i></button></a>
                                                <a href="/entrevistar/{{ $assistidos->idat }}/{{ $assistidos->idas }}"><button
                                                        type="button"
                                                        class="btn btn-outline-warning btn-sm tooltips"><span
                                                            class="tooltiptext">Entrevista</span><i class="bi bi-mic"
                                                            style="font-size: 1rem; color:#000;"></i></button></a>
                                                <a href="/temas/{{ $assistidos->idat }}"><button type="button"
                                                        class="btn btn-outline-warning btn-sm tooltips"><span
                                                            class="tooltiptext">Temática</span><i
                                                            class="bi bi-journal-bookmark-fill"
                                                            style="font-size: 1rem; color:#000;"></i></button></a>
                                                <button type="button" class="btn btn-outline-danger btn-sm tooltips"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalRel{{ $assistidos->idat }}"><span
                                                        class="tooltiptext">Limpar</span><i class="bi bi-arrow-repeat"
                                                        style="font-size: 1rem; color:#000;"></i></button>
                                                <button type="button" class="btn btn-outline-danger btn-sm tooltips"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalF{{ $assistidos->idat }}"><span
                                                        class="tooltiptext">Finalizar</span><i class="bi bi-door-open"
                                                        style="font-size: 1rem; color:#000;"></i></button>


                                                {{-- Modal de Reset --}}
                                                <div class="modal fade" id="modalRel{{ $assistidos->idat }}"
                                                    tabindex="-1" aria-labelledby="exampleModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                style="background-color:rgb(196, 27, 27);">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style=" color:white">Reiniciar</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Tem certeza que deseja resetar? <br /><span
                                                                    style="color:rgb(196, 27, 27);">Todo o progresso feito
                                                                    até aqui será apagado!</span>&#63;

                                                            </div>
                                                            <div class="modal-footer mt-2">
                                                                <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">Cancelar</button>
                                                                <a type="button" class="btn btn-primary"
                                                                    href="/reset/{{ $assistidos->idat }}">Confirmar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Modal de Reset Fim --}}


                                                {{-- Modal de Finalizar --}}
                                                <form action="/finalizar/{{ $assistidos->idat }}" method="POST"
                                                    id="final">
                                                    @csrf
                                                    <div class="modal fade" id="modalF{{ $assistidos->idat }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">

                                                            <div class="modal-content">
                                                                <div class="modal-header"
                                                                    style="background-color:rgb(196, 27, 27);">
                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                        style=" color:white">Finalizar Atendimento</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            Tem certeza que deseja finalizar o atendimento
                                                                            de:
                                                                            <br /><span
                                                                            
                                                                                style="color:rgb(196, 27, 27);">{{ $assistidos->nm_1 }}</span>&#63;


                                                                        </div>
                                                                        
                                                                        <center>
                                                                            <div class="col-9 mt-5">
                                                                                <span
                                                                                
                                                                                    style="color:rgb(196, 27, 27);">*</span>
                                                                                Não esqueça de conferir se os seus encaminhamentos foram registrados corretamentes,
                                                                                Utilizando para isso a ação de visualizar.
                                                                            </div>
                                                                        </center>
                                                                        <div class="col-10">
                                                                    


                                                                        </div>
                                                                        
                                                                        <div class="col-12 mt-3">

                                                                            <input class="form-check-input"
                                                                                type="checkbox" name="emergencia"
                                                                                id="emergencia"
                                                                                style="width:15px; height:15px; border:1px solid rgb(0, 102, 255)">
                                                                            <label class="emergencia" name="hello"
                                                                                id="hello">Este atendimento é uma <span
                                                                                    style="color:rgb(196, 27, 27);">Emergência</span></label>
                                                                            
                                                                        </div>


                                                                        </center>
                                                                    </div>
                                                                </div>

                                                                <div class="modal-footer mt-2">
                                                                    <button type="button" class="btn btn-danger"
                                                                        data-bs-dismiss="modal">Cancelar</button>

                                                                    <button type="submit" id="finalizar"
                                                                        class="btn btn-primary">Confirmar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                {{-- Modal de Finalizar Fim --}}


                                                {{-- Inicio modal de confirmação de encaminhamento --}}
                                                <div class="modal" tabindex="-1" id="confirmacaoEncaminhamento">
                                                    <div class="modal-dialog">

                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                style="background-color:rgb(255, 147, 7)">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style=" color:white">Atenção!</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">


                                                                <div class="row">
                                                                    <div class="col-12">
                                                                      
                                                                        <br /><span
                                                                            style="color:rgb(255, 147, 7); font-weight: bold">Tem
                                                                            certeza que deseja continuar&#63;</span>


                                                                    </div>
                                                                    <center>
                                                                        <div class="col-9 mt-5">
                                                                            <span
                                                                            
                                                                                style="color:rgb(255, 147, 7); font-weight: bold">*</span>
                                                                            Não esqueça de conferir se os seus encaminhamentos foram registrados corretamentes,
                                                                            Utilizando para isso a ação de visualizar.
                                                                        </div>
                                                                    </center>


                                                                    </center>
                                                                </div>


                                                            </div>

                                                            <div class="modal-footer mt-2">
                                                                <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">Cancelar</button>

                                                                <button class="btn btn-primary confirmar">Confirmar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- FIM modal de confirmação de encaminhamento --}}

                                                {{-- Inicio modal de confirmação de temática --}}
                                                <div class="modal" tabindex="-1" id="confirmacaoTematica">
                                                    <div class="modal-dialog">

                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                style="background-color:rgb(255, 147, 7)">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style=" color:white">Atenção!</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">


                                                                <div class="row">
                                                                    <div class="col-12">
                                                                       
                                                                        <br /><span
                                                                            style="color:rgb(255, 147, 7); font-weight: bold">Tem
                                                                            certeza que deseja continuar&#63;</span>


                                                                    </div>
                                                                    <center>
                                                                        <div class="col-9 mt-5">
                                                                            <span
                                                                            
                                                                                style="color:rgb(255, 147, 7); font-weight: bold">*</span>
                                                                            Não esqueça de conferir se suas temáticas foram registrados corretamentes,
                                                                            Utilizando para isso a ação de visualizar.
                                                                        </div>
                                                                    </center>



                                                                    </center>
                                                                </div>


                                                            </div>

                                                            <div class="modal-footer mt-2">
                                                                <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">Cancelar</button>

                                                                <button class="btn btn-primary confirmar">Confirmar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- FIM modal de confirmação de temática --}}




                                                <!--<button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#tratamento{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Tratamentos"><i class="bi bi bi-bandaid" style="font-size: 1rem; color:#000;"></i></button>
                                                                                                        <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#entrevista{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Entrevistas"><i class="bi bi bi-mic" style="font-size: 1rem; color:#000;"></i></button>
                                                                                                        <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#anotacoes{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Entrevistas"><i class="bi bi-journal-bookmark-fill" style="font-size: 1rem; color:#000;"></i></button>
                                                                                                        <button class="btn btn-outline-danger btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#finalizar{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Finalizar"><i class="bi bi-door-open" style="font-size: 1rem; color:#000;"></i></button>-->
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <style>
        .emergencia {
            opacity: 50%;
        }
    </style>

    <script>
        $(document).ready(function() {

            let numeroAcoes

            function finalAjax() {
                $.ajax({
                    type: "GET",
                    url: "/encaminhamentos-tematicas/" + @JSON($assistido)[0]['idat'],
                    dataType: "JSON",
                    success: function(response) {
                        numeroAcoes = response

                    },
                });
            }

            finalAjax()
            $('#finalizar').click(function(e) {
                e.preventDefault()
                finalAjax()
                console.log(numeroAcoes)
                $('.modal').modal('hide');

                if (numeroAcoes['encaminhamentos'] == 0) {
                    $('#confirmacaoEncaminhamento').modal('show');
                } else if (numeroAcoes['tematicas'] == 0) {
                    $('#confirmacaoTematica').modal('show');
                } else {
                    $('#final').submit()
                }
            })

            $('.confirmar').click(function(e) {
                e.preventDefault()
                $('#final').submit()

            })
        });
    </script>
    <script>
        $('#emergencia').change(function() {
            $('#hello').toggleClass('emergencia')
        })
    </script>
    <script>
        $(document).ready(function() {
            function filaAjax() {
                $.ajax({
                    type: "GET",
                    url: "/pessoas-para-atender",
                    dataType: "JSON",
                    success: function(response) {

                        $('#id_pessoas_para_atender').val(response);
                    },
                    error: function(error) {
                        console.error('Erro ao buscar dados:', error);
                    }
                });
            }

            // Chama a função imediatamente e a cada 5 segundos
            filaAjax(); // Chamada inicial
            setInterval(filaAjax, 5000); // Chamada a cada 5 segundos
        });
    </script>
@endsection
