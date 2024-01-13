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
            <form class="form-horizontal mt-2" method="post" action="/atualizar-mediuns/{{ $medium->idm }}" id="mediumForm">
                @csrf

                <div class="row mt-3">
                    <div class="col-4">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                            <option value="{{ $medium->id_pessoa }}"> {{ $medium->nome_completo }}</option>
                            @foreach ($pessoas as $pessoa)
                                <option value="{{ $pessoa->id }}"> {{ $pessoa->nome_completo }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <label for="id_grupo" class="form-label">Nome grupo</label>
                        <select class="form-select" aria-label=".form-select-lg example" name="id_grupo">
                            <option value="{{ $medium->id_grupo }}"> {{ $medium->nome_grupo }}</option>
                            @foreach ($grupo as $grupos)
                                <option value="{{ $grupos->id }}"> {{ $grupos->nome }} </option>
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
                    <div class="row mt-3">
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
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" aria-label=".form-select-lg example" name="status" id="idstatus" required="required">
                            <option value="{{ $medium->id_pessoa }}"> {{ $medium->status }}</option>
                            @foreach ($tipo_status_pessoa as $tipo)
                                <option value="{{ $tipo->id }}"> {{ $tipo->tipos }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <label for="motivo_status" class="form-label">Motivo status</label>
                        <select class="form-select" aria-label=".form-select-lg example" name="motivo_status" id="motivo_status" required="required" disabled>
                            <option value="{{ $medium->id_pessoa }}"> {{ $medium->motivo_status }}</option>
                            @foreach ($tipo_motivo_status_pessoa as $motivo)
                                <option value="{{ $motivo->id }}"> {{ $motivo->motivo }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
                <?php $a = 1; ?>

                <div class="row mt-3">
                    <div class="col">
                        <label for="id_mediunidade" class="form-label">Mediunidades</label>
                        <div class="form-check">
                            @foreach ($tipo_mediunidade as $tipos)
                                <?php
                                $isChecked = in_array($tipos->id, $createdMediunidadeIds) ? 'checked' : '';
                                ?>
                                <input class="form-check-input mediunidade-checkbox" type="checkbox" name="mediunidades[]" value="{{ $tipos->id }}" id="mediunidade_{{ $a++ }}" {{ $isChecked }}>
                                <label class="form-check-label" for="mediunidade_{{ $a }}">
                                    {{ $tipos->tipo }}
                                </label><br>
                            @endforeach
                        </div>
                    </div>

                    <div class="col">
                        @foreach ($tipo_mediunidade as $tipos)
                            <?php
                            $dateValue = in_array($tipos->id, $createdMediunidadeIds) ? $createdMediunidadeData[$tipos->id] : '';
                            ?>
                            <div class="form-group data_manifestou" name="id_mediunidade_medium" id="data_inicio_{{ $tipos->id }}" style="{{ $dateValue ? '' : 'display: none;' }}">
                                <label for="data_inicio_{{ $tipos->id }}" class="form-label small mb-0">{{ $tipos->tipo }}</label>
                                <input type="date" class="form-control form-control-sm date-input ms-2" name="datas_manifestou[{{ $tipos->id }}]" value="{{ $dateValue }}" placeholder="Data">
                            </div>
                        @endforeach
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
</div>

<!-- Adicione este script no final do seu HTML ou em uma seção de scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        $('.mediunidade-checkbox').change(function () {
            $('.data_manifestou').hide();
            $('.mediunidade-checkbox:checked').each(function () {
                var dataIndex = $(this).val();
                $('#data_inicio_' + dataIndex).show();
            });
        });

        // Mostra automaticamente as datas das mediunidades já criadas
        $('.mediunidade-checkbox:checked').change();
    });
</script>

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
