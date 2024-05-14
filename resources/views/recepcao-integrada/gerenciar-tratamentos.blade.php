@extends('layouts.app')

@section('title')
    Gerenciar Tratamentos
@endsection

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

    <?php
    //echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
    ?>

    <div class="container";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR TRATAMENTOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('gtcdex') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">

                            <div class ="col-2">Data início
                                <input class="form-control" type="date" id="" name="dt_enc"
                                    value="{{ $data_enc }}">
                            </div>

                            <div class="col-1">
                                Dia
                                <select class="form-select teste" id="" name="dia" type="number">
                                    @foreach ($dia as $dias)
                                        <option value="{{ $dias->id }}" {{ $diaP == $dias->id ? 'selected' : '' }}>
                                            {{ $dias->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">Assistido
                                <input class="form-control" type="text" id="3" name="assist"
                                    value="{{ $assistido }}">
                            </div>

                            <div class="col-1">Status
                                <select class="form-select teste1" id="4" name="status" type="number">

                                    @foreach ($stat as $status)
                                        <option value="{{ $status->id }}"
                                            {{ $situacao == $status->id ? 'selected' : '' }}> {{ $status->nome }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <br />
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">

                                <a href="/gerenciar-tratamentos"><input class="btn btn-light btn-sm me-md-2"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                        value="Limpar"></a>

                                <a href="/job"><input class="btn btn-light btn-sm me-md-2"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                        value="Job"></a>

                                <a href="/incluir-avulso"><input class="btn btn-success btn-sm me-md-2" type="button"
                                        value="Incluir Avulso"></a>

                    </form>

                </div>
            </div>




            <br />



        </div style="text-align:right;">
        <hr />
        <div class="table">Total assistidos: {{ $contar }}
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">Nr</th>
                        <th class="col">PRIORIDADE</th>
                        <th class="col">ASSISTIDO</th>
                        <th class="col">REPRESENTANTE</th>
                        <th class="col">DIA</th>
                        <th class="col">HORÁRIO</th>
                        <th class="col">TRATAMENTO</th>
                        <th class="col">GRUPO</th>
                        <th class="col">STATUS</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align: center;">
                    <tr>
                        @foreach ($lista as $listas)
                            <td>{{ $listas->idtr }}</td>
                            <td>{{ $listas->prdesc }}</td>
                            <td>{{ $listas->nm_1 }}</td>
                            <td>{{ $listas->nm_2 }}</td>
                            <td>{{ $listas->nomed }}</td>
                            <td>{{ date('H:i', strtotime($listas->h_inicio)) }}</td>
                            <td>{{ $listas->sigla }}</td>
                            <td>{{ $listas->nomeg }}</td>
                            <td>{{ $listas->tst }}</td>
                            <td>




                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                                    data-tt="tooltip" data-placement="top" title="Presença"
                                    data-bs-target="#presenca{{ $listas->idtr }}"><i class="bi bi bi-exclamation-triangle"
                                        style="font-size: 1rem; color:#000;"></i></button>


                                <div class="modal fade closes" id="presenca{{ $listas->idtr }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <form method="post" action="/presenca-tratatamento/{{ $listas->idtr }}">
                                        @csrf
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:orange;color:white">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Presença
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="recipient-name" class="col-form-label"
                                                            style="font-size:17px">Tem certeza que deseja registrar
                                                            presença para<br /><span
                                                                style="color:orange">{{ $listas->nm_1 }}</span>&#63;</label>
                                                    </div>
                                                    <center>
                                                        <div class="mb-2 col-10">
                                                            <label class="col-form-label">Insira o número de acompanhantes,
                                                                <span style="color:orange">se necessário:</span></label>
                                                            <input type="number" class="form-control"
                                                                name="acompanhantes" placeholder="0" min="0">
                                                        </div>
                                                    </center>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                        data-bs-dismiss="modal">Cancelar</button>

                                                    @if ($listas->dt_fim == $now)
                                                        <button type="button" class="btn btn-primary openModal"
                                                            id="openModal" data-bs-toggle="modal" data-bs-dismiss="modal"
                                                            data-bs-target="#staticBackdrop{{ $listas->idtr }}">
                                                            Registrar Presença
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-primary">Registrar
                                                            Presença</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                </div>

                                <div class="modal fade" id="staticBackdrop{{ $listas->idtr }}" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header"
                                                style="background-color:rgb(39, 91, 189);color:white">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">ATENÇÃO!</h1>
                                                <a href="/gerenciar-tratamentos" type="button" class="btn-close"
                                                    aria-label="Close"></a>
                                            </div>
                                            <div class="modal-body">
                                                <label for="recipient-name" class="col-form-label"
                                                    style="font-size:17px">Este é o último dia de tratamento de:<br /><span
                                                        style="color: rgb(39, 91, 189)">{{ $listas->nm_1 }}</span></label>
                                                <br />

                                            </div>
                                            <div class="modal-footer">
                                                <a href="/gerenciar-tratamentos" type="button"
                                                    class="btn btn-danger">Cancelar Presença</a>
                                                <button type="type" class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>




                                <a href="/visualizar-tratamento/{{ $listas->idtr }}" type="button"
                                    class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Histórico"><i class="bi bi-search"
                                        style="font-size: 1rem; color:#000;"></i></a>

                                <a href="/alterar-grupo-tratamento/{{ $listas->idtr }}"type="button"
                                    class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Alterar Grupo"><i class="bi bi-arrow-left-right"
                                        style="font-size: 1rem; color:#000;"></i></a>

                                <a type="button" class="btn btn-outline-danger btn-sm" data-tt="tooltip"
                                    data-placement="top" data-bs-target="#inativa{{ $listas->idtr }}"
                                    data-bs-toggle="modal" title="Inativar"><i class="bi bi-x-circle"
                                        style="font-size: 1rem; color:#000;"></i></a>


                                <div class="modal fade" id="inativa{{ $listas->idtr }}" data-bs-keyboard="false"
                                    tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header"
                                                style="background-color:#DC4C64;color:white">
                                                <h1 class="modal-title fs-5" id="inativarLabel">Inativação</h1>
                                                <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label for="recipient-name" class="col-form-label"
                                                    style="font-size:17px">Tem certeza que deseja inativar:<br /><span
                                                        style="color:#DC4C64; font-weight: bold;">{{ $listas->nm_1 }}</span></label>
                                                <br />

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-bs-dismiss="modal"
                                                    class="btn btn-danger">Cancelar</button>
                                                <a href="/inativar-tratamento/{{ $listas->idtr }}" type="type"
                                                    class="btn btn-primary">Confirmar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div class="d-flex justify-content-center">
        {{ $lista->withQueryString()->links() }}
    </div>
    </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            if ({{ $diaP == null }}) { //Deixa o select status como padrao vazio
                $(".teste").prop("selectedIndex", -1);
            }

        });
    </script>
    <script>
        $(document).ready(function() {
            if ({{ $situacao == null }}) { //Deixa o select status como padrao vazio
                $(".teste1").prop("selectedIndex", -1);
            }

        });
    </script>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
