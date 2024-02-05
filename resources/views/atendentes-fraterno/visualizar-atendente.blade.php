@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    VISUALIZAR ATENDENTE
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/atualizar-atendente/{{ $atendente->id }}">
                @csrf

                <div class="row">
                    <div class="col">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <select class="form-control" name="id_pessoa" disabled>
                            <option value="{{ $atendente->id }}">{{ $atendente->nome_completo }}</option>
                            @foreach ($pessoas as $pessoa)
                            <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" name="status" disabled>
                            <option value="{{ $atendente->id }}" selected>{{ $atendente->tipos }}</option>
                            @foreach ($tipo_status_pessoa as $status)
                                @if ($status->id != $atendente->id && $status->id != $atendente->status)
                                    <option value="{{ $status->id }}">{{ $status->tipo }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="data_fim" class="form-label">Data fim</label>
                        <input type="date" class="form-control" id="dt_fim" name="dt_fim" disabled value="{{ $atendente->dt_fim }}">
                    </div>
                    <div class="col">
                        <label for="motivo_status" class="form-label">Motivo</label>
                        <select class="form-control" aria-label=".form-select-lg example" name="motivo_status" disabled id="motivo_status" >
                            <option value="" {{ is_null($atendente->motivo_status) ? 'selected' : '' }}></option>
                            @foreach ($tipo_motivo_status_pessoa as $motivo)
                                @if ($motivo->motivo == 'mudou' || $motivo->motivo == 'desencarnou')
                                    <option value="{{ $motivo->id }}" {{ $atendente->motivo_status == $motivo->id ? 'selected' : '' }}>
                                        {{ $motivo->motivo }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                <div class="row mt-4">
                    <div class="col">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nome grupo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @foreach ($gruposAtendente as $grupoAtendente)
                                        <div class="mb-3">
                                            <select class="form-control" name="id_grupo[]" disabled>
                                                @foreach ($grupo as $grupos)
                                                <option value="{{ $grupos->id }}" @if ($grupos->id == $grupoAtendente->id_grupo) selected @endif>
                                                    {{ $grupos->nome }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endforeach
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br>

                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                    </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
