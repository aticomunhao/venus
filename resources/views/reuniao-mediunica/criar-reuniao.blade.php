@extends('layouts.app')

@section('title')
   Cadastrar Reunião
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
                                CADASTRAR REUNIÃO
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="post" action="/nova-reuniao">
                            @csrf
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="grupo" class="form-label">Grupo</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select select2" id="grupo" name="grupo" required>
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->idg }}">{{ $grupos->nome }} - {{ $grupos->nsigla }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="tratamento" class="form-label">Tipo de Trabalho</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select slct" id="tratamento" name="tratamento" required>
                                        @foreach ($tratamento as $tratamentos)
                                            <option value="{{ $tratamentos->idt }}">{{ $tratamentos->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="row mt-3">
                                <div class="col">
                                    <label for="dia" class="form-label">Dia da semana <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span></label>
                                    <select class="form-select slct" id="dia" name="dia" required>
                                        @foreach ($dia as $dias)
                                            <option value="{{ $dias->idd }}">{{ $dias->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="h_inicio" class="form-label">Hora de início</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="time" id="h_inicio" name="h_inicio" required>
                                </div>
                                <div class="col-2">
                                    <label for="h_fim" class="form-label">Hora de fim</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="time" id="h_fim" name="h_fim" required>
                                </div>
                                <div class="col-4">
                                    <label for="h_fim" class="form-label">Observação</label>
                                    <select class="form-select slct" id="observacao" name="observacao">
                                        <option></option>
                                        @foreach ($observacao as $obs)
                                            <option value="{{ $obs->id }}">{{ $obs->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="max_atend" class="form-label">Max atendimentos</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input type="number" class="form-control" id="max_atend" min="1" max="800"
                                        name="max_atend" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Data Inicio</label>

                                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio">
                                </div>
                                <div class="col">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="dt_fim" min="1" max="800"
                                        name="dt_fim">
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
                                <label for="id_sala" class="form-label">Número</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" id="id_sala" name="id_sala">
                                    <option value=""></option>
                                    @foreach ($salas as $sala)
                                        <option value="{{ $sala->id }}" data-nome="{{ $sala->nome }}"
                                            data-numero="{{ $sala->numero }}"
                                            data-localizacao="{{ $sala->nome_localizacao }}">
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

  

    <script>
        $(document).ready(function(){
            function disab(){
                let grupo = $('#grupo').prop('selectedIndex');

                let array = @json($grupo);
                array = array[grupo]

                if (array.id_tipo_grupo != 3) {
                    $('#max_atend').prop("disabled", false)
                    $('#tratamento').prop("disabled", false)
                    $('.sumir').prop('hidden', false)



                } else {
                    bufferM = $('#max_atend').val()
                    bufferT = $('#tratamento').prop("selectedIndex")
                    $('#max_atend').prop("disabled", true)
                    $('#max_atend').val('')
                    $('#tratamento').prop("disabled", true)
                    $('#tratamento').prop("selectedIndex", -1)
                    $('.sumir').prop('hidden', true)
                }
            };

            disab();
            $('#grupo').change(function() {
               disab()
            })
        })

    </script>
    <script>
        document.getElementById('id_sala').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
        });


 
    </script>
@endsection

@section('footerScript')
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
