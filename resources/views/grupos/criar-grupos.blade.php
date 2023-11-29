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
                                CADASTRAR GRUPOS
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="col-12">
                                <div class="row justify-content-center">

                                    <form class="form-horizontal mt-2" method="post" action="/incluir-grupos">
                                        @csrf

                                        <div class="row">
                                            <div class="col-1 text-end offset-11">
                                                Status
                                                <label for="status"></label>
                                                <input type="checkbox" name="status" style="text-align: right;"
                                                    data-toggle="toggle" data-onlabel="A" data-offlabel="D"
                                                    data-onstyle="success" data-offstyle="" @if($grupos[0]->status_grupo) checked @endif>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col">
                                                Nome
                                                <select class="form-select" aria-label=".form-select-lg example" name="nome">
                                                    @foreach ($grupos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->nome }}</option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>

                                            <div class="col">
                                                Hora inicio
                                                <select class="form-select" aria-label=".form-select-lg example" name="h_inicio">
                                                    @foreach ($grupos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->h_inicio }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col">
                                                Hora fim
                                                <select class="form-select" aria-label=".form-select-lg example" name="h_fim">
                                                    @foreach ($grupos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->h_fim }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col">
                                                Max aten
                                                <select class="form-select" aria-label=".form-select-lg example" name="max_atend">
                                                    @foreach ($grupos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->max_atend }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                Tipo grupo
                                                <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_grupo">
                                                    @foreach ($grupos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->id_tipo_grupo }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- <div class="col">
                                                Status grupo
                                                <select class="form-select" aria-label=".form-select-lg example" name="status_grupo">
                                                    @foreach ($grupos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->status_grupo }}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}

                                            <div class="col">
                                                Tipo tratamento
                                                <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_tratamento">
                                                    @foreach ($grupos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->id_tipo_tratamento }}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                        </div>
                                        </div>

                                        <br>

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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
