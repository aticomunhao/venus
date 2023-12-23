<!-- resources/views/medium/editar-mediuns.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        VISUALIZAR MÉDIUM
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{ $medium->id }}">
                    @csrf


                    <div class="row mt-3">
                        <div class="col-5">
                            <label class="form-label">Nome</label>
                            <select class="form-control" aria-label=".form-select-lg example" name="id_pessoa"disabled>
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{$pessoa->id}}" @if($pessoa->id == $medium->id_pessoa) selected @endif>
                                        {{$pessoa->nome_completo}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="tipo_funcao" class="form-label">Função</label>
                            <select class="form-control" aria-label=".form-select-lg example" name="tipo_funcao" disabled>
                                @foreach ($tipo_funcao as $funcao)
                                    <option value="{{ $funcao }}" {{ $funcao == $medium->nome ? 'selected' : '' }}>
                                        {{ $funcao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" aria-label=".form-select-lg example" name="status"disabled>
                                <option value="ativo" @if($medium->status == 'ativo') selected @endif>Ativo</option>
                                <option value="inativo" @if($medium->status == 'inativo') selected @endif>Inativo</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Motivo Status</label>
                            <select class="form-control" aria-label=".form-select-lg example" name="motivo_status" disabled>
                                @foreach ($tipo_motivo_status_pessoa as $motivo)
                                    <option value="{{ $motivo }}" @if(isset($medium->motivo_status) && $motivo == $medium->motivo_status) selected @endif>
                                        {{ $motivo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipos de mediunidade</label>
                            @foreach ($tipo_mediunidade as $tipo)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="id_tp_mediunidade[]" value="{{ $tipo->id }}" id="tipo_{{ $tipo->id }}" @if(in_array($tipo->id, explode(',', $medium->id_tp_mediunidade))) checked @endif disabled>
                                    <label class="form-check-label" for="tipo_{{ $tipo->id }}">{{ $tipo->tipo }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Data manifestou mediunidade</label>
                            <input type="date" class="form-control" id="data_manifestou_mediunidade" name="data_manifestou_mediunidade" value="{{ $medium->data_manifestou_mediunidade }}" disabled>
                        </div>
                    </div>

                    <div class="row mt-2 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <br>
                            <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
