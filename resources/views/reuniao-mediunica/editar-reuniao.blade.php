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
                        <div class="row">
                            <div class="col">
                                Grupo
                                <select class="form-select slct" name="grupo" required>
                                    @foreach($grupo as $grupos)
                                        <option value="{{$grupos->idg}}" {{$grupos->nome == $info->nome ? 'selected' : ''}}>{{$grupos->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                Tipo de Tratamento
                                <select class="form-select slct" name="tratamento" required>
                                    @foreach($tratamento as $tratamentos)
                                        <option value="{{$tratamentos->idt}}" {{$tratamentos->descricao == $info->descricao ? 'selected' : ''}}>{{$tratamentos->descricao}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row mt-3">
                            <div class="col">
                                Dia da semana
                                <select class="form-select slct" name="dia" required>
                                    @foreach($dia as $dias)
                                        <option value="{{$dias->idd}}" {{$dias->nome == $info->dia ? 'selected' : ''}}>{{$dias->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                Max atendimentos
                                <input type="number" class="form-control" min="1" max="800" name="max_atend" required value="{{ $info->max_atend }}">
                            </div>
                            <div class="col">
                                Hora de início
                                <input class="form-control" type="time" name="h_inicio" required value="{{ $info->h_inicio }}">
                            </div>
                            <div class="col">
                                Hora de fim
                                <input class="form-control" type="time" name="h_fim" required value="{{ $info->h_fim }}">
                            </div>
                        </div>
                        <br>
                        <div class="row mt-3">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Sala</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="id_sala" class="form-label">Número</label>
                                                <select class="form-select" id="id_sala" name="id_sala">
                                                    <option value=""></option>
                                                    @foreach ($salas as $sala)
                                                        <option value="{{ $sala->id }}" 
                                                            data-nome="{{ $sala->nome }}" 
                                                            data-numero="{{ $sala->numero }}" 
                                                            data-localizacao="{{ $sala->nome_localizacao }}"
                                                            {{ $sala->id == $info->id_sala ? 'selected' : '' }}>
                                                            {{ $sala->numero }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="nome" class="form-label">Nome</label>
                                                <input type="text" class="form-control" id="nome" name="nome" value="{{ $info->sala }}" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="localizacao" class="form-label">Localização</label>
                                                <input type="text" class="form-control" id="localizacao" name="localizacao" value="{{ $info->nome_localizacao }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                            document.getElementById('id_sala').addEventListener('change', function() {
                                var selectedOption = this.options[this.selectedIndex];
                                document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
                                document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
                            });
                        
                            // Trigger change event to populate the fields on page load
                            document.getElementById('id_sala').dispatchEvent(new Event('change'));
                        </script>
                        
                        <br>
                        <div class="row mt-3">
                            <div class="col-4 d-grid gap-1 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-reunioes" role="button">Cancelar</a>
                            </div>
                            <div class="col-4 d-grid gap-2 mx-auto">
                                <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <br/>
            </div>
        </div>
    </div>
</div>
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
