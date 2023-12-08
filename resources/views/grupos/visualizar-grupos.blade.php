@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                VISUALIZAR GRUPOS
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <form class="form-horizontal mt-2" method="post" action="/incluir-grupos">
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        Nome
                                        <input type="text" class="form-control" id="nome" name="nome" value="{{ $grupo[0]->nome }}" disabled>
                                    </div>

                                    <div class="col">
                                        Status
                                        <select class="form-select" aria-label=".form-select-lg example" name="status_grupo" disabled="required">
                                            <option value="{{ $grupo[0]->status_grupo }}"> {{ $grupo[0]->descricao1 }}</option>
                                            @foreach ($tipo_status_grupo as $tipos)
                                                <option value="{{ $tipos->id }}"> {{ $tipos->descricao1 }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-3">
                                        Motivo
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_motivo" disabled="required">
                                            <option value="{{ $grupo[0]->id_tipo_motivo }}"> {{ $grupo[0]->tipo }}</option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->tipo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                    <div class="col-5">
                                        <br>
                                        Tipo de tratamento
                                        <select class="form-select" aria-label=".form-select-lg example" name="descricao" disabled>
                                            @foreach ($grupo as $item)
                                                <option value="{{ $item->id }}" {{ $grupo[0]->descricao == $item->id ? 'selected' : '' }}>
                                                    {{ $item->descricao }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <br>
                                        Tipo grupo
                                        <select class="form-select" aria-label=".form-select-lg example" name="nm_tipo_grupo" disabled>
                                            @foreach ($grupo as $item)
                                                <option value="{{ $item->id }}" {{ $grupo[0]->nm_tipo_grupo == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nm_tipo_grupo}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <br>
                                        Hora início
                                        <input type="time" class="form-control" id="h_inicio" name="h_inicio" value="{{ $grupo[0]->h_inicio}}" disabled>
                                    </div>

                                    <div class="col">
                                        <br>
                                        Hora fim
                                        <input type="time" class="form-control" id="h_fim" name="h_fim" value="{{ $grupo[0]->h_fim }}" disabled>
                                    </div>

                                    <div class="col">
                                        <br>
                                        Max atendido
                                        <input type="number" class="form-control" id="max_atend" name="max_atend" value="{{ $grupo[0]->max_atend }}" disabled>
                                    </div>


                                </div>
                            </div>
                                <div class="row justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <br>
                                        <a class="btn btn-danger" href="/gerenciar-grupos" role="button">Fechar</a>
                                    </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
