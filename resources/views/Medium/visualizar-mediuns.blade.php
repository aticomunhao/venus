@extends('layouts.app')

@section('content')
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            VISUALIZAR MÉDIUM
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{ $medium->idm }}">
                        @csrf
                        <div class="row mt-3">
                            <div class="col-4">
                                <label for="id_pessoa" class="form-label">Nome</label>
                                <select name="id_pessoa" class="form-control" disabled>
                                    <option value="{{ $medium->id_pessoa }}"> {{ $medium->nome_completo }}</option>
                                    @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->id }}"> {{ $pessoa->nome_completo}} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <label for="id_setor" class="form-label">Setor</label>
                                        <select name="id_setor" class="form-control" disabled>
                                            <option value="{{ $medium->id_setor }}"> {{ $medium->nome_setor }}</option>
                                            @foreach ($setor as $setores)
                                            <option value="{{ $setores->id }}"> {{ $setores->nome }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="id_funcao" class="form-label">Função</label>
                                        <select name="id_funcao" class="form-control" disabled>
                                            <option value="{{ $medium->id_funcao }}"> {{ $medium->nome_funcao }}</option>
                                            @foreach ($tipo_funcao as $funcao)
                                            <option value="{{ $funcao->id }}"> {{ $funcao->nome}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="status" class="form-label text-start">Status</label>
                                        <select name="status" class="form-control" disabled>
                                            <option value="1">Ativo</option>
                                            <option value="2">Inativo</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="motivo_status" class="form-label text-start">Motivo</label>
                                        <select name="motivo_status" class="form-control" disabled>
                                            <option value="1">Desencarnou</option>
                                            <option value="2">Mudou-se</option>
                                            <option value="3">Afastado</option>
                                            <option value="4">Saúde</option>
                                            <option value="5">Não informado</option>
                                        </select>
                                    </div>
                            </div>
                        </div>
                                </div>


                                        <div class="row mt-4">
                                            <div class="col">
                                                Tipo mediunidade
                                                <label class="form-label"></label>
                                                <select class="form-select" name="id_tp_mediunidade[]" multiple disabled>
                                                    @foreach ($tipo_mediunidade as $tipo)
                                                        <option value="{{ $tipo->id }}" {{ isset($medium->tipos_mediunidade) && in_array($tipo->id, (array)$medium->tipos_mediunidade) ? 'selected' : '' }}>
                                                            {{ $tipo->tipo }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                            <div class="col-md-3">
                                                @if(isset($medium->data_manifestou_mediunidade))
                                                @foreach ($tipo_mediunidade as $tipo)
                                                <div class="form-group data_manifestou" id="data_manifestou_{{ $tipo->id }}">
                                                    <label for="data_manifestou_mediunidade[{{ $tipo->id }}]"
                                                        class="form-label small mb-0"> {{ $tipo->tipo }}</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        name="data_manifestou_mediunidade[{{ $tipo->id }}]"
                                                        value="{{ isset($medium->data_manifestou_mediunidade[$tipo->id]) ? $medium->data_manifestou_mediunidade[$tipo->id] : '' }}" required="required" disabled>
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row justify-content-center">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <br>
                                <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Fechar</a>
                            </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
