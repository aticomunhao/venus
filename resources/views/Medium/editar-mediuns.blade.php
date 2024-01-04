@extends('layouts.app')

@section('content')
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
                <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{ $medium->idm }}"
                    id="mediumForm">
                    @csrf

                    <div class="row mt-3">
                        <div class="col-5">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                <option value="{{ $medium->id_pessoa }}"> {{ $medium->nome_completo }}</option>
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->id }}"> {{ $pessoa->nome_completo }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <label for="id_setor" class="form-label">Setor</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="setor">
                                        <option value="{{ $medium->id_setor }}"> {{ $medium->nome_setor }}</option>
                                        @foreach ($setor as $setores)
                                            <option value="{{ $setores->id }}"> {{ $setores->nome }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="id_funcao" class="form-label">Função</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="id_funcao">
                                        <option value="{{ $medium->id_funcao }}"> {{ $medium->nome_funcao }}</option>
                                        @foreach ($tipo_funcao as $funcao)
                                            <option value="{{ $funcao->id }}"> {{ $funcao->nome }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="status" class="form-label text-start">Status</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="status"
                                        id="idstatus" required="required">
                                        <option value="1">Ativo</option>
                                        <option value="2">Inativo</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="motivo_status" class="form-label text-start">Motivo</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="motivo_status"
                                        id="motivo_status" required="required" disabled>
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
                        <div class="col-md-6">
                            <label class="form-label">Tipos de Mediunidade</label>
                            @foreach ($tipo_mediunidade as $tipo)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="id_tp_mediunidade[]"
                                        value="{{ $tipo->id }}" id="tipo_{{ $tipo->id }}"
                                        {{ isset($medium->tipos_mediunidade) && in_array($tipo->id, (array) $medium->tipos_mediunidade) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="tipo_{{ $tipo->id }}">{{ $tipo->tipo }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            @if (isset($medium->data_manifestou_mediunidade))
                                @foreach ($tipo_mediunidade as $tipo)
                                    <div class="form-group data_manifestou" id="data_manifestou_{{ $tipo->id }}">
                                        <label for="data_manifestou_mediunidade[{{ $tipo->id }}]"
                                            class="form-label small mb-0"> {{ $tipo->tipo }}</label>
                                        <input type="date" class="form-control form-control-sm"
                                            name="data_manifestou_mediunidade[{{ $tipo->id }}]"
                                            value="{{ isset($medium->data_manifestou_mediunidade[$tipo->id]) ? $medium->data_manifestou_mediunidade[$tipo->id] : '' }}"
                                            required="required">
                                    </div>
                                @endforeach
                            @endif
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


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Adiciona um ouvinte de evento para o campo "Status"
            $('#idstatus').change(function() {
                // Obtém o valor selecionado no campo "Status"
                var selectedStatus = $(this).val();
                console.log(selectedStatus);
                // Habilita ou desabilita o campo "Motivo" com base na seleção
                if (selectedStatus === '2') {
                    $('#motivo_status').prop('disabled', false);
                } else {
                    $('#motivo_status').prop('disabled', true);
                    $('#motivo_status').val(''); // Limpa a seleção quando desabilitado
                }
            });

            // Define o valor inicial do campo "Status" ao carregar a página
            $('#status').val('').trigger('change');
        });
        </script>

    @endsection
