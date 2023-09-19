@extends('layouts.app')

@section('title') Gerenciar Pessoas @endsection

@section('content')

<div class="container p-5 my-5 border ">
{{-- Teste de Banco @dump($atendentes) --}}
    <div class="row">
        {{--Titulo Gerenciar Atendentes --}}
        <div class="col-8">
            <h1 class="display-6 p-2">Gerenciar Pessoas</h1>
        </div>

        {{-- botão Novo + --}}
        <div class="col-2 offset-2"> {{-- float-end --}}
            <div class="text-right">
              <a href="#" class="btn btn-success">Cadastrar Nova +</a>
            </div>
        </div>
    </div>

    {{-- Tabela Resultados --}}
    <table class="table table-hover">
        {{-- <table class="table table-bordered"> Bordered --}}
        {{-- <table class="table table-striped"> Striped --}}
        {{-- <table class="table table-hover"> hover --}}
        {{-- <table class="table table-dark"> dark, for dark mode --}}


        {{-- Cabeçalio --}}
        <thead class="table-primary">
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Status</th>
                <th>DDD</th>
                <th>Telefone</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        {{-- Corpo Tabela --}}
        <tbody>
            @foreach ($pessoas as $pessoa)
            <tr>
                <td>{{ $pessoa['nome_completo'] }}</td>
                <td>{{ $pessoa['cpf'] }}</td>
                <td>{{ $pessoa['status'] ? 'Ativo' : 'Inativo' }}</td>
                <td>{{ $pessoa['ddd'] }}</td>
                <td>{{ $pessoa['celular'] }}</td>


                {{-- Buttons --}}
                <td class="text-center">

                    {{-- Button group --}}
                    <div class="btn-group">

                            {{-- Botão Grupos --}}
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Grupos <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Grupo 1</a></li>
                                    <li><a href="#">Grupo 2</a></li>
                                    <li><a href="#">Grupo 3</a></li>
                                </ul>
                            </div>

                            {{-- Botão Status --}}
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Status <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Ativo</a></li>
                                    <li><a href="#">Inativo</a></li>
                                </ul>
                            </div>

                            {{-- Botão Visualizar --}}
                            <div class="btn-group">
                                <button class="btn btn-info text-white"> Visualizar </button>
                                {{-- <a href="#" class="btn">Visualizar</a> basico  --}}
                                     <button type="button" href="#" class="btn btn-info">Visualizar</button> {{--outline--}}
                                {{-- <a href="#" class="btn tbn-info ">Visualizar</a> Colored --}}
                            </div>


                    </div>

                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
@endsection

