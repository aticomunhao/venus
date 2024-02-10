@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">CRIAR ENTREVISTA</div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/criar-entrevista/{{ $encaminhamento->id }}">
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
                        <label for="entrevista" class="form-label">Entrevista</label>
                        <input type="text" class="form-control" id="entrevista" name="entrevista" >
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <label for="quantidade_pessoas" class="form-label">Quantidade de Pessoas</label>
                        <input type="number" class="form-control" id="quantidade_pessoas" name="quantidade_pessoas" >
                    </div>
                    <div class="col">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade">
                    </div>
                    <div class="col">
                        <label for="sexo" class="form-label">Sexo</label>
                        <select class="form-select" id="sexo" name="sexo">
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="idade" class="form-label">Idade</label>
                        <input type="number" class="form-control" id="idade" name="idade" >
                    </div>
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

