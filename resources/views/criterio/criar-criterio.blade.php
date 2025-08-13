@extends('layouts.app')
@section('content')
    <br>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                Criar Critério
            </div>
            <div class="card-body">
                {{-- <h5 class="card-title">Special title treatment</h5> --}}
                <p class="card-text">
                <div class="row justify-content-around">
                    <div class="col-md-3 col-sm-12">
                        <label for="idsetor" class="form-label">Setor: </label>
                        <select class="form-select select2" id="idsetor" name="setor" required>
                            @foreach ($setores as $setor)
                                <option value="{{ $setor->ids }}">{{ $setor->nome }} - {{ $setor->sigla }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <label for="idatividade" class="form-label">Atividade: </label>
                        <select class="form-select select2" id="idatividade" name="atividade" required>
                            @foreach ($tipos_tratamentos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->descricao }} - {{ $tipo->sigla }}
                                    {{ $tipo->id_semestre ? ' - ' . $tipo->id_semestre : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                </p>

            </div>
        </div>
    </div>
@endsection
