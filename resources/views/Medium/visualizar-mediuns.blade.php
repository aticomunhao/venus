@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <br>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                               VISUALIZAR MÉDIUM
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="col">
                                <div class="row justify-content-center">
                                    <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{$medium[0]->id}}">
                                        @csrf
                                        <div class="col-1 text-end offset-11">
                                            Status
                                            <label for="status"></label>
                                            <input type="checkbox" name="status" style="text-align: right;"
                                                data-toggle="toggle" data-onlabel="A" data-offlabel="D"
                                                data-onstyle="success" data-offstyle="" @if($medium[0]->status) checked @endif disabled>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                Nome

                                                <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa" disabled>
                                                    @foreach ($pessoas as $pessoa)
                                                        <option value="{{$pessoa->id}}" @if($pessoa->id == $medium[0]->id_pessoa) selected @endif>
                                                            {{$pessoa->nome_completo}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>
                                            <div class="col">
                                                Tipo mediunidade
                                                <select class="form-select" aria-label=".form-select-lg example" name="id_tp_mediunidade" disabled>
                                                    @foreach ($tipo_mediunidade as $tipo)
                                                        <option value="{{$tipo->id}}" @if($tipo->id == $medium[0]->id_tp_mediunidade) selected @endif>
                                                            {{$tipo->tipo}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="d-grid gap-1 col-4 mx-auto">
                                                <br>
                                                <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Cancelar</a>
                                            </div>
                                            <div class="d-grid gap-2 col-4 mx-auto">
                                                <br>
                                                <button class="btn btn-primary" type="submit">Confirmar</button>
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
