@extends('layouts.app')
@section('content')
    <br>
    <div class="container">
        <div class="card" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            <div class="card-header">
                Criar Critério de Atividade
            </div>
            <div class="card-body">

                {{-- Formulário de Criação --}}
                <form action="{{ route('incluir.tipo_criterio_controller') }}" method="POST">
                    @csrf
                    <div class="row justify-content-around">
                        <div class="col-md-3 col-sm-12">
                            <label for="nome" class="form-label">Nome do Critério</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label for="tipo" class="form-label">Tipo de Critério</label>
                            <select class="form-select" id="tipo" name="tipo_criterio" required>
                                @foreach ($tipo_valores as $valor)
                                    <option value="{{ $valor }}">{{ $valor }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row justify-content-around">
                        <div class="col-md-3">
                            <a href="{{ route('index.tipo_criterio_controller') }}" class="btn btn-danger"
                                style="width: 100%">Cancelar</a>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success" style="width: 100%">Criar Critério</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
