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
                                EDITAR GRUPOS
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <form class="form-horizontal mt-2" method="post" action="/editar-grupos/{{ $grupo->id }}">
                                @csrf
                                @method('PUT') <!-- Utiliza o método PUT para atualização -->

                                <div class="row">
                                    <div class="col-1 text-end offset-11">
                                        Status
                                        <label for="status"></label>
                                        <input type="checkbox" name="status" style="text-align: right;"
                                            data-toggle="toggle" data-onlabel="A" data-offlabel="D"
                                            data-onstyle="success" data-offstyle="" {{ $grupo->status_grupo ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-8">
                                        Nome
                                        <input type="text" class="form-control" id="nome" name="nome" value="{{ $grupo->nome }}">
                                    </div>

                                    <div class="col">
                                        Hora inicio
                                        <input type="hora" class="form-control" id="h_inicio" name="h_inicio" value="{{ $grupo->h_inicio }}">
                                    </div>

                                    <div class="row">

                                    <div class="col">
                                        <br>
                                        Hora fim
                                        <input type="hora" class="form-control" id="h_fim" name="h_fim" value="{{ $grupo->h_fim }}">
                                    </div>

                                    <div class="col">
                                        <br>
                                        Max atendido
                                        <input type="number" class="form-control" id="max_atend" name="max_atend" value="{{ $grupo->max_atend }}">
                                    </div>

                                    <div class="col">
                                        <br>
                                        Tipo grupo
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_grupo">
                                            @foreach ($grupos as $item)
                                                <option value="{{ $item->id }}" {{ $grupo->id_tipo_grupo == $item->id ? 'selected' : '' }}>
                                                    {{ $item->id_tipo_grupo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col">
                                        <br>
                                        Tipo de tratamento
                                        <input type="text" class="form-control" id="id_tipo_tratamento" name="id_tipo_tratamento" value="{{ $grupo->id_tipo_tratamento }}">
                                    </div>
                                </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <br>
                                        <a class="btn btn-danger" href="/gerenciar-grupos" role="button">Cancelar</a>
                                    </div>
                                    <div class="d-grid gap-2 col-4 mx-auto">
                                        <br>
                                        <button class="btn btn-primary">Confirmar</button>
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
