@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">AGENDAR ENTREVISTADOR</div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/incluir-entrevistador/{{ $encaminhamento->id }}">
                @csrf

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_encaminhamento" class="form-label">Nome</label>
                        <select class="form-control" id="id_encaminhamento" name="id_encaminhamento" disabled>
                            <option value="{{ $encaminhamento->id }}">{{ $entrevistas->nome_completo }}</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_entrevistador" class="form-label">Entrevistador</label>
                        <select class="form-select" id="id_entrevistador" name="id_entrevistador">
                            <option value=""></option>
                            @foreach ($pessoas as $pessoa)
                                <option value="{{ $pessoa->id }}">{{ $pessoa->nome_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_sala" class="form-label">Sala</label>
                        <select class="form-control" id="id_sala" name="id_sala" disabled>
                            <option >{{ $entrevistas->nome }}</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero" value="{{ $entrevistas ? $entrevistas->numero : '' }}" readonly disabled>
                    </div>
                    <div class="col">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" value="{{ $entrevistas ? $entrevistas->local : '' }}" readonly disabled>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" name="data"  value="{{ $entrevistas->data }}"  disabled>
                    </div>
                    <div class="col">
                        <label for="hora" class="form-label">Hora</label>
                        <input type="time" class="form-control" id="hora" name="hora" value="{{ $entrevistas->hora }}" disabled>
                    </div>
                </div>
                <br>

                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Cancelar</a>
                    </div>
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <button type="submit" class="btn btn-primary">Salvar Entrevista</button>
                    </div>
                </div>
            </form>
        </div>
        <script>
            // Obtém a data mínima no formato AAAA-MM-DD
            var dataMinima = new Date("2024-01-01").toISOString().split('T')[0];

            // Define a data mínima no campo de entrada
            document.getElementById("data").setAttribute("min", dataMinima);
        </script>
    </div>
</div>
@endsection
