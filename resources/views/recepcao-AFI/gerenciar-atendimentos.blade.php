@extends('layouts.app')

@section('title') Gerenciar Atendimentos @endsection

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<?php
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
?>

<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ATENDIMENTOS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('atedex')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class ="col-2">Data início
                        <input class="form-control" type="date" id="" name="dt_ini" value="{{$data_inicio}}">
                    </div>
                    <div class="col-2">Assistido
                        <input class="form-control" type="text" id="3" name="assist" value="{{$assistido}}">
                    </div>
                    <div class="col-2">Status
                        <select class="form-select" id="4" name="status" type="number">                           
                            <option value=""></option>
                            @foreach ($st_atend as $statusz)
                            <option @if(old('status')==$statusz->id) {{'selected="selected"'}} @endif value="{{ $statusz->id }}">{{$statusz->descricao}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-atendimentos"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>
                            <a href="/criar-atendimento"><input class="btn btn-success btn-sm me-md-2" type="button" autofocus value="Novo Atendimento &plus;"></a>
                            <a href="/gerenciar-pessoas"><input class="btn btn-success btn-sm me-md-2" type="button" value="Gerenciar Pessoas"></a>
                            <a href="/gerenciar-atendente-dia"><input class="btn btn-success btn-sm me-md-2" type="button" value="Atendentes do dia"></a>

                        </div>
                </div>
                <br>
            </div style="text-align:right;">
            <hr>
            <div class="table">Total assistidos: {{$contar}}
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">AFI PREF</th>
                            <th class="col">TIPO AFI</th>
                            <th class="col">HORÁRIO CHEGADA</th>
                            <th class="col">PRIOR</th>
                            <th class="col">ASSISTIDO</th>
                            <th class="col">REPRESENTANTE</th>                                                        
                            <th class="col">ATENDENTE</th>
                            <th class="col">SALA</th>                           
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($lista as $listas)
                            <td scope="">{{$listas->ida}}</td>
                            <td scope="">{{$listas->nm_3}}</td>
                            <td scope="">{{$listas->tipo}}</td>
                            <td scope="">{{date( 'd/m/Y H:i:s', strtotime($listas->dh_chegada))}}</td>
                            <td scope="">{{$listas->prdesc}}</td>
                            <td scope="" >{{$listas->nm_1}}</td>
                            <td scope="">{{$listas->nm_2}}</td>                                                        
                            <td scope="">{{$listas->nm_4}}</td>                            
                            <td scope="">{{$listas->nr_sala}}</td>
                            <td scope="">{{$listas->descricao}}</td>
                            <td scope="">                                
                                <!--<a href="/desce-status/{{$listas->ida}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-left-square" style="font-size: 1rem; color:#000;"></i></button></a>
                                <button class="btn btn-outline-warning btn-sm" style="font-size: 1rem; color:#000;" type="button" id="" data-bs-toggle="modal" data-bs-target="#atendimento{{$listas->ida}}"><i class="bi bi-person" style="font-size: 1rem; color:#000;"></i></button>
                                @include('recepcao-AFI.popUp-sel-atendente')
                                <a href="/sobe-status/{{$listas->ida}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-right-square" style="font-size: 1rem; color:#000;"></i></button></a>-->
                                <a href="/editar-atendimento/{{$listas->ida}}"><button type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Editar"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/visualizar-atendimentos/{{$listas->idas}}"><button type="button" class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top" title="Visualizar"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/cancelar-atendimento/{{$listas->ida}}"><button type="button" class="btn btn-outline-danger btn-sm" data-tt="tooltip" data-placement="top" title="Cancelar"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>    
                            </td>
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
