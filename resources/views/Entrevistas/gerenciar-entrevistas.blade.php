@extends('layouts.app')

@section('title') Gerenciar Entrevista @endsection

@section('content')

<?php

?>

<div class="container">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ENTREVISTA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('gerenciamento')}}" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col-1">Dia
                            <select class="form-select" id="" name="dia" type="number">

                            </select>
                        </div>

                        <div class="col-1">Status
                            <select class="form-select" id="4" name="status" type="number">

                            </select>
                        </div>
                        <div class="col"><br/>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-tratamentos"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                        </div>
                    </div>
                </form>
                <br/>
            </div>
            <hr/>
            <div class="table">Total assistidos:
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">ASSISTIDO</th>
                            <th class="col">TRATAMENTO</th>
                            <th class="col">ENTREVISTA</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($informacoes as $informacao)
                        <tr>
                            <td>{{$informacao->id}}</td>
                            <td>{{$informacao->nome_pessoa}}</td>
                            <td>{{$informacao->tratamento_descricao}} ({{$informacao->tratamento_sigla}})</td>
                            <td>{{$informacao->entrevista_descricao}} ({{$informacao->entrevista_sigla}})</td>
                            <td>
                    @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>




@endsection

@section('footerScript')


@endsection
