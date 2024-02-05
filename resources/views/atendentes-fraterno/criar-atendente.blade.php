@extends('layouts.app')

@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        CADASTRAR ATENDENTE
                    </div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="{{ route('cadastrar') }}">
                    @csrf
                    <div class="row">
                        <div class="col">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar nome..."
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
                            <label for="id_grupo" class="form-label">Nome grupo</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_grupo[]" id="id_grupo">
                                @foreach ($grupo as $grupos)
                                    <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="row mt-4">
                            <div class="col" id="additional-group-fields">

                            </div>
                        </div>

                        <div class="col">
                            <div class="form-check custom-checkbox">
                                <input type="checkbox" class="form-check-input" id="multiple_groups_checkbox">
                                <label class="form-check-label" for="multiple_groups_checkbox">
                                    Adicionar mais grupos ao atendente
                                </label>
                            </div>
                        </div>
                        <style>
                            .custom-checkbox input[type="checkbox"] {
                                border: 2px solid #000;
                                width: 17px;
                                height: 17px;
                                cursor: pointer;
                            }
                        </style>


                        <div class="col" id="group_selection" style="display: none;">
                            <label for="selected_groups" class="form-label">Informe a quantidade de grupos:</label>
                            <select class="form-select" name="selected_groups" id="selected_groups">

                                @for ($i = 0; $i <= 20; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <br>


                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


                    <script>
                        $(document).ready(function() {
                            $('#multiple_groups_checkbox').on('change', function() {
                                var isChecked = $(this).prop('checked');
                                $('#group_selection').toggle(isChecked);

                                if (!isChecked) {

                                    $('#additional-group-fields').empty();
                                }
                            });

                            $('#selected_groups').on('change', function() {
                                updateAdditionalFields();
                            });

                            function updateAdditionalFields() {

                                $('#additional-group-fields').empty();


                                var selectedQuantity = parseInt($('#selected_groups').val());
                                for (var i = 0; i < selectedQuantity; i++) {
                                    var newField = $('#additional-group-field-template').clone();
                                    newField.attr('id', 'additional-group-field-');
                                    newField.find('.form-select').attr('name', 'additional_id_grupo[' + i + ']');
                                    $('#additional-group-fields').append(newField.show());
                                }
                            }
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

                    <div id="additional-group-field-template" class="col" style="display: none;">
                        <label for="additional_id_grupo" class="form-label">Nome grupo adicional</label>
                        <select class="form-select" aria-label=".form-select-lg example">
                            <option value=""></option>
                            @foreach ($grupo as $grupos)
                                <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <br>

                    <div class="row mt-4 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
