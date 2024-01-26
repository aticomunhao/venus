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
                        CADASTRAR MÉDIUM
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="{{ route('medium.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <input type="text" class="form-select" id="searchInput" placeholder="Pesquisar nome..."
                                value="{{ old('nome_completo') }}">
                            <ul id="pessoaList" class="list-group" style="display: none;">
                                @foreach ($pessoas as $pessoa)
                                    <li class="list-group-item" data-id="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="id_pessoa" id="selectedId" value="">
                        </div>

                        <div class="col">
                            <label for="tipo_status_pessoa" class="form-label">Status</label>
                            <select class="form-control" aria-label=".form-select-lg example"
                                name="tipo_status_pessoa" >
                                @foreach ($tipo_status_pessoa as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->tipos }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="id_setor" class="form-label">Setor</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_setor">
                                @foreach ($setor as $setores)
                                    <option value="{{ $setores->ids }}">{{ $setores->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="id_funcao" class="form-label">Função</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_funcao">
                                @foreach ($tipo_funcao as $funcao)
                                    <option value="{{ $funcao->idf }}">{{ $funcao->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="id_grupo" class="form-label">Nome grupo</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_grupo">
                                @foreach ($grupo as $grupos)
                                    <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="id_mediunidade" class="form-label"></label>
                            <div class="table-responsive">
                                <div class="table">
                                    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                        <thead>
                                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                <th scope="col"></th>
                                                <th scope="col">Tipo de Mediunidade</th>
                                                <th scope="col">Data que manifestou</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tipo_mediunidade as $tipo)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input" type="checkbox" name="id_tp_mediunidade[]"
                                                            value="{{ $tipo->id }}">
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $tipo->tipo }}
                                                    </td>
                                                    <td>
                                                        <div class="form-group data_manifestou" name="id_mediunidade_medium"
                                                            id="data_inicio_{{ $tipo->id }}">
                                                            <input type="hidden" name="id_medium" value="{{ $id_medium }}">
                                                            @if (old("data_inicio.$tipo->id"))
                                                                @foreach (old("data_inicio.$tipo->id") as $oldDate)
                                                                    <input type="date" class="form-control form-control-sm"
                                                                        name="data_inicio[{{ $tipo->id }}][]"
                                                                        value="{{ $oldDate }}" required="required">
                                                                @endforeach
                                                            @else
                                                                <input type="date" class="form-control form-control-sm"
                                                                    name="data_inicio[{{ $tipo->id }}][]" value=""
                                                                    required="required">
                                                            @endif
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
                        /* Estilo mais visível e maior para o checkbox */
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
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Adicione antes do fechamento da tag </body> -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkboxes = document.querySelectorAll('input[name="id_tp_mediunidade[]"]');
        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var tipoId = this.value;
                var dataInput = document.querySelector('#data_inicio_' + tipoId + ' input[type="date"]');

                if (!this.checked) {
                    // Limpar a data e ocultar o campo se o checkbox for desmarcado
                    dataInput.value = '';
                    dataInput.parentElement.style.display = 'none';
                } else {
                    // Exibir o campo de data se o checkbox for marcado
                    dataInput.parentElement.style.display = '';
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
        jQuery(document).ready(function() {
            jQuery('.lista').select2({
                height: '150%',
                width: "100%",
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.data_manifestou')
                .hide()
                .find('input[type=date]')
                .prop('required', false);

            $('[name^=id_tp_mediunidade]').change(function() {
                $('.data_manifestou')
                    .hide()
                    .find('input[type=date]')
                    .prop('required', false);

                $('[name^=id_tp_mediunidade]:checked').each(function() {
                    var tipoId = $(this).val();
                    $('#data_inicio_' + tipoId)
                        .show()
                        .find('input[type=date]')
                        .prop('required', true);
                });
            });
        });
    </script>
@endsection
