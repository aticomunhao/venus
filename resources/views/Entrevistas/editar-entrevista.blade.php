@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">EDITAR ENTREVISTA</div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/atualizar-entrevista/{{ $encaminhamento->id }}">
                @csrf

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_encaminhamento" class="form-label">Nome</label>
                        <select class="form-select" id="id_encaminhamento" name="id_encaminhamento"disabled>
                            <option value="{{ $encaminhamento->id }}">{{ $entrevistas->nome_completo }}</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_entrevistador" class="form-label">Entrevistador</label>
                        <select class="form-select" id="id_entrevistador" name="id_entrevistador" {{ is_null($pessoas) ? 'disabled' : ""}}>

                            <option value={{ !is_null($pessoas) ? $pessoas->id : ""}}>{{!is_null($pessoas) ? $pessoas->nome_completo : ""  }}</option>
                            @foreach ($entrevistador as $entrevistadores)
                            <option value="{{ $entrevistadores->id }}">{{ $entrevistadores->nome_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                 <div class="row mb-5">
                    <div class="col">
                        <label for="id_sala" class="form-label">Sala</label>
                        <select class="form-select" id="id_sala" name="id_sala">
                            <option >{{ $entrevistas->nome }}</option>
                            @foreach ($salas as $sala)
                            <option value="{{ $sala->id }}"
                                data-nome="{{ $sala->nome }}"
                                data-numero="{{ $sala->numero }}"
                                data-localizacao="{{ $sala->nome_localizacao }}">
                                {{ $sala->nome }}
                            </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-select" id="numero" name="numero" value="{{ $entrevistas ? $entrevistas->numero : '' }}" readonly >
                    </div>
                    <div class="col">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-select" id="localizacao" name="localizacao" value="{{ $entrevistas ? $entrevistas->local : '' }}" readonly >
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-select" id="data" name="data"  value="{{ $entrevistas->data }}">
                    </div>
                    <div class="col">
                        <label for="hora" class="form-label">Hora</label>
                        <input type="time" class="form-select" id="hora" name="hora" value="{{ $entrevistas->hora }}" >
                    </div>
                </div>
                <br>
                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Cancelar</a>
                    </div>
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>


            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('id_sala').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('numero').value = selectedOption.getAttribute('data-numero');
        document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
    });
</script>
@endsection
