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
                <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{ $medium->idm }}"
                    id="mediumForm">
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
                        <div class="col-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" aria-label=".form-select-lg example" name="id_pessoa" id="status"
                                required="required" disabled>
                                @foreach ($tipo_status_pessoa as $tipos)
                                    <option value="{{ $tipos->id }}"
                                        {{ $medium->status == $tipos->id ? 'selected' : '' }}>
                                        {{ $tipos->tipos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="motivo_status" class="form-label">Motivo status</label>
                            <select class="form-control" aria-label=".form-select-lg example" name="motivo_status"
                                id="motivo_status" required="required" disabled>
                                <option value=""></option>
                                @foreach ($tipo_motivo_status_pessoa as $motivo)
                                    <option value="{{ $motivo->id }}"
                                        {{ $medium->motivo_status == $motivo->id ? 'selected' : '' }}>
                                        {{ $motivo->motivo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
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
                                    <option value="{{ $funcao->id }}"> {{ $funcao->nome }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="id_grupo" class="form-label">Nome grupo</label>
                            <select name="id_grupo" class="form-control" disabled>
                                <option value="{{ $medium->id_grupo }}"> {{ $medium->nome_grupo }}</option>
                                @foreach ($grupo as $grupos)
                                    <option value="{{ $grupos->id }}"> {{ $grupos->nome }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="id_mediunidade" class="form-label"></label>
                        </div>
                        <div class="col">
                            <label for="data_inicio" class="form-label"></label>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table
                            class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">Mediunidades</th>
                                    <th class="text-center">Data que manifestou</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mediunidade_medium as $mediuns)
                                    <tr>
                                        <td class="text-center">
                                            <select name="id" class="form-control" disabled>
                                                @foreach ($tipo_mediunidade as $tipos)
                                                    <option value="{{ $tipos->id }}"
                                                        @if ($tipos->id == $mediuns->id_mediunidade) selected @endif>
                                                        {{ $tipos->tipo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <select name="data_inicio" class="form-control" disabled>
                                                <option value="{{ $mediuns->id_mediuns }}" selected>
                                                    {{ $mediuns->data_inicio }}
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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
