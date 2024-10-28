@extends('layouts.app')
@section('title')
    Relatório de Temáticas
@endsection
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            RELATÓRIO DE VAGAS
        </h4>
        <br>


        <div class="card">
            <div class="card-header">
                <form action="/relatorio-vagas-grupos">
                    <div class="row">
                        <div class="col-6">
                            Nome
                            <input class="form-control" type="text" id="" name="grupo" value=""
                                placeholder="Nome do grupo...">
                        </div>
                        <div class="col-1">
                            <br />
                            <input class="btn btn-light btn-sm me-md-2 col-6 col-12"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                value="Pesquisar">
                        </div>
                        <div class="col-1">
                            <br />
                            <a href="/relatorio-vagas-grupos">
                                <input class="btn btn-light btn-sm me-md-2 col-12"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar">
                            </a>
                        </div>

                        <div class="col d-flex justify-content-end mt-3">

                            <a href="/gerenciar-encaminhamentos">
                                <input class="btn btn-warning btn-sm me-md-2 col-12"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Encaminhamentos">
                            </a>
                        </div>

                    </div>
                </form>
            </div>
            <div class="card-body">


                <table class="table  table-striped table-bordered border-secondary table-hover align-middle ">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>GRUPO</th>
                            <th>DIA</th>
                            <th>H_INICIO</th>
                            <th>H_FIM</th>
                            <th>SETOR</th>
                            <th>VAGAS</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">

                        @foreach ($grupos as $grupo)
                            <tr>
                                <td> {{ $grupo->nome }} </td>
                                <td> {{ $grupo->dia }} </td>
                                <td>{{ $grupo->h_inicio }}</td>
                                <td>{{ $grupo->h_fim }}</td>
                                <td>{{ $grupo->setor }}</td>
                                @if (($grupo->max_atend - $grupo->trat) < ($grupo->max_atend * 0.1))
                                    <td style="color:red">{{$grupo->max_atend - $grupo->trat}}</td>
                                @else
                                    <td style="color:green">{{$grupo->max_atend - $grupo->trat}}</td>
                                @endif
                            </tr>
                        @endforeach


                    </tbody>
                </table>

                {{ $grupos->links('pagination::bootstrap-5') }}
            </div>
        </div>
        <br />
    @endsection
