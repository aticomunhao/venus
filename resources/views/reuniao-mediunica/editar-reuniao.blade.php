@extends('layouts.app')

@section('head')
    <title>Editar Reunião Mediúnica</title>
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                Editar Reunião
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="post" action="/atualizar-reuniao/{{ $info->id }}">
                            @csrf
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="grupo" class="form-label">Grupo<span style="color:red" data-tt="tooltip" data-placement="top"
                                        title="Obrigatório">*</span></label>
                                    <select class="form-select slct" id="grupo" name="grupo" required>
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->idg }}" {{$grupos->nome == $info->nome ? 'selected' : ''}}>{{ $grupos->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="tratamento" class="form-label">Tipo de Tratamento<span
                                            style="color:red" data-tt="tooltip" data-placement="top"
                                            title="Obrigatório" class="sumir">*</span></label>
                                    <select class="form-select slct" id="tratamento" name="tratamento" required>
                                        @foreach ($tratamento as $tratamentos)
                                            <option value="{{ $tratamentos->idt }}" {{$tratamentos->descricao == $info->descricao ? 'selected' : ''}}>{{ $tratamentos->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="row mt-3">
                                <div class="col">
                                    <label for="dia" class="form-label">Dia da semana<span
                                            style="color:red" data-tt="tooltip" data-placement="top"
                                            title="Obrigatório">*</span></label>
                                    <select class="form-select slct" id="dia" name="dia" required>
                                        @foreach ($dia as $dias)
                                            <option value="{{ $dias->idd }}" {{$dias->nome == $info->dia ? 'selected' : ''}} >{{ $dias->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="h_inicio" class="form-label">Hora de início<span
                                            style="color:red" data-tt="tooltip" data-placement="top"
                                            title="Obrigatório">*</span></label>
                                    <input class="form-control" type="time" id="h_inicio" name="h_inicio" required value="{{ $info->h_inicio }}">
                                </div>
                                <div class="col">
                                    <label for="h_fim" class="form-label">Hora de fim<span
                                            style="color:red" data-tt="tooltip" data-placement="top"
                                            title="Obrigatório">*</span></label>
                                    <input class="form-control" type="time" id="h_fim" name="h_fim" required value="{{ $info->h_fim }}">
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="max_atend" class="form-label">Max atendimentos<span
                                            style="color:red" data-tt="tooltip" data-placement="top"
                                            title="Obrigatório" class="sumir">*</span></label>
                                    <input type="number" class="form-control" id="max_atend" min="1" max="800"
                                        name="max_atend" value="{{ $info->max_atend }}" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Data Inicio</label>
                                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ $info->data_inicio }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="dt_fim" min="1" max="800"
                                        name="dt_fim" value="{{ $info->data_fim }}">
                                </div>
                            </div>
                            <br />
                    </div>
                </div>
                <br />
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Sala</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="id_sala" class="form-label">Número<span style="color:red" data-tt="tooltip" data-placement="top"
                                    title="Obrigatório">*</span></label>
                                <select class="form-select" id="id_sala" name="id_sala">
                                    <option value=""></option>
                                    @foreach ($salas as $sala)
                                        <option value="{{ $sala->id }}" data-nome="{{ $sala->nome }}"
                                            data-numero="{{ $sala->numero }}"
                                            data-localizacao="{{ $sala->nome_localizacao }}" {{ $sala->id == $info->id_sala ? 'selected' : '' }}>
                                            {{ $sala->numero }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao" readonly>


                            </div>
                            <div class="row mt-5">
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <a class="btn btn-danger" href="/gerenciar-reunioes" role="button">Cancelar</a>
                                </div>
                                <div class="d-grid gap-2 col-4 mx-auto">
                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $('#max_atend').prop("disabled", false)
        $('#tratamento').prop("disabled", false)
        $('#grupo').change(function() {
            let grupo = $('#grupo').prop('selectedIndex');


            let array = @json($grupo);
            array = array[grupo]

            if (array.id_tipo_grupo == 3) {
                $('#max_atend').prop("disabled", false)
                $('#tratamento').prop("disabled", false)
                $('.sumir').prop('hidden', false)



            } else {
                bufferM = $('#max_atend').val()
                bufferT = $('#tratamento').prop("selectedIndex")
                console.log(bufferM, bufferT)
                $('#max_atend').prop("disabled", true)
                $('#max_atend').val('')
                $('#tratamento').prop("disabled", true)
                $('#tratamento').prop("selectedIndex", -1)
                $('.sumir').prop('hidden', true)
            }

        })
    </script>

    <script>
        $( document ).ready(function() {

                var selectedOption = document.getElementById('id_sala')
        selectedOption = selectedOption.options[selectedOption.selectedIndex];
        document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
        document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');


        document.getElementById('id_sala').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
        });


        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        })
    </script>
@endsection

@section('footerScript')
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
