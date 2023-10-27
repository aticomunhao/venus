@extends('layouts.main')

@section('content')

<h1>Gerenciar Grupos </h1>

<div class="row">
    {{--Titulo Gerenciar Atendentes --}}
    <div class="col-8">
        <h1 class="display-4 p-2">Gerenciar Grupos</h1>
    </div>

    {{-- botão Novo + --}}
    <div class="col-2 offset-2"> {{-- float-end --}}
        <div class="text-right">
            <a href="{{route('novo-grupo')}}" class="btn btn-success">Novo +</a>
        </div>
    </div>

    <hr class="m-0 p-0">
    {{-- Perplexity --}}
    <div class="row">

        {{-- Search Bar --}}
        <div class="col-6 my-3">
            <form class="d-flex" action="/gerenciar-grupos" method="GET">
                {{--<form class="d-flex" action="{{route('gerenciar-atendentes/', $request->search)}}" method="GET"> --}}
                @csrf
                <input class="form-control me-2" type="search" id="query" name="query"
                    @if ($search) value="Buscando por {{$search}}"
                    @else placeholder=" Grupo " @endif >

                <label for="query"></label>
                <button class="btn btn-outline-success" type="submit">Procurar</button>
            </form>
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
                <th>Grupos</th>
                <th>Dia da Semana</th>
                <th>Inicio</th>
                <th>Fim</th>
                <th>Status</th>
                <th>Vagas</th>
                <th>Tipo</th>
                <th>Nº Trabalhadores</th>
                <th>Sala</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        {{-- Corpo Tabela --}}
        <tbody>

            @foreach ($grupos as $grupo)
                <tr>

                    <td> {{ $grupo->nome }}</td>
                    <td> {{ $grupo->id_dia_semana }}
                        <td> {{ $grupo->hr_inicio }}</td>
                        <td> {{ $grupo->hr_fim }}</td>
                        <td> {{ $grupo->ativo }}</td>
                        <td> {{ $grupo->nr_vagas }}</td>
                        <td> {{ $grupo->id_tipo_grupo }}</td>
                        <td> {{ $grupo->nr_trabalhadores }}</td>
                        <td> {{ $grupo->id_sala }}</td>


                    {{-- Campo Buttons}} --}}
                    <td class="text-center">
                        {{-- btn-group --}}
                        <div class="btn-group justify-content-center">
                            {{-- <div class="btn-group d-flex justify-content-center"> --}}


                            {{-- Botão Visualizar --}}
                            <div class="btn-group">
                                {{-- <a href="#" class="btn">Visualizar</a> basico  --}}
                                <button type="button" class="btn btn-info text-white">Visualizar </button> {{--outline--}}
                                {{-- <a href="#" class="btn tbn-info ">Visualizar</a> Colored --}}
                            </div>
                            {{--
                            <a href="/visualizar-atendendes/{{$atendentes->id}}"><button type="button" class="btn btn-outline-info btn-sm"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                            --}}

                        </div>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

        @if (count($grupos) == 0 && $search)
            <p>Não foi possível encontrar nenhum Atendente com " {{ $search }}" ! <a href="/gerenciar-atendentes">Ver todos:</a></p>
        @elseif (count($grupos) == 0)
            <h3>Nenhum Grupo encontrado! </h3>
        @endif

</div>



@endsection


