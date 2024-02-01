@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        EDITAR ATENDENTE
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
                        <div class="row mt-4">
                            <div class="col">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="{{ $atendente->id }}">{{ $atendente->tipo }}</option>
                                    @foreach ($tipo_status_pessoa as $status)
                                        <option value="{{ $status->id }}">{{ $status->tipo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <label for="data_fim" class="form-label">Data fim</label>
                                <input type="date" class="form-select" id="data_fim" name="data_fim" value="{{ $atendente->dt_fim }}">
                            </div>
                             <div class="col">
                                <label for="motivo_status" class="form-label">Motivo </label>
                                <select class="form-select" aria-label=".form-select-lg example" name="motivo_status" id="motivo_status"
                                    required="required">
                                    <option value="" {{ is_null($atendente->motivo_status) ? 'selected' : '' }}></option>
                                    @foreach ($tipo_motivo_status_pessoa as $motivo)
                                        <option value="{{ $motivo->id }}" {{ $atendente->motivo_status == $motivo->id ? 'selected' : '' }}>
                                            {{ $motivo->motivo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nome grupo</th>
                                            <th>Adicionar grupos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                @foreach ($gruposAtendente as $grupoAtendente)
                                                    <div class="mb-3">
                                                        <select class="form-select" name="id_grupo[]">
                                                            @foreach ($grupo as $grupos)
                                                                <option value="{{ $grupos->id }}"
                                                                    @if ($grupos->id == $grupoAtendente->id_grupo) selected @endif>
                                                                    {{ $grupos->nome }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>

                                                <div id="gruposAdicionais" style="display: none;">
                                                    <div class="col" id="group_selection" style="display: none;">
                                                        <label for="selected_groups" class="form-label">Selecione a quantidade de grupos:</label>
                                                        <select class="form-select" name="selected_groups" id="selected_groups">

                                                            <option value="1">1</option>
                                                        </select>
                                                    </div>

                                                    <div id="grupoAdicionalContainer">

                                                        <div class="mb-3 grupo-adicional" style="display: none;">
                                                            <label for="novo_grupo" class="form-label">Novo Grupo</label>
                                                            <select class="form-select" name="novo_grupo[]">
                                                                <option> </option>
                                                                @foreach ($grupo as $grupos)
                                                                    <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-sm btn-success adicionar-mais" data-toggle="tooltip" data-placement="top" title="Adicionar Mais">+</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <input type="checkbox" id="adicionarMaisGrupos" name="adicionarMaisGrupos">


                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <style>
                            .table th input[type="checkbox"],
                            .table td input[type="checkbox"] {
                                width: 17px;
                                height: 17px;
                                cursor: pointer;
                                border: 2px solid #000;
                            }
                            </style>
                        <div class="row mt-4 justify-content-center">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                            </div>
                            <div class="d-grid gap-2 col-4 mx-auto">
                                <button type="submit" class="btn btn-primary">Confirmar</button>
                            </div>
                              <!-- Adicione o jQuery à sua página se ainda não estiver presente -->
                              <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

                              <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

                                <script>
                                    $(document).ready(function () {
                                        // Função para verificar e atualizar o estado dos campos
                                        function atualizarCampos() {
                                            var atendenteId = "{{ $atendente->id }}";
                                            var statusSelecionado = $("select[name='status']").val();

                                            // Verificar se o status é ativo
                                            if (statusSelecionado == atendenteId) {
                                                $("#data_fim, #motivo_status").prop('disabled', true);
                                            } else {
                                                $("#data_fim, #motivo_status").prop('disabled', false);
                                            }
                                        }

                                        // Adicionar um ouvinte de alteração ao campo de status
                                        $("select[name='status']").change(function () {
                                            atualizarCampos();
                                        });

                                        // Chamar a função quando a página for carregada
                                        atualizarCampos();
                                    });
                                </script>

                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                            <script>
                                $(document).ready(function () {
                                    $('#adicionarMaisGrupos').on('change', function () {
                                        var isChecked = $(this).prop('checked');
                                        $('#gruposAdicionais').toggle(isChecked);

                                        if (isChecked) {
                                            // Mostra o primeiro campo "Novo Grupo" e esconde os demais
                                            $('.grupo-adicional:first').show();
                                            $('.grupo-adicional:not(:first)').hide();
                                        } else {
                                            // Remove todos os elementos exceto o primeiro
                                            $('.grupo-adicional:not(:first)').remove();
                                        }
                                    });

                                    // Adiciona mais campos "Novo Grupo" dinamicamente ao clicar no botão de "+"
                                    $('#grupoAdicionalContainer').on('click', '.adicionar-mais', function () {
                                        var novoGrupoClone = $('.grupo-adicional:last').clone();
                                        novoGrupoClone.find('select[name="novo_grupo[]"]').val('');
                                        $('#grupoAdicionalContainer').append(novoGrupoClone);
                                    });
                                });
                            </script>
                             <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var dataFimInput = document.getElementById('data_fim');
                                    var motivoSelect = document.getElementById('motivo_status');


                                    dataFimInput.addEventListener('change', function() {

                                        motivoSelect.disabled = false;
                                    });
                                });
                            </script>

                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
