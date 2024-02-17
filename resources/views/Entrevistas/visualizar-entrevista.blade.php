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
                <form class="form-horizontal mt-2" method="post" action="/visualizar-entrevista/{{ $entrevistas[0]->id }}">
                    @csrf

                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_encaminhamento" class="form-label">Nome</label>
                            <select class="form-control" id="id_encaminhamento" name="id_encaminhamento" disabled>
                                <option value="{{ $entrevistas[0]->id}}">{{ $entrevistas[0]->id}}</option>
                                @foreach ($pessoas as $pessoa)
                                <option value="{{ $pessoa->id}}">{{ $pessoa->nome_completo }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_entrevistador" class="form-label">Entrevistador</label>
                            <select class="form-control" id="id_entrevistador" name="id_entrevistador" disabled>

                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->id}}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- <div class="row mb-5">
                        <div class="col">
                            <label for="id_sala" class="form-label">Sala</label>
                            <select class="form-select" id="id_sala" name="id_sala">
                                <option value=""></option>
                                @foreach ($salas as $sala)
                                    <option value="{{ $sala->id }}" @if($entrevista && $entrevista->id_sala == $sala->id) selected @endif>{{ $sala->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="numero" name="numero" value="{{ $entrevista ? $entrevista->numero : '' }}" readonly>
                        </div>
                        <div class="col">
                            <label for="localizacao" class="form-label">Localização</label>
                            <input type="text" class="form-control" id="localizacao" name="localizacao" value="{{ $entrevista ? $entrevista->localizacao : '' }}" readonly>
                        </div>
                    </div> --}}

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
