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
            <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{ $medium->idm }}" id="mediumForm">
                @csrf

                <div class="row mt-3">
                    <div class="col-5">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <select name="id_pessoa" class="form-control" disabled>
                            <option value="{{ $medium->id_pessoa }}"> {{ $medium->nome_completo }}</option>
                            @foreach ($pessoas as $pessoa)
                                <option value="{{ $pessoa->id }}"> {{ $pessoa->nome_completo }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <label for="id_grupo" class="form-label">Nome grupo</label>
                                <select name="id_grupo" class="form-control" disabled>
                                    <option value="{{ $medium->id_grupo }}"> {{ $medium->nome_grupo }}</option>
                                    @foreach ($grupo as $grupos)
                                        <option value="{{ $grupos->id }}"> {{ $grupos->nome }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="id_funcao" class="form-label">Função</label>
                                <select name="id_funcao" class="form-control" disabled>
                                    <option value="{{ $medium->id_funcao }}"> {{ $medium->nome_funcao }}</option>
                                    @foreach ($tipo_funcao as $funcao)
                                        <option value="{{ $funcao->id }}"> {{ $funcao->nome }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-4">
                        <label for="id_setor" class="form-label">Setor</label>
                        <select name="id_setor" class="form-control" disabled>
                            <option value="{{ $medium->id_setor }}"> {{ $medium->nome_setor }}</option>
                            @foreach ($setor as $setores)
                                <option value="{{ $setores->id }}"> {{ $setores->nome }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="status" class="form-label text-start">Status</label>
                        <select name="status" class="form-control" disabled>
                            <option value="1" {{ $medium->status == 1 ? 'selected' : '' }}>Ativo</option>
                            <option value="2" {{ $medium->status == 2 ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="motivo_status" class="form-label text-start">Motivo</label>
                        <select name="motivo_status" class="form-control" disabled>
                            <option value="1" {{ $medium->motivo_status == 1 ? 'selected' : '' }}>Desencarnou</option>
                            <option value="2" {{ $medium->motivo_status == 2 ? 'selected' : '' }}>Mudou-se</option>
                            <option value="3" {{ $medium->motivo_status == 3 ? 'selected' : '' }}>Afastado</option>
                            <option value="4" {{ $medium->motivo_status == 4 ? 'selected' : '' }}>Saúde</option>
                            <option value="5" {{ $medium->motivo_status == 5 ? 'selected' : '' }}>Não informado</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <label for="id_mediunidade" class="form-label">Mediunidades</label>
                        @foreach ($mediunidade_medium as $mediuns)
                        <select name="id" class="form-control" disabled>
                            @foreach ($tipo_mediunidade as $tipos)
                                <option value="{{ $tipos->id }}" @if ($tipos->id == $mediuns->id_mediunidade) selected @endif>
                                    {{ $tipos->tipo }}
                                </option>
                            @endforeach
                        </select>
                        @endforeach
                    </div>
                    <div class="col">
                        <label for="data_inicio" class="form-label">Data que manifestou</label>
                        @foreach ($mediunidade_medium as $mediunidades)
                        <select name="data_inicio" class="form-control" disabled>
                            <option value="{{ $mediunidades->id_mediuns }}" @if ($mediunidades->data_inicio == $mediuns->data_inicio) selected @endif>
                                {{ $mediunidades->data_inicio }}
                            </option>
                        </select>
                        @endforeach
                    </div>
                </div>
                <br>
                <div class="row mt-1 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Cancelar</a>
                    </div>
                    <div class="d-grid gap-2 col-4 mx-auto">
                        <button class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
