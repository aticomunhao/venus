@extends('layouts.app')

@section('title')
    Passe trabalhador
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form class="form-horizontal" method="post" action="/armazenar-avulsopti">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    PASSE TRABALHADOR
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">


                            <div class="row g-3 align-items-end">

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Quantidade de passes
                                        <span class="tooltips ms-1">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                    </label>
                                    <input type="number" class="form-control" name="acompanhantes" placeholder="0"
                                        min="0" required>
                                </div>

                                <!-- Reunião Mediúnica -->
                                <div class="col-md-8">
                                    <label class="form-label">
                                        Reunião Mediúnica
                                        <span class="tooltips ms-1">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                    </label>
                                    <select class="form-select select2" name="reuniao" required>
                                        @foreach ($reuniao as $reunioes)
                                            <option value="{{ $reunioes->id }}">
                                                {{ $reunioes->nome }} - {{ $reunioes->nomedia }} -
                                                {{ date('H:i', strtotime($reunioes->h_inicio)) }}/{{ date('H:i', strtotime($reunioes->h_fim)) }}
                                                - Sala {{ $reunioes->sala }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-4 justify-content-center">
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <a class="btn btn-danger px-4" href="/gerenciar-pti" role="button">Cancelar</a>
                                </div>
                                <div class="d-grid gap-2 col-4 mx-auto">
                                    <button type="submit" class="btn btn-primary px-4"
                                        style="color:#fff;">Confirmar</button>
                                </div>
                            </div>
                        </div>

                    @endsection
