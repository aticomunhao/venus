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
            <form class="form-horizontal mt-2" method="post" action="/agendar-entrevista/{{ $encaminhamento->id }}">
                @csrf

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <select class="form-control" id="id_pessoa" name="id_pessoa" disabled>
                            @foreach ($informacoes as $informacao)
                                <option value="{{ $informacao->id_pessoa }}">{{ $informacao->nome_pessoa }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_tipo_tratamento" class="form-label">Tratamento</label>
                        <select class="form-select" id="id_tipo_tratamento" name="id_tipo_tratamento" >
                            @foreach ($informacoes as $informacao)
                                <option value="{{ $informacao->id_tipo_tratamento }}">{{ $informacao->tratamento_descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                    <br>
                    Data atendimento
                    <input type="date" class="form-control" id="h_inicio" name="data_inicio" value="{{ $entrevista[0]->data }}">
                </div>
                <div class="col">
                    <br>
                   Horario atendimento
                    <input type="time" class="form-control" id="h_fim" name="data_fim" value="{{ $entrevista->horario }}">
                </div>

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
    </div>
</div>
@endsection

