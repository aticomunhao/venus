@extends('layouts.app')

@section('title') Gerenciar Tratamentos @endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

<?php
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
?>

<div class="container";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR TRATAMENTOS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('gtcdex')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class ="col-2">Data início
                        <input class="form-control" type="date" id="" name="dt_enc" value="{{$data_enc}}">
                    </div>
                    <div class="col-1">Dia
                        <select class="form-select" id="" name="dia" type="number">
                            <option value=""></option>
                            @foreach ($dia as $dias)
                            <option value="{{$dias->id}}">{{$dias->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">Assistido
                        <input class="form-control" type="text" id="3" name="assist" value="{{$assistido}}">
                    </div>
                    <div class="col-1">Status
                        <select class="form-select" id="4" name="status" type="number">
                            <option value="{{$situacao}}"></option>
                            @foreach ($stat as $status)
                            <option value="{{$status->id}}">{{$status->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                        <div class="col"><br/>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-tratamentos"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>
                    
                        </div>
                </div>
                <br/>
            </div style="text-align:right;">
            <hr/>
            <div class="table">Total assistidos: {{$contar}}
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>

                            <th class="col">PRIORIDADE</th>
                            <th class="col">ASSISTIDO</th>
                            <th class="col">REPRESENTANTE</th>
                            <th class="col">DIA</th>
                            <th class="col">HORÁRIO</th>
                            <th class="col">TRATAMENTO</th>
                            <th class="col">GRUPO</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($lista as $listas)
                            <td>{{$listas->idtr}}</td>
                            <td>{{$listas->prdesc}}</td>
                            <td>{{$listas->nm_1}}</td>
                            <td>{{$listas->nm_2}}</td>
                            <td>{{$listas->nomed}}</td>
                            <td>{{date ('H:m:s', strtotime($listas->h_inicio))}}</td>
                            <td>{{$listas->sigla}}</td>
                            <td>{{$listas->nomeg}}</td>
                            <td>{{$listas->tst}}</td>
                            <td>
                                <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#presenca{{$listas->idtr}}" data-tt="tooltip" data-placement="top" title="Presença"><i class="bi bi bi-exclamation-triangle" style="font-size: 1rem; color:#000;"></i></button>
                                <a href="/visualizar-trat/{{$listas->idtr}}"><button type="button" class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top" title="Histórico"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/inativar/{{$listas->idtr}}"><button type="button" class="btn btn-outline-danger btn-sm"  data-tt="tooltip" data-placement="top" title="Inativar"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>
                            </td>
                            @include('recepcao-integrada/pop-up-presenca')
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
            {{$lista->withQueryString()->links()}}
        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

</script>




@endsection

@section('footerScript')


@endsection
