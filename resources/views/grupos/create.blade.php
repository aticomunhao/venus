@extends('layouts.main')

@section('content')


<h1>Incluir Novo Grupo </h1>

<form action="incluir-grupo" method="POST">
  @csrf
    <label for="nome">Nome</label>
    <input type="text" name="nome" class="form-control">

    <label for="id_dia_semana">Dia da semana</label>
    <select name="id_dia_semana" class="form-control">
        <option value="">Selecione</option>
        @foreach($dia_semana as $id => $dia)
            <option value="{{ $id }}">{{ $dia }}</option>
        @endforeach
    </select>

    <label for="hr_inicio">Hora de início</label>
    <input type="time" name="hr_inicio" class="form-control">

    <label for="hr_fim">Hora de término</label>
    <input type="time" name="hr_fim" class="form-control">

    <label for="ativo">Ativo</label>
    <input type="checkbox" name="ativo" value="1" checked>

    <label for="nr_vagas">Número de vagas</label>
    <input type="number" name="nr_vagas" class="form-control">

    <label for="id_tipo_grupo">Tipo de grupo</label>
    <select name="id_tipo_grupo" class="form-control">
        <option value="">Selecione</option>
        @foreach($tipo_grupo as $id => $tipo)
            <option value="{{ $id }}">{{ $tipo }}</option>
        @endforeach
    </select>

    <label for="nr_trabalhadores">Número de trabalhadores</label>
    <input type="number" name="nr_trabalhadores" class="form-control">


    <label for="id_sala">Sala</label>
    <select name="id_sala" class="form-control">
        <option value="">Selecione</option>
        @foreach($salas as $id => $sala)
            <option value="{{ $id }}">{{ $sala }}</option>
        @endforeach
    </select>

    <br>

    <button type="submit" class="btn btn-primary float-end">Salvar</button>
</form>



{{--
<div class="mb-3">
    <label for="nome" class="form-label">Nome</label>
    <input type="text" class="form-control" id="nome" name="nome" placeholder="">
  </div>
  <div class="mb-3">
    <label for="id_dia_semana" class="form-label">Qual dia da Semana?</label>
    <input type="text" class="form-control" id="id_dia_semana" >
  </div>
  <div class="mb-3">
    <label for="hr_inicio" class="form-label">Hora Inicio</label>
    <input type="text" class="form-control" id="hr_inicio">
  </div>
  <div class="mb-3">
    <label for="hr_fim" class="form-label"></label>
    <input type="text" class="form-control" id="hr_fim" class="hr_fim">
  </div> --}}





@endsection


