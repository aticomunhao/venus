@extends('layouts.app')

@section('content')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="/venus/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        VISUALIZAR MEMBRO
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-membro/{{ $membro->idm }}">
                    @csrf

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="id_associado" class="form-label">Nome</label>
                                <select class="form-control" aria-label=".form-select-lg example" name="id_associado" disabled>
                                    @foreach ($associado as $associados)
                                        <option value="{{ $associados->id }}" @if($membro->id_associado == $associados->id) selected @endif>{{ $associados->nome_completo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="tipo_status_pessoa" class="form-label">Status</label>
                                <select class="form-control" aria-label=".form-select-lg example" name="tipo_status_pessoa" disabled>
                                    @foreach ($tipo_status_pessoa as $tipo)
                                        <option value="{{ $tipo->id }}" @if($membro->id_associado == $tipo->id) selected @endif>{{ $tipo->tipos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="id_funcao" class="form-label">Função</label>
                                <select class="form-control" aria-label=".form-select-lg example" name="id_funcao" disabled>
                                    @foreach ($tipo_funcao as $funcao)
                                        <option value="{{ $funcao->id }}" @if($membro->id_funcao == $funcao->id) selected @endif>{{ $funcao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="id_grupo" class="form-label">Nome grupo</label>
                                <select class="form-control" aria-label=".form-select-lg example" name="id_grupo" disabled>
                                    @foreach ($grupo as $grupos)
                                        <option value="{{ $grupos->id }}" @if($membro->id_grupo == $grupos->id) selected @endif>{{ $grupos->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-1 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-membro" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection