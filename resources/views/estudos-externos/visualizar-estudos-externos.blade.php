@extends('layouts.app')

@section('title')
    Visualizar Estudos Externos
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Card principal -->
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                VISUALIZAR ESTUDOS EXTERNOS
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="setor">Setor</label>
                                    <select class="form-select select2" name="setor" id="setor" disabled>
                                        @foreach ($setores as $setor)
                                            <option value="{{ $setor->id }}"
                                                {{ $setor->id == $lista->setor ? 'selected' : '' }}>
                                                {{ $setor->sigla }} - {{ $setor->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">Pessoa
                                    <select class="form-select select2" name="pessoa" disabled>
                                        <option value="">Selecione uma pessoa</option>
                                        @foreach ($pessoas as $pessoa)
                                            <option value="{{ $pessoa->id }}"
                                                {{ $pessoa->id == $lista->id_pessoa ? 'selected' : '' }}>
                                                {{ $pessoa->nome_completo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card para Pessoa / Instituição / Anexo -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>CURSO</span>
                    </div>
                    <div class="form-group row" style="margin: 5px; margin-top: 5px; margin-bottom: 15px;">
                        <div class="col-md-5">Instituição
                            <select class="form-select select2" name="instituicao" disabled>
                                <option value="">Selecione uma instituição</option>
                                @foreach ($instituicoes as $instituicao)
                                    <option value="{{ $instituicao->id }}"
                                        {{ $instituicao->id == $lista->instituicao ? 'selected' : '' }}>
                                        {{ $instituicao->nome_fantasia }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">Estudo
                            <select class="form-select select2" name="estudo" disabled>
                                <option value="">Selecione um estudo</option>
                                @foreach ($estudos as $estudo)
                                    <option value="{{ $estudo->id }}"
                                        {{ $estudo->id == $lista->id_tipo_atividade ? 'selected' : '' }}>
                                        {{ $estudo->sigla }} -
                                        {{ $estudo->id_semestre ?? 'N/P' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">Início
                            <input type="date" class="form-control" value="{{ old('dt_inicial', $lista->data_inicio) }}"
                                name="dt_inicial" disabled>
                        </div>
                        <div class="col-md-3">Término
                            <input type="date" class="form-control" value="{{ old('dt_fim', $lista->data_fim) }}"
                                name="dt_final" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="arquivo">Arquivo de Anexo</label>
                            <div class="row align-items-center">
                                {{-- Link para arquivo atual --}}
                                @if (!empty($lista->documento_comprovante))
                                    <div class="col-auto">
                                        <a href="{{ asset('storage/' . $lista->documento_comprovante) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm">
                                            Visualizar Arquivo
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Botões de ação -->
                <br>
                <div class="row mb-3">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-primary" href="javascript:history.back()" role="button">Retornar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
