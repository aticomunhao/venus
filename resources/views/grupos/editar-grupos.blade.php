@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                EDITAR GRUPOS
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <form class="form-horizontal mt-2" method="post" action="/atualizar-grupos/{{ $grupo[0]->id }}">
                                @csrf

                                <div class="row">
                                    <div class="col">
                                        Número
                                        <select class="form-select" aria-label=".form-select-lg example" name="id" id="id" disabled>
                                            <option value="{{ $grupo[0]->id }}"> {{ $grupo[0]->id }}</option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->id }} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-5">
                                        Nome
                                        <input type="text" class="form-control" id="nome" name="nome" maxlength="30" value="{{ $grupo[0]->nome }}" required="required" oninput="validarLetrasEspeciais(this)">
                                    </div>

                                    <script>
                                        function validarLetrasEspeciais(input) {
                                            // Permite letras, espaços e caracteres especiais
                                            input.value = input.value.replace(/[^a-zA-Z\u00C0-\u00FF\s]/g, '');
                                        }
                                    </script>


                                    <div class="col-3">
                                        Status
                                        <select class="form-select" aria-label=".form-select-lg example" name="status_grupo" id="status_grupo" required="required">
                                            <option value="1">Ativo</option>
                                            <option value="2">Inativo</option>
                                            <option value="3">Experimental</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        Motivo
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_motivo_inativacao" id="id_motivo_inativacao" required="required" disabled>
                                            <option value="{{ $grupo[0]->id_motivo_inativacao }}"> {{ $grupo[0]->tipo }}</option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->tipo }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        <br>
                                        Tipo grupo
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_grupo" required="required">
                                            <option value="{{ $grupo[0]->id_tipo_grupo }}" > {{ $grupo[0]->nmg }}</option>
                                            @foreach ($tipo_grupo as $item)
                                                <option value="{{ $item->id }}" > {{ $item->nm_tipo_grupo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <br>
                                        Data início
                                        <input type="date" class="form-control" id="h_inicio" name="data_inicio" value="{{ $grupo[0]->data_inicio }}"disabled>
                                    </div>
                                    <div class="col">
                                        <br>
                                        Data fim
                                        <input type="date" class="form-control" id="h_fim" name="data_fim" value="{{ $grupo[0]->data_fim }}" disabled>
                                    </div>
                                        <div class="col">
                                            <br>
                                            Setor
                                            <select class="form-select" aria-label=".form-select-lg example" name="id_setor" required="required">
                                                @foreach ($setor as $setores)
                                                <option value="{{ $grupo[0]->id_setor}}" > {{ $grupo[0]->nm_setor}}</option>
                                                    <option value="{{ $setores->id}}">{{ $setores->nome}}</option>
                                                @endforeach
                                            </select>
                                       
                                    </div>


                                <div class="row justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <br>
                                        <a class="btn btn-danger" href="/gerenciar-grupos" role="button">Cancelar</a>
                                    </div>
                                    <div class="d-grid gap-2 col-4 mx-auto">
                                        <br>
                                        <button class="btn btn-primary">Confirmar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            // Adiciona um ouvinte de evento para o campo "Status"
            $('#status_grupo').change(function () {
                // Obtém o valor selecionado no campo "Status"
                var selectedStatus = $(this).val();

                // Habilita ou desabilita o campo "Motivo" com base na seleção
                if (selectedStatus === '2') {
                    $('#id_motivo_inativacao').prop('disabled', false);
                } else {
                    $('#id_motivo_inativacao').prop('disabled', true);
                    $('#id_motivo_inativacao').val(''); // Limpa a seleção quando desabilitado
                }
            });

            // Define o valor inicial do campo "Status" ao carregar a página
            $('#status_grupo').val('{{ $grupo[0]->status_grupo }}').trigger('change');
        });
    </script>
@endsection
