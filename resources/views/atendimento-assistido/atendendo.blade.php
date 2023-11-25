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
                        <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{date( 'd/m/Y' , strtotime ($now))}}" type="text" name="data" id="" disabled>
                    </div>   
                    <div class="col-3">Grupo
                        <input class="form-control" style="text-align:left; font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="" name="nome" id="" type="text" disabled>
                    </div>
                
                    <div class="col-2">Código Atendente                    
                        <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="text" name="id_atendene" id="" value="{{$atendente}}" disabled>
                    </div>
                                 
                    <div class="col-5">Nome do Atendete                   
                        <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$nome}}" name="nome_usuario" id="" type="text" disabled>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" style="text-align:right;">
            <div class="col-6">
                <a href="/meus-atendimentos"><input class="btn btn-info btn-sm me-md-2"  type="button" value="Meus atendimentos"></a>
            </div>
            <div class="col-6">
            <a href="/atender"><input class="btn btn-success btn-sm me-md-2"  type="button" value="Atender agora"></a>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">                    
                    <div class="table">
                        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                            <thead style="text-align: center;">
                                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                    <th class="col">ATENDENTE PREFERIDO</th>
                                    <th class="col">TIPO AF</th>
                                    <th class="col">HORÁRIO CHEGADA</th>
                                    <th class="col">PRIORIDADE</th>
                                    <th class="col">ASSISTIDO</th>
                                    <th class="col">REPRESENTANTE</th>
                                    <th class="col">STATUS</th>
                                    <th class="col">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                            @foreach($assistido as $assistidos)                               
                            <tr>
                                <td scope="">{{$assistidos->nm_3}}</td>
                                <td scope="">{{$assistidos->tipo}}</td>
                                <td scope="">{{date( 'Y/m/d H:m:s', strtotime($assistidos->dh_chegada))}}</td>
                                <td scope="">{{$assistidos->prdesc}}</td>
                                <td scope="">{{$assistidos->nm_1}}</td>
                                <td scope="">{{$assistidos->nm_2}}</td> 
                                <td scope="">{{$assistidos->descricao}}</td>
                                <td scope="">
                                    <a href="/historico/{{$assistidos->idat}}/{{$assistidos->idas}}"><button type="button" class="btn btn-outline-primary btn-sm" data-toggle="tooltip" id="#element" data-placement="top" title="Visualizar">
<i class="bi bi-search" style="font-size: 1rem; color:#000;" ></i></button></a>
                                    <a href="/fim-analise/{{$assistidos->idat}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-bell" style="font-size: 1rem; color:#000;"></i></button></a>
                                    <a href="/iniciar-atendimento/{{$assistidos->idat}}"><button type="button" class="btn btn-outline-success btn-sm"><i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i></button></a>                                        
                                    <!--<a href="/tratar/{{$assistidos->idat}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-bandaid" style="font-size: 1rem; color:#000;"></i></button></a>-->                                        
                                    <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#tratamento{{$assistidos->idat}}"><i class="bi bi bi-bandaid" style="font-size: 1rem; color:#000;"></i></button>                                                                        
                                    <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#entrevista{{$assistidos->idat}}"><i class="bi bi bi-mic" style="font-size: 1rem; color:#000;"></i></button>
                                    <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#anotacoes{{$assistidos->idat}}"><i class="bi bi-journal-bookmark-fill" style="font-size: 1rem; color:#000;"></i></button></a>
                                    <button class="btn btn-outline-danger btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#finalizar{{$assistidos->idat}}"><i class="bi bi-door-open" style="font-size: 1rem; color:#000;"></i></button>
                                @include('atendimento-assistido.pop-up-enc_tratamento')
                                @include('atendimento-assistido.pop-up-enc_entrevista')
                                </td>
                                @include('atendimento-assistido.pop-up-anotacoes')                               
                            </tr>
                            @include('atendimento-assistido.pop-up-finalizar') 
                            @endforeach
                            </tbody>                         
                            
                        </table>
                                               
                    </div>                    
                </div>
            </div>
        </div>
    </div>    
</div>
<!--style="pointer-events: none"-->

<script>

$('#element').tooltip('show')

</script>
    



@endsection

@section('footerScript')  


@endsection
