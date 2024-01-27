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
                        <div class="col-1">
                            <label for="id_pessoa" class="form-label">Id</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->idp }}">{{ $pessoa->idp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
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
                                    Selecione mais de um grupo
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
                            <label for="selected_groups" class="form-label">Selecione a quantidade de grupos:</label>
                            <select class="form-select" name="selected_groups" id="selected_groups">

                                @for ($i = 1; $i <= 12; $i++)
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
