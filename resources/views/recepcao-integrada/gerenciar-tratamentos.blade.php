@extends('layouts.app')

@section('title')
    Gerenciar Tratamentos
@endsection

@section('content')
 

    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR TRATAMENTOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('gtcdex') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">

                            <div class ="col-1">Data início
                                <input class="form-control pesquisa" type="date" id="dt_enc" name="dt_enc"
                                    value="{{ $data_enc }}">
                            </div>

                            <div class="col-1">
                                Dia
                                <select class="form-select teste pesquisa" id="" name="dia" type="number">
                                    @foreach ($dia as $dias)
                                        <option value="{{ $dias->id }}" {{ $diaP == $dias->id ? 'selected' : '' }}>
                                            {{ $dias->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-2">Assistido
                                <input class="form-control pesquisa" type="text" id="3" name="assist"
                                value="{{ $assistido }}">
                            </div>
                            
                            <div class="col-md-2">CPF
                                <input class="form-control" type="text" maxlength="11"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    id="2" name="cpf" value="{{ $cpf }}">
                           
                        </div>
                            <div class="col-1">Status
                                <select class="form-select teste1" id="4" name="status" type="number">

                                    @foreach ($stat as $status)
                                        <option value="{{ $status->id }}"
                                            {{ $situacao == $status->id ? 'selected' : '' }}> {{ $status->nome }} </option>
                                    @endforeach
                                    <option value="all"
                                        {{ $situacao == 'all' ? 'selected' : '' }}> Todos os Status </option>
                                </select>

                            </div>

                            <div class="col">
                                <br />
                                <input class="btn btn-light btn-sm me-md-2"
                                style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">

                                <a href="/gerenciar-tratamentos"><input class="btn btn-light btn-sm me-md-2"
                                    style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>


                                        @if(in_array(17,session()->get('usuario.acesso')))

                                        <a href="/job"><input class="btn btn-light btn-sm me-md-2"
                                                style="box-shadow: 1px 1px 3px #000000; margin:5px;" type="button"
                                                value="Job"></a>
                                        @endif

                                        <a href="/incluir-avulso" class="btn btn-danger btn-sm"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;">Atendimento de Emergência</a>


                                        <a href="/gerenciar-encaminhamentos" class="btn btn-warning btn-sm"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;">Encaminhamentos</a>
                    </form>

                </div>
            </div>
            <br />


        </div style="text-align:right;">
        <hr />
Total assistidos: {{ $contar }}
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




                                @if ($listas->status == 1 or $listas->status == 2){{-- Botão de presença --}}
                                    <button type="button" class="btn btn-outline-warning tooltips" data-bs-toggle="modal"
                                        data-bs-target="#presenca{{ $listas->idtr }}"><span class="tooltiptext">Presença</span><i
                                            class="bi bi bi-exclamation-triangle"
                                            style="font-size: 1rem; color:#000;"></i></button>
                                @else
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" {{-- botão de presença --}}
                                        data-tt="tooltip" data-placement="top" title="Presença"
                                        data-bs-target="#presenca{{ $listas->idtr }}" disabled><i
                                            class="bi bi bi-exclamation-triangle"
                                            style="font-size: 1rem; color:#000;"></i></button>
                                @endif

                                {{-- inicio da modal de presença --}}
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
                                            <button data-bs-dismiss="modal" type="button" class="btn-close"
                                            aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label for="recipient-name" class="col-form-label"
                                            style="font-size:17px">Este é o último dia de tratamento de:<br /><span
                                            style="color: rgb(39, 91, 189)">{{ $listas->nm_1 }}</span></label>
                                            <br />

                                        </div>
                                        <div class="modal-footer">
                                            <button data-bs-dismiss="modal" type="button"
                                            class="btn btn-danger">Cancelar</button>
                                            <button type="type" class="btn btn-primary">Confirmar Presença</button>
                                        </div>
                                    </div>
                                </div>
                                {{-- fim da modal de presença --}}
                            </div>
                        </form>




                                <a href="/visualizar-tratamento/{{ $listas->idtr }}" type="button"{{-- botão de histórico --}}
                                    class="btn btn-outline-primary btn-sm tooltips">
                                    <span class="tooltiptext">Histórico</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></a>



                                @if ($listas->status == 1 or $listas->status == 2){{-- botao de inativar --}}
                                    <a type="button" class="btn btn-outline-danger btn-sm tooltips" data-bs-target="#inativa{{ $listas->idtr }}"
                                        data-bs-toggle="modal"><span class="tooltiptext">Inativar</span><i class="bi bi-x-circle"
                                            style="font-size: 1rem; color:#000;"></i></a>

                                @else
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-tt="tooltip"
                                        data-placement="top" data-bs-target="#inativa{{ $listas->idtr }}"
                                        data-bs-toggle="modal" title="Inativar" disabled><i class="bi bi-x-circle"
                                            style="font-size: 1rem; color:#000;"></i></button>
                                @endif
                                        {{-- modal de inativação --}}
                                <form action="/inativar-tratamento/{{ $listas->idtr }}">
                                    <div class="modal fade" id="inativa{{ $listas->idtr }}" data-bs-keyboard="false"
                                        tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:#DC4C64;color:white">
                                                    <h1 class="modal-title fs-5" id="inativarLabel">Inativação</h1>
                                                    <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                        aria-label="Close"></button>
                                                </div>
                                                <br />
                                                <div class="modal-body">
                                                    <label for="recipient-name" class="col-form-label"
                                                        style="font-size:17px">Tem certeza que deseja inativar:<br /><span
                                                            style="color:#DC4C64; font-weight: bold;">{{ $listas->nm_1 }}</span>&#63;</label>
                                                    <br />

                                                    <center>
                                                        <div class="mb-2 col-10">
                                                            <label class="col-form-label">Insira o motivo da
                                                                <span style="color:#DC4C64">inativação:</span></label>
                                                            <select class="form-select teste1" name="motivo" required>

                                                                @foreach ($motivo as $motivos)
                                                                    <option value="{{ $motivos->id }}">
                                                                        {{ $motivos->tipo }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </center>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" data-bs-dismiss="modal"
                                                        class="btn btn-danger">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- fim modal de inativação --}}
                                </form>

                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        {{ $lista->links('pagination::bootstrap-5') }}
    </div>
    </div>
    </div>


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
                $(".teste1").prop("selectedIndex", 1);
            }
            $('.pesquisa').change(function(){
                $(".teste1").prop("selectedIndex", 6);
            })

        });
    </script>


    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
