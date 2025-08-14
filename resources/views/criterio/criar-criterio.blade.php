@extends('layouts.app')
@section('content')
    <br>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Criar Critério
            </div>
            <form method="POST" >
                @csrf
                <div class="card-body">
                    <div class="row justify-content-around">
                        <div class="col-md-4 col-sm-12">
                            <label for="idsetor" class="form-label">Setor:</label>
                            <select class="form-select select2" id="idsetor" name="setor" required>
                                @foreach ($setores as $setor)
                                    <option value="{{ $setor->ids }}">{{ $setor->nome }} - {{ $setor->sigla }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label for="idatividade" class="form-label">Atividade:</label>
                            <select class="form-select select2" id="idatividade" name="atividade" required>
                                @foreach ($tipos_tratamentos as $tipo)
                                    <option value="{{ $tipo->id }}">
                                        {{ $tipo->descricao }} - {{ $tipo->sigla }}
                                        {{ $tipo->id_semestre ? ' - ' . $tipo->id_semestre : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label for="criterioSelect">Critério:</label>
                            <select id="criterioSelect" class="form-select">
                                <option value="">Selecione...</option>
                                @foreach ($tipos_criterios as $crit)
                                    <option value="{{ $crit->id }}" data-tipovalor="{{ $crit->tipo_valor }}">
                                        {{ $crit->descricao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="valorInput">Valor:</label>
                            <input type="text" id="valorInput" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="addCriterio">Adicionar</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered align-middle" id="criteriosTable">
                            <thead>
                                <tr>
                                    <th>CRITÉRIO</th>
                                    <th>TIPO CRITÉRIO</th>
                                    <th>VALOR</th>
                                    <th>AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {

            $('#addCriterio').click(function() {
                let criterioId = $('#criterioSelect').val();
                let criterioNome = $('#criterioSelect option:selected').text();
                let tipoValor = $('#criterioSelect option:selected').data('tipovalor');
                let valor = $('#valorInput').val();

                if (!criterioId) {
                    alert('Selecione um critério antes de adicionar.');
                    return;
                }

                // Evita duplicados
                let existe = false;
                $('input[name="criterios[]"]').each(function() {
                    if ($(this).val() == criterioId) {
                        existe = true;
                    }
                });
                if (existe) {
                    alert('Este critério já foi adicionado.');
                    return;
                }

                // Adiciona na tabela
                let linha = `
            <tr>
                <td>${criterioNome}
                    <input type="hidden" name="criterios[]" value="${criterioId}">
                </td>
                <td>${tipoValor}</td>
                <td>
                    <input type="text" class="form-control" name="valores[]" value="${valor}" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remover">Remover</button>
                </td>
            </tr>
        `;
                $('#criteriosTable tbody').append(linha);

                // Limpa campos
                $('#criterioSelect').val('');
                $('#valorInput').val('');
            });

            // Remover linha
            $(document).on('click', '.remover', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>
@endsection
