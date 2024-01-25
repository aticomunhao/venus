@extends('layouts.app')
@section('head')
    <title>Inlcuir Atendentes Apoio</title>
@endsection
@section('content')
    <br />
    <div class="container">
        <div class="card" >
            <div class="card-header">
                Inlcuir Atendentes Apoio
            </div>
            <div class="card-body">
                <br>
                <div class="row justify-content-start">
                    <form method="POST" action="/armazenar-atendentes-apoio">
                        @csrf
                            <div class="row col-10 offset-1" style="margin-top:none">
                                <div class="col-md-6 col-12">
                                    <div>Nome</div>
                                    <select class="form-select" aria-label="Default select example" required name="nome">
                                        <option value=""></option>
                                        @foreach ($nomes as $nome)
                                        <option value="{{ $nome->id }}">{{ $nome->nome_completo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-12 mt-3 mt-md-0 ">
                                    <div>Horário de inicio</div>
                                    <input type="time" class="form-control" aria-label="Sizing example input"{{-- Input de porcentagem, com minimo de 0.01 e maximo de 100 --}}
                                         name = "dhInicio" required="Required">
                                </div>
                                <div class="col-md-3 col-12 mt-3 mt-md-0 ">
                                    <div>Horário de Final</div>
                                    <input type="time" class="form-control" aria-label="Sizing example input"{{-- Input de data --}}
                                         name = "dhFinal" required="Required">
                                </div>
                            </div>
                        <center>
                            <div class="col-12" style="margin-top: 70px;">
                                <a href="/gerenciar-atendentes-apoio" class="btn btn-danger col-3">
                                    Cancelar
                                </a>
                                <button type = "submit" class="btn btn-primary col-3 offset-3">
                                    Confirmar
                                </button>
                            </div>
                        </center>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
