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

                    {{-- Inicio Select Nome, atualmente Disabled --}}
                    <div class="row">
                        <div class="col">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-control" name="id_pessoa" disabled>
                                {{-- Codigo inutil --}}
                                <option value="{{ $atendente->id }}">{{ $atendente->nome_completo }}</option>
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                                {{-- Fim código inutl --}}
                            </select>
                        </div>
                    </div>
                    {{-- Fim Select Nome --}}


                    <div class="row mt-4">
                        {{-- Inicio Select Status, disabled --}}
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
                        {{-- Fim Select Status --}}

                        {{-- Input data Fim, disabled --}}
                        <div class="col">
                            <label for="data_fim" class="form-label">Data fim</label>
                            <input type="date" class="form-control" id="dt_fim" name="dt_fim" disabled
                                value="{{ $atendente->dt_fim }}">
                        </div>
                        {{-- Fim Input data fim --}}

                        {{-- Select Motivo, disabled --}}
                        <div class="col">
                            <label for="motivo_status" class="form-label">Motivo</label>
                            <select class="form-control" aria-label=".form-select-lg example" name="motivo_status" disabled
                                id="motivo_status">
                                <option value="" {{ is_null($atendente->motivo_status) ? 'selected' : '' }}></option>
                                @foreach ($tipo_motivo_status_pessoa as $motivo)
                                    @if ($motivo->motivo == 'mudou' || $motivo->motivo == 'desencarnou')
                                        <option value="{{ $motivo->id }}"
                                            {{ $atendente->motivo_status == $motivo->id ? 'selected' : '' }}>
                                            {{ $motivo->motivo }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        {{-- Fim Select Motivo --}}

                        <div class="row mt-4">
                            <div class="col">
                                Nome Grupo
                                <hr />
                                {{-- Foreach de grupos, disabled --}}
                                @foreach ($gruposAtendente as $grupoAtendente)
                                    <div class="mb-3">
                                        <select class="form-control" name="id_grupo[]" disabled>
                                            @foreach ($grupo as $grupos)
                                                <option value="{{ $grupos->id }}"
                                                    @if ($grupos->id == $grupoAtendente->id_grupo) selected @endif>
                                                    {{ $grupos->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                                {{-- Fim Foreach Grupos --}}
                            </div>
                        </div>

                        <br>
                        {{-- Botão de retornar --}}
                        <div class="row mt-4 justify-content-center">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-primary" href="/gerenciar-atendentes" role="button">Retornar</a>
                            </div>
                        </div>
                        {{-- Fim Botao Retornar --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
