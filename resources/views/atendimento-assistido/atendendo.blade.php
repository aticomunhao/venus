@extends('layouts.app')

@section('title') Atendimento Fraterno @endsection

@section('content')
<?php
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
?>

<div class="container-fluid";>
    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">ATENDIMENTO FRATERNO</h4>
    <div class="col-12">
        <hr>
        <div class="card">
            <div class="card-body"> 
                <div class="row">
                    <div class="col-2">Data               
                        <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{date( 'd/m/Y' , strtotime ($lista[0]->dh_chegada))}}" type="text" name="data" id="">
                    </div>   
                    <div class="col-3">Grupo
                        <input class="form-control" style="text-align:left; font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$grupo[0]->nomeg}}" name="nome" id="" type="text">
                    </div>
                
                    <div class="col-2">Código Atendente                    
                        <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="text" name="id_atendene" id="" value="{{$lista[0]->id_atendente}}">
                    </div>
                                 
                    <div class="col-5">Nome do Atendete                   
                        <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$lista[0]->nm_4}}" name="nome_usuario" id="" type="text">
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" style="text-align:center;">
            <a href="/meus-atendimentos"><input class="btn btn-success btn-sm me-md-2"  type="button" value="Meus atendimentos"></a>
        </div>
        <br>
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">                    
                    <div class="table">
                        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                            <thead style="text-align: center;">
                                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                    <th class="col">ASSISTIDO</th>
                                    <th class="col">REPRESENTANTE</th>
                                    <th class="col">HORÁRIO CHEGADA</th>
                                    <th class="col">TIPO AF</th>
                                    <th class="col">STATUS</th>
                                    <th class="col">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                                <tr>
                                    <td scope="" >{{$lista[0]->nm_1}}</td>
                                    <td scope="" >{{$lista[0]->nm_2}}</td>
                                    <td scope="" >{{date( 'd/m/Y H:i:s', strtotime($lista[0]->dh_chegada))}}</td>
                                    <td scope="" >{{$lista[0]->tipo}}</td>
                                    <td scope="" >{{$lista[0]->descricao}}</td>
                                    <td scope="">
                                        <a href="/historico-assistido"><button type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>    
                                        <a href="/iniciar-atendimento"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-left-square" style="font-size: 1rem; color:#000;"></i></button></a>
                                        
                                        <a href="/encaminhamentos"><button type="button" class="btn btn-outline-primary btn-sm"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <a href="/registrar-historico"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-right-square" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <a href="/finalizar"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button></a>
                                        
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('footerScript')  


@endsection
