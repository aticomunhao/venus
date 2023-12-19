@extends('layouts.app')

@section('title') Gerenciar Atendente dia @endsection

@section('content')


<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ATENDENTES DO DIA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('atedia')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class="col-2">Grupo
                        <select class="form-select" id="" name="grupo" type="number">                           
                            <option value=""></option>
                            @foreach ($grupo as $grupos)
                            <option @if(old('grupo')==$grupos->id) {{'selected="selected"'}} @endif value="{{ $grupos->id }}">{{$grupos->nome}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                    <div class="col-2">Atendente
                        <input class="form-control" type="text" id="" name="atendente" value="{{$atendente}}">
                    </div>
                    <div class="col-2">Status
                        <select class="form-select" id="" name="status" type="number">                           
                            <option value="">Todos</option>
                            @foreach ($situacao as $sit)
                            <option @if(old('status')==$sit->id) {{'selected="selected"'}} @endif value="{{ $sit->id }}">{{$sit->tipo}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-atendente-dia"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                            <a href="/definir-sala-atendente"><input class="btn btn-success btn-sm me-md-2" type="button" value="Definir Sala"></a>
                    </form>
                        </div>
                </div>
                <br>
            </div style="text-align:right;">
            <hr>
            <div class="table">Total atendentes: 
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">DATA</th>                                               
                            <th class="col">ATENDENTE</th>
                            <th class="col">SALA</th>                           
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($atende as $atendes)
                            <td scope="">{{$atendes->ida}}</td>
                            <td scope="">{{date( 'd/m/Y', strtotime($atendes->data_hora))}}</td>                                                       
                            <td scope="">{{$atendes->nm_4}}</td>                            
                            <td scope="">{{$atendes->nm_sala}}</td>
                            <td scope="">{{$atendes->tipo}}</td>
                            <td scope="">                                
                                <!--<a href="/desce-status/{{$atendes->ida}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-left-square" style="font-size: 1rem; color:#000;"></i></button></a>
                                <button class="btn btn-outline-warning btn-sm" style="font-size: 1rem; color:#000;" type="button" id="" data-bs-toggle="modal" data-bs-target="#atendimento{{$atendes->ida}}"><i class="bi bi-person" style="font-size: 1rem; color:#000;"></i></button>
                                @include('recepcao-AFI.popUp-sel-atendente')
                                <a href="/sobe-status/{{$atendes->ida}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-right-square" style="font-size: 1rem; color:#000;"></i></button></a>-->
                                <a href="/editar-atendente/{{$atendes->ida}}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/visualizar-atendete/{{$atendes->ida}}"><button type="button" class="btn btn-outline-primary btn-sm"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/cancelar-atendente/{{$atendes->ida}}"><button type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>    
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
            {{$atende->withQueryString()->links()}}
        </div>
    </div>
</div>



@endsection

@section('footerScript')  


@endsection
