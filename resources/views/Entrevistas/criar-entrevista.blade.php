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
                <form action="{{ route('criar-entrevista') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="assistido" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="assistido" name="assistido">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="tratamento" class="form-label">Tratamento</label>
                                <input type="text" class="form-control" id="tratamento" name="tratamento">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="entrevista" class="form-label">Entrevista</label>
                                <input type="text" class="form-control" id="entrevista" name="entrevista">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="quantidade_pessoas" class="form-label">Quantidade de Pessoas</label>
                                <input type="number" class="form-control" id="quantidade_pessoas"
                                    name="quantidade_pessoas">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="sexo" class="form-label">Sexo da Pessoa</label>
                                <select class="form-select" id="sexo" name="sexo">
                                    <option value="Masculino">Masculino</option>
                                    <option value="Feminino">Feminino</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="idade" class="form-label">Idade</label>
                                <input type="number" class="form-control" id="idade" name="idade">
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="row mt-4 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
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
