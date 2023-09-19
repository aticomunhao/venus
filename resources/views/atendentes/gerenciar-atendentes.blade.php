@extends('layouts.app')

@section('title', 'Gerenciar Atendentes')

@section('content')


<div class="container p-5 my-5 border ">
{{-- Teste de Banco @dump($atendentes) --}}


    <div class="row">
        {{--Titulo Gerenciar Atendentes --}}
        <div class="col-8">
            <h1 class="display-4 p-2">Gerenciar Atendendes</h1>
        </div>

        {{-- botão Novo + --}}
        <div class="col-2 offset-2"> {{-- float-end --}}
            <div class="text-right">
              <a href="novo-atendente" class="btn btn-success">Novo +</a>
            </div>
        </div>

        <hr>
        {{-- Perplexity --}}
        <div class="row">

            {{-- Search Bar --}}
            <div class="col-6 my-3">
                <form class="d-flex" method="GET" action="/gerenciar-atendentes" >
                    @csrf
                    <input class="form-control me-2" type="search" id="query" name="query" placeholder="Nome / CPF / Grupo">
                    <label for="query"></label>
                    <button class="btn btn-outline-success" type="submit">Procurar</button>
                </form>
            </div>


            {{-- Extra Buttons
            <div class="col text-end">
                <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Option 1</a></li>
                    <li><a class="dropdown-item" href="#">Option 2</a></li>
                    <li><a class="dropdown-item" href="#">Option 3</a></li>
                </ul>
                </div>
                <div class="btn-group ms-2">
                <button type="button" class="btn btn-primary">Button 1</button>
                <button type="button" class="btn btn-primary">Button 2</button>
                <button type="button" class="btn btn-primary">Button 3</button>
                </div>
                <div class="form-check form-switch ms-2 ">
                <input class="form-check-input text-end" type="checkbox" id="flexSwitchCheckDefault">
                <label class="form-check-label text-end" for="flexSwitchCheckDefault"> Text Here </label>
                </div>
            </div>
                    {{-- END extra buttons --}}
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
                    <th>Grupo</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            {{-- Corpo Tabela --}}
            <tbody>
                @foreach ($atendentes as $atendente)
                    <tr>

                        <td> {{ $atendente->nome_completo }}</td>
                        <td> {{ $atendente->nome_grupo }} </td>
                        <td class="text-center"> {{ $atendente->status_atendente ? 'Ativo' : 'Inativo' }}</td>


                        {{-- Campo Buttons}} --}}
                        <td class="text-center">
                            {{-- btn-group --}}
                            <div class="btn-group justify-content-center">
                                {{-- <div class="btn-group d-flex justify-content-center"> --}}

                                {{-- Botão Grupos --}}
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" > Grupos <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        @foreach ($grupos as $grupo)
                                      <li><a class="dropdown-item" href="#">{{ $grupo->nome }} </a></li>
                                      @endforeach
                                    </ul>
                                </div>



                                {{-- Botão Status --}}
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" > Status <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        @foreach ($atendentes as $atendente)
                                      <li><a class="dropdown-item" href="#">{{ $atendente->status_atendente }} </a></li>
                                      @endforeach
                                    </ul>
                                </div>


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

            @if (count($atendentes) == 0 && $search)
                <p>Não foi possível encontrar nenhum Atendente com " {{ $search }}" ! <a href="/gerenciar-atendentes">Ver todos:</a></p>
            @elseif (count($atendentes) == 0)
                <p>Nenhum atendente encontrado</p>
            @endif

    </div>
</div>

@endsection
