@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">VISUALIZAR ENTREVISTA</div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/visualizar-entrevista/{{ $encaminhamento->id }}">
                @csrf

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_encaminhamento" class="form-label">Nome assistido</label>
                        <select class="form-control" id="id_encaminhamento" name="id_encaminhamento" disabled>
                            <option value="{{ $encaminhamento->id }}">{{ $entrevistas->nome_completo }}</option>
                        </select>
                    </div>
                </div>    
                <div class="row mb-5">
                    <div class="col">
                        <label for="id_entrevistador" class="form-label">Entrevistador</label>
                        <select class="form-control" id="id_entrevistador" name="id_entrevistador" disabled>
                            @if (!is_null($membros))
                                <option value="">{{ $membros->nome_entrevistador }}</option>
                                <option value="{{ $membros->id }}">{{ $membros->nome_entrevistador }}</option>
                            @else
                                <option value=""></option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                      Sala
                                </h5>
                            </div>
                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="numero" class="form-label">Número </label>
                                            <input type="text" class="form-control" id="numero" name="numero" value="{{ $entrevistas ? $entrevistas->numero : '' }}" readonly disabled>
                                        </div>
                                        <div class="col">
                                            <label for="nome" class="form-label">Nome </label>
                                            <input type="text" class="form-control" id="nome" name="nome" value="{{ $entrevistas ? $entrevistas->nome : '' }}" readonly disabled>
                                        </div>
                                        <div class="col">
                                            <label for="localizacao" class="form-label">Localização</label>
                                            <input type="text" class="form-control" id="localizacao" name="localizacao" value="{{ $entrevistas ? $entrevistas->local : '' }}" readonly disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
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
                <br>

                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Fechar</a>
                    </div>
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
