@extends('layouts.app')

@section('title') Gerenciar Atendimentos @endsection

@section('content')
<?php
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
?>

<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ATENDIMENTOS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('recdex')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class ="col">Data início
                        <input class="form-control" type="date" id="" name="dt_enc" value="{{$data_enc}}">
                    </div>
                    <div class="col-5">Assistido
                        <input class="form-control" type="text" id="3" name="assist" value="{{$assistido}}">
                    </div>
                    <div class="col">Status
                        <select class="form-select" id="4" name="status" type="number">
                            <option value="{{$situacao}}"></option>
                            @foreach ($stat as $status)
                            <option value="{{$status->id}}">{{$status->descricao}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                        <div class="col"><br/>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-recepcao"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
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
                            <th class="col">HORÁRIO ENCAMINHAMENTO</th>
                            <th class="col">PRIORIDADE</th>
                            <th class="col">ASSISTIDO</th>
                            <th class="col">REPRESENTANTE</th>
                            <th class="col">TIPO TRATAMENTO</th>                                                                                                              
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($lista as $listas)
                            <td>{{$listas->ide}}</td>           
                            <td>{{$listas->dh_enc}}</td>
                            <td>{{$listas->prdesc}}</td>
                            <td>{{$listas->nm_1}}</td>
                            <td>{{$listas->nm_2}}</td>
                            <td>{{$listas->desctrat}}</td>
                            <td>{{$listas->tsenc}}</td>
                            <td>                                
                                <a href="/agenda/{{$listas->ide}}"><button type="button" class="btn btn-outline-success btn-sm"><i class="bi bi-bookmark-check" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/faltas/{{$listas->ide}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/visualizar/{{$listas->ide}}"><button type="button" class="btn btn-outline-primary btn-sm"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/inativar/{{$listas->ide}}"><button type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>    
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



@endsection

@section('footerScript')  


@endsection
