@extends('layouts.app')

@section('title', 'Novo Atendente')

@if (session('msg'))
   <p class="msg">{{ session('msg') }}</p>
@endif

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
            {{-- Search Bar --}}
            <div class="col-6 my-3">
                <form class="d-flex" action="{{route('novo-atendente')}}" method="GET">
                    @csrf
                    <input class="form-control me-2" type="search" id="query" name="query"
                        @if ($search) value="Buscando por {{$search}}"
                        @else placeholder=" Nome / Grupo " @endif >
                    <label for="query"></label>
                    <button class="btn btn-outline-success" type="submit">Procurar</button>
                </form>
            </div>
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
                        @if ($pessoa->atendente()->get())
                            <td>
                                <p> A PESSOA JÁ É ATENDENTE! </p>
                            </td>
                        @else
                            <td>
                                <div class="input-group text-right col-2 offset-2 "> {{-- float-end --}}
                                    <form action="{{route('incluir-atendente')}}" method="POST">
                                            @csrf
                                        <input type="hidden" class="form-control" name="NovoAtendenteRequest" value="{{$pessoa}}">

                                        <label for="store-data-btn"></label>
                                        <input class="btn btn-success" id="store-data-btn" type="submit" name="store-data-btn" value="Adicionar Atendente" ></button>
                                    </form>
                                </div>
                            </td>

                        @endif

                    </tr>
                @endforeach

            </tbody>
        </table>

    </div>
</div>

@endsection
