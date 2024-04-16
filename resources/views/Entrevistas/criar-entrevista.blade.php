@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">AGENDAR ENTREVISTA</div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="{{ url('/incluir-entrevista/' . $encaminhamento->id) }}">
                @csrf

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_encaminhamento" class="form-label">Nome</label>
                        <select class="form-control" id="id_encaminhamento" name="id_encaminhamento" disabled>
                            @foreach ($informacoes as $informacao)
                            <option value="{{ $informacao->id_pessoa }}">{{ $informacao->nome_pessoa }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" name="data">
                    </div>
                
                    <div class="col">
                        <label for="hora" class="form-label">Hora</label>
                        <input type="time" class="form-control" id="hora" name="hora">
                    </div>
                </div>
                <br>
                <fieldset>
                    <div class="form-group row">
                        <div class="col">
                            <div id="accordion" class="card">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">Sala</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-5">
                                        <div class="col">
                                            <label for="id_sala" class="form-label">Numero</label>
                                            <select class="form-select" id="id_sala" name="id_sala">
                                                <option value=""></option>
                                                @foreach ($salas as $sala)
                                                <option value="{{ $sala->id }}"
                                                    data-nome="{{ $sala->nome }}"
                                                    data-numero="{{ $sala->numero }}"
                                                    data-localizacao="{{ $sala->nome_localizacao }}">
                                                    {{ $sala->numero }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="numero" class="form-label">Nome</label>
                                            <input type="text" class="form-control" id="nome" name="nome" readonly>
                                        </div>
                                        <div class="col">
                                            <label for="localizacao" class="form-label">Localização</label>
                                            <input type="text" class="form-control" id="localizacao" name="localizacao" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <br>
                
                <br>
                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Cancelar</a>
                    </div>
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Obtém a data atual no formato 'YYYY-MM-DD'
    var dataAtual = new Date().toISOString().split('T')[0];

    // Define a data mínima no campo de entrada
    document.getElementById('data').setAttribute('min', dataAtual);
</script>

<script>
    // Obtém a data atual no formato 'YYYY-MM-DD'
    var dataAtual = new Date().toISOString().split('T')[0];

    // Define a data mínima no campo de entrada
    document.getElementById('data').setAttribute('min', dataAtual);
</script>

<script>
    document.getElementById('id_sala').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
        document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
    });
</script>

@endsection
