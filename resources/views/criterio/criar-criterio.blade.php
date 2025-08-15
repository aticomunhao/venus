@extends('layouts.app')
@section('content')
    <br>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Criar Critério
            </div>
            <form method="POST" action="">
                @csrf
                <div class="card-body">
                    {{-- Setor e Atividade --}}
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

                    {{-- Linha de adição de critério --}}
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
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="addCriterio">Adicionar</button>
                        </div>
                    </div>

                    {{-- Tabela --}}
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

                    {{-- Botão salvar --}}
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- jQuery --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script>
        $(function() {
            $('#addCriterio').click(function() {
                let criterioId = $('#criterioSelect').val();
                let criterioNome = $('#criterioSelect option:selected').text();
                let tipoValor = $('#criterioSelect option:selected').data('tipovalor');

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
                // Gera input conforme o tipo
                let inputValor = '';
                switch (tipoValor) {
                    case 'numero':
                        inputValor =
                            `<input type="number" class="form-control" name="valores[]" required>`;
                        break;
                    case 'texto':
                        inputValor = `<input type="text" class="form-control" name="valores[]" required>`;
                        break;
                    case 'data':
                        inputValor = `<input type="date" class="form-control" name="valores[]" required>`;
                        break;
                    case 'boolean':
                        inputValor = `
                    <select class="form-select" name="valores[]" required>
                        <option value="">Selecione...</option>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                `;
                        break;
                    default:
                        inputValor = `<input type="text" class="form-control" name="valores[]" required>`;
                }

                // Adiciona linha
                let linha = `
            <tr>
                <td>${criterioNome}
                    <input type="hidden" name="criterios[]" value="${criterioId}">
                </td>
                <td>${tipoValor}</td>
                <td>${inputValor}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remover">Remover</button>
                </td>
            </tr>
        `;
                $('#criteriosTable tbody').append(linha);

                // Limpa seleção
                $('#criterioSelect').val('');
            });

            // Remover linha
            $(document).on('click', '.remover', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>
@endsection
