@extends('layouts.app')

@section('title')
    Gerenciar Pessoas
@endsection

@section('content')
    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR ATENDENTES DE APOIO</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <form action="" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col">Nome
                            <input class="form-control" type="text" maxlength="45"
                                oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="1" name="nome">
                        </div>

                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                value="Pesquisar">
                            <a href="/gerenciar-atendentes-apoio"><input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>
                </form>
                <a href="/incluir-atendentes-apoio"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                        type="button" value="Novo Atendente+"></a>
            </div>
        </div>
    </div>
    <hr>
    Quantidade filtrada:
    <div class="table">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
            <thead style="text-align: center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th class="col">NOME</th>
                    <th class="col">CPF</th>
                    <th class="col">HORARIO DE INICIO</th>
                    <th class="col">HORARIO FINAL</th>
                    <th class="col">STATUS</th>
                    <th class="col">AÇÕES</th>
                </tr>
            </thead>
            <tbody style="font-size: 14px; color:#000000; text-align:center;">

                @foreach ( $atendente as  $atendentes)
                <tr>
                    <td>{{$atendentes->nome_completo}}</td>
                    <td>{{ str_pad($atendentes->cpf, 11, "0", STR_PAD_LEFT)}}</td>
                    <td>{{date('G:i', strtotime($atendentes->dh_inicio))}}</td>
                    <td>{{date('G:i', strtotime($atendentes->dh_fim))}}</td>
                    <td>{{$atendentes->tipo}}</td>
                </tr>
                @endforeach

            </tbody>
            @endsection



