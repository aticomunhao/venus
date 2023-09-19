@extends('layouts.app')

@section('title', 'Gerenciar Atendentes')

@section('content')


<div class="container p-5 my-5 border ">

        <div class="row">


            {{--Titulo Novo Atendente --}}
            <div class="col-8">
                <h1 class="display-4 p-2">Adicionar Atendende</h1>
            </div>

            {{-- botão Voltar --}}
            <div class="col-2 offset-2"> {{-- float-end --}}
                <div class="text-right">
                  <a href="gerenciar-atendentes" class="btn btn-primary">Voltar</a>
                </div>
            </div>
            {{-- Add Flash Here --}}

            <hr>
            <div class="row">

                {{-- Search Bar
                <div class="col-6 my-3">
                    <form class="d-flex" method="POST" action="{{ route('search') }}" >
                        @csrf
                        <input class="form-control me-2" type="search" id="query" name="query" placeholder="Nome / CPF / Grupo">
                        <label for="query"></label>
                        <button class="btn btn-outline-success" type="submit">Procurar</button>
                    </form>
                </div>
                --}}
            </div>


            {{-- Tabela Resultados Pessoas --}}
            <table class="table table-hover">

                {{-- Cabeçalio --}}
                <thead class="table-primary">
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th class="text-center">Incluir?</th>
                    </tr>
                </thead>

                {{-- Corpo Tabela --}}
                <tbody>
                    @foreach ($pessoas as $pessoa)
                        <tr>
                            <td> {{ $pessoa->nome_completo }}</td>
                            <td> {{ $pessoa->cpf }} </td>

                            {{-- botão Incluir --}}
                            <td>
                                <div class="input-group text-right col-2 offset-2 "> {{-- float-end --}}
                                    <form action="/novo-atendente/{{$id}}" method="GET">
                                            @csrf
                                        <input type="hidden" class="form-control" name="NovoAtendenteRequest" value="{{$pessoa->id}}">

                                        <label for="store-data-btn"></label>
                                        <input class="btn btn-success" id="store-data-btn" type="submit" name="store-data-btn" value="Adicionar Atendente" ></button>
                                    </form>


                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

@endsection
