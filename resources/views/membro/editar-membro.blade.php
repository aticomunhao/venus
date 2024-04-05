@extends('layouts.app')

@section('content')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="/venus/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        EDITAR MEMBRO
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-membro/{{ $membro->idm }}"
                    id="mediumForm">
                    @csrf

                    <div class="row mt-3">
                        <div class="col-4">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="searchInput" disabled
                                placeholder="Pesquisar nome..." value="{{ $membro->nome_completo }}">
                            <ul id="pessoaList" class="list-group" style="display: none;">
                                @foreach ($pessoas as $pessoa)
                                    <li class="list-group-item" data-id="{{ $pessoa->id }}">{{ $pessoa->nome_completo }}
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="id_pessoa" id="selectedId" value="{{ $membro->id_pessoa }}">
                        </div>
                        <div class="col">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="status" id="idstatus"
                                required="required">
                                @foreach ($tipo_status_pessoa as $status)
                                    <option value="{{ $status->id }}"
                                        {{ $medium->status == $status->id ? 'selected' : '' }}>
                                        {{ $status->tipos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="motivo_status" class="form-label">Motivo status</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="motivo_status"
                                id="motivo_status" required="required">
                                @foreach ($tipo_motivo_status_pessoa as $motivo)
                                    <option value="{{ $motivo->id }}"
                                        {{ $medium->motivo_status == $motivo->id ? 'selected' : '' }}>
                                        {{ $motivo->motivo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                            <div class="col">
                                <label for="id_funcao" class="form-label">Função</label>
                                <select class="form-select" aria-label=".form-select-lg example" name="id_funcao">
                                    <option value="{{ $medium->id_funcao }}">{{ $medium->nome_funcao }}</option>
                                    @foreach ($tipo_funcao as $funcao)
                                        <option value="{{ $funcao->id }}">{{ $funcao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <label for="id_grupo" class="form-label">Nome grupo</label>
                                <select class="form-select" aria-label=".form-select-lg example" name="id_grupo">
                                    <option value="{{ $medium->id_grupo }}">{{ $medium->nome_grupo }}</option>
                                    @foreach ($grupo as $grupos)
                                        <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <?php $a = 1; ?>

                        <div class="row mt-3">
                            <div class="col">
                                <label for="id_mediunidade" class="form-label"></label>
                                <div class="table-responsive">
                                    <div class="table">
                                        <table
                                            class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                            <thead>
                                                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                    <th scope="col">
                                                         {{-- <input type="checkbox" id="toggleAll"
                                                            onclick="toggleCheckboxes(this)">  --}}
                                                    </th>
                                                    <th scope="col">Mediunidade</th>
                                                    <th scope="col">Data de Manifestação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tipo_mediunidade as $tipos)
                                                    <tr class="{{ $a % 2 == 0 ? 'table-row-gray' : 'table-row-white' }}">
                                                        <td>
                                                            <?php
                                                            $isChecked = in_array($tipos->id, $createdMediunidadeIds) ? 'checked' : '';
                                                            ?>
                                                            <input class="form-check-input mediunidade-checkbox"
                                                                type="checkbox" name="mediunidades[]"
                                                                value="{{ $tipos->id }}"
                                                                id="mediunidade_{{ $a++ }}" {{ $isChecked }}>
                                                        </td>
                                                        <td class="text-center">
                                                            <label class="form-check-label"
                                                                for="mediunidade_{{ $a }}">
                                                                {{ $tipos->tipo }}
                                                            </label>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                            $dateValue = in_array($tipos->id, $createdMediunidadeIds) ? $createdMediunidadeData[$tipos->id] : '';
                                                            ?>
                                                            <div class="form-group data_manifestou"
                                                                name="id_mediunidade_medium"
                                                                id="data_inicio_{{ $tipos->id }}"
                                                                style="{{ $dateValue ? '' : 'display: none;' }}">
                                                                <input type="date"
                                                                    class="form-control form-control-sm date-input"
                                                                    name="datas_manifestou[{{ $tipos->id }}]"
                                                                    value="{{ $dateValue }}" placeholder="Data">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>

                            .table th input[type="checkbox"],
                            .table td input[type="checkbox"] {
                                width: 17px; /* Ajusta a largura do checkbox */
                                height: 17px; /* Ajusta a altura do checkbox */
                                cursor: pointer; /* Adiciona o cursor de ponteiro ao passar sobre o checkbox */
                                border: 2px solid #000; /* Adiciona borda preta ao checkbox */
                            }



                        </style>
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
    <!-- Adicione antes do fechamento da tag </body> -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkboxes = document.querySelectorAll('.mediunidade-checkbox');

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var tipoId = this.value;
                var dataInput = document.querySelector('#data_inicio_' + tipoId + ' .date-input');

                if (!this.checked) {
                    // Limpar a data se o checkbox for desmarcado
                    dataInput.value = '';
                    dataInput.parentElement.style.display = 'none'; // Oculta o campo de data
                } else {
                    dataInput.parentElement.style.display = ''; // Exibe o campo de data se o checkbox for marcado
                }
            });
        });
    });
</script>

    <script>
        // Função para filtrar a lista com base na entrada do usuário
        function filterList() {
            var input, filter, ul, li, a, i, id;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            ul = document.getElementById('pessoaList');
            li = ul.getElementsByTagName('li');

            // Exibe a lista somente quando o usuário começar a digitar
            ul.style.display = (filter === "") ? 'none' : 'block';

            for (i = 0; i < li.length; i++) {
                a = li[i];
                id = a.getAttribute('data-id');
                if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = '';
                } else {
                    li[i].style.display = 'none';
                }
            }
        }

        // Adiciona um ouvinte de evento ao campo de pesquisa
        document.getElementById('searchInput').addEventListener('input', filterList);

        // Adiciona um ouvinte de evento às opções da lista
        var listItems = document.getElementById('pessoaList').getElementsByTagName('li');
        for (var i = 0; i < listItems.length; i++) {
            listItems[i].addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var nome = this.innerHTML;
                document.getElementById('searchInput').value = nome;
                document.getElementById('selectedId').value = id;
                // Oculta a lista após a seleção de um item
                document.getElementById('pessoaList').style.display = 'none';
            });
        }
    </script>

    <script>
        function toggleCheckboxes(checkbox) {
            var checkboxes = document.getElementsByClassName('mediunidade-checkbox');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = checkbox.checked;
            }
        }
    </script>

    <!-- Adicione este script no final do seu HTML ou em uma seção de scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.mediunidade-checkbox').change(function() {
                $('.data_manifestou').hide();
                $('.mediunidade-checkbox:checked').each(function() {
                    var dataIndex = $(this).val();
                    $('#data_inicio_' + dataIndex).show();
                });
            });

            // Mostra automaticamente as datas das mediunidades já criadas
            $('.mediunidade-checkbox:checked').change();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var statusSelect = document.getElementById('idstatus');
            var motivoStatusSelect = document.getElementById('motivo_status');

            // Adiciona um ouvinte de evento ao campo de status
            statusSelect.addEventListener('change', function() {
                // Se o status for "ativo", desabilita o campo de motivo_status
                motivoStatusSelect.disabled = statusSelect.value === '1';
                // Se desabilitado, seleciona a opção vazia
                if (motivoStatusSelect.disabled) {
                    motivoStatusSelect.value = '';
                }
            });

            // Dispara o evento inicialmente para configurar o estado inicial
            statusSelect.dispatchEvent(new Event('change'));
        });
    </script>
@endsection
