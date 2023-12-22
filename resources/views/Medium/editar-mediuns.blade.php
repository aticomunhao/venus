@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        EDITAR MÉDIUM
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{$medium[0]->id}}">
                    @csrf

                    <div class="row">
                        <div class="col">
                            Nome
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{$pessoa->id}}" @if($pessoa->id == $medium[0]->id_pessoa) selected @endif>
                                        {{$pessoa->nome_completo}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            Status
                            <select class="form-select" aria-label=".form-select-lg example" name="status">
                                <option value="ativo" @if($medium[0]->status == 'ativo') selected @endif>Ativo</option>
                                <option value="inativo" @if($medium[0]->status == 'inativo') selected @endif>Inativo</option>
                            </select>
                        </div>
                        <div class="col">
                        Motivo Status
                        <select class="form-select" aria-label=".form-select-lg example" name="motivo_status">
                            @foreach ($tipo_motivo_status_pessoa as $motivo)
                                <option value="{{ $motivo }}" @if(isset($medium->motivo_status) && $motivo == $medium->motivo_status) selected @endif>
                                    {{ $motivo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                        <div class="col">
                            Tipo mediunidade
                            <select class="form-select" aria-label=".form-select-lg example" name="id_tp_mediunidade">
                                @foreach ($tipo_mediunidade as $tipo)
                                    <option value="{{$tipo->id}}" @if($tipo->id == $medium[0]->id_tp_mediunidade) selected @endif>
                                        {{$tipo->tipo}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2 justify-content-center">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
