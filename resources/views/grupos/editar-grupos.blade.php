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
                                        Nome
                                        <input type="text" class="form-control" id="nome" name="nome" maxlength="30" value="{{ $grupo[0]->nome }}" required="required">
                                    </div>
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
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_motivo" id="tipo_motivo" required="required" disabled>
                                            <option value=""> </option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->tipo }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <br>
                                        Tipo de tratamento
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_tratamento" required="required">
                                            <option value="{{ $grupo[0]->id_tipo_tratamento }}"> {{ $grupo[0]->descricao }}</option>
                                            @foreach ($tipo_tratamento as $tipo)
                                            <option value="{{ $tipo->id }}"> {{ $tipo->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        Hora início
                                        <input type="time" class="form-control" id="h_inicio" name="h_inicio" value="{{ $grupo[0]->h_inicio }}" required="required">
                                    </div>
                                    <div class="col">
                                        <br>
                                        Hora fim
                                        <input type="time" class="form-control" id="h_fim" name="h_fim" value="{{ $grupo[0]->h_fim }}" required="required">
                                    </div>
                                    <div class="col">
                                        <br>
                                        Max atendido
                                        <input type="number" class="form-control" id="max_atend" min="1" max="100" name="max_atend" oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3);" value="{{ $grupo[0]->max_atend }}" required="required">
                                    </div>
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
                    $('#tipo_motivo').prop('disabled', false);
                } else {
                    $('#tipo_motivo').prop('disabled', true);
                    $('#tipo_motivo').val(''); // Limpa a seleção quando desabilitado
                }
            });

            // Define o valor inicial do campo "Status" ao carregar a página
            $('#status_grupo').val('{{ $grupo[0]->status_grupo }}').trigger('change');
        });
    </script>
@endsection
