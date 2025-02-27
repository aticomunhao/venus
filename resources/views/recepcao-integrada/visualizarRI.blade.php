@extends('layouts.app')

@section('title')
     Visualizar Tratamentos
@endsection

@section('content')
    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            VISUALIZAR TRATAMENTOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('RI') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">
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
                                                <div class="row col-10">
                                                    <div class="col-12">Data início
                                                        <input class="form-control pesquisa" type="date" id="dt_enc"
                                                            name="dt_enc" value="{{ $data_enc, old('dt_enc') }}">
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        Dia
                                                        <select class="form-select teste pesquisa" id=""
                                                            name="dia" type="number">
                                                            @foreach ($dia as $dias)
                                                                <option value="{{ $dias->id }}"
                                                                    {{ $diaP == $dias->id ? 'selected' : '' }}
                                                                    {{ $dias->id == old('dia') ? 'selected' : '' }}>
                                                                    {{ $dias->nome }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-12 mt-3">Assistido
                                                        <input class="form-control pesquisa" type="text" id="3"
                                                            name="assist" value="{{ old('assist') }}">
                                                    </div>
                                                    <div class="col-md-12 mt-3">CPF
                                                        <input class="form-control" type="text" maxlength="11"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                            id="2" name="cpf" value="{{ $cpf }}">
                                                    </div>
                                                    <div class="col-12 mt-3">Grupo
                                                        <input class="form-control pesquisa" autocomplete="off"
                                                            id="grupo" name="grupo" type="text" list="grupos"
                                                            value="{{ $cron }}">
                                                        <datalist id="grupos">
                                                            @foreach ($cronogramas as $cronograma)
                                                                <option
                                                                    value="{{ $cronograma->id }} - {{ $cronograma->nome }} - {{ $cronograma->dia }} - {{ $cronograma->h_inicio }} - {{ $cronograma->setor }}">
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                 
                                                    <div class="col-12 mt-3">Status
                                                        <select class="form-select teste1" id="4" name="status"
                                                            type="number">
                                                            @foreach ($stat as $status)
                                                                <option value="{{ $status->id }}"
                                                                    {{ $situacao == $status->id ? 'selected' : '' }}
                                                                    {{ old('status') == $status->id ? 'selected' : '' }}>
                                                                    {{ $status->nome }}
                                                                </option>
                                                            @endforeach
                                                            <option value="all"
                                                                {{ $situacao == 'all' ? 'selected' : '' }}>
                                                                Todos os Status
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a class="btn btn-secondary" href="/visualizarRI-tratamento">Limpar</a>
                                            <button type="submit" class="btn btn-primary">Confirmar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col d-flex justify-content-end">
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#filtros" style="box-shadow: 3px 5px 6px #000000; margin:5px;">
                                    Filtrar <i class="bi bi-funnel"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br />

        <div style="text-align:left;">
            <hr />
            Total assistidos: {{ $contar }}
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">PRIORIDADE</th>
                        <th class="col">ASSISTIDO</th>
                        <th class="col">REPRESENTANTE</th>
                        <th class="col">DIA</th>
                        <th class="col">HORÁRIO</th>
                        <th class="col">TRATAMENTO</th>
                        <th class="col">GRUPO</th>
                        <th class="col">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 16px; color:#000000; text-align: center;">
                    @foreach ($lista as $listas)
                        <tr>
                            <td>{{ $listas->prdesc }}</td>
                            <td>{{ $listas->nm_1 }}</td>
                            <td>{{ $listas->nm_2 }}</td>
                            <td>{{ $listas->nomed }}</td>
                            <td>{{ date('H:i', strtotime($listas->h_inicio)) }}</td>
                            <td>{{ $listas->sigla }}</td>
                            <td>{{ $listas->nomeg }}</td>
                            <td>{{ $listas->tst }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $lista->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if ({{ $diaP == null }}) {
                $(".teste").prop("selectedIndex", -1);
            }
        });

        $(document).ready(function() {
            if ({{ $situacao == null }}) {
                $(".teste1").prop("selectedIndex", 1);
            }
            $('.pesquisa').change(function() {
                $(".teste1").prop("selectedIndex", 6);
            });
        });
    </script>
@endsection
