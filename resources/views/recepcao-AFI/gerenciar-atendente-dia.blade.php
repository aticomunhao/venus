@extends('layouts.app')

@section('title') Gerenciar Atendente dia @endsection

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

<div class="container";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ATENDENTES DO DIA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('afidia')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class ="col">Data
                        <input class="form-control" type="date" id="" name="data" value="{{$data}}">
                    </div>
                    <div class="col">Grupo
                        <select class="form-select" id="" name="grupo" type="number"> 
                            <option value=""></option>                        
                            @foreach ($grupo as $grupos)
                            {{--<option value="{{ old('grupo', $grupos->id) }}" selected="{{ old('grupo') == $grupos->id ? 'selected' : '' }}">{{$grupos->nome}}</option>--}}
                            <option @if(old('grupo') == $grupos->id) {{'selected="selected"'}} @endif value="{{ $grupos->id }}">{{$grupos->nome}}</option>                            
                            @endforeach 
                                      
                        </select>                       
                    </div>
                    <div class="col">Atendente
                        <input class="form-control" type="text" id="" name="atendente" value="{{$atendente}}">
                    </div>
                    <div class="col">Status
                        <select class="form-select" id="" name="status" type="number">
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="false" {{ old('status') == 'false' ? 'selected' : '' }}>Inativo</option>
                            <option value="" {{ old('status') == '' ? 'selected' : '' }}>Todos</option>
                        </select>                       
                    </div>
                        <div class="col-5"><br>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-atendente-dia"><input class="btn btn-light btn-sm me-md-2"  style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                            <a href="/definir-sala-atendente"><input class="btn btn-success btn-sm me-md-2" autofocus type="button" value="Definir Atendente / Sala"></a>
                    </form>
                    <a href="/gerenciar-atendimentos"><input class="btn btn-danger btn-sm me-md-2" style="font-size: 0.9rem;" type="button" value="Retornar principal"></a>
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
                            <th class="col">GRUPO</th>                                               
                            <th class="col">ATENDENTE</th>
                            <th class="col">SALA</th>                           
                            <th class="col">STATUS ATENDENTE</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($atende as $atendes)
                            <td scope="">{{$atendes->nr}}</td>
                            <td scope="">{{date( 'd/m/Y', strtotime($atendes->data_hora))}}</td>                                                       
                            <td scope="">{{$atendes->nomeg}}</td>
                            <td scope="">{{$atendes->nm_4}}</td>                            
                            <td scope="">{{$atendes->nm_sala}}</td>
                            <td scope="">{{$atendes->tipo}}</td>
                            <td scope="">
                                @if (date( 'Y-m-d', strtotime($atendes->data_hora)) < $now)
                                    <button disabled type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Editar"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button>           
                                    <button disabled class="btn btn-outline-danger btn-sm"  style="font-size: 1rem; color:#000;" type="button" id="" data-bs-toggle="modal" data-bs-target="#delete{{$atendes->idatd}}" data-tt="tooltip" data-placement="top" title="Excluir"><i class="bi bi-trash3" style="font-size: 1rem; color:#000;"></i></button>                                
                                @else
                                    <a href="/editar-atendente-dia/{{$atendes->idatd}}"><button  type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Editar"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button></a>            
                                    <button class="btn btn-outline-danger btn-sm" style="font-size: 1rem; color:#000;" type="button" id="" data-bs-toggle="modal" data-bs-target="#delete{{$atendes->idatd}}" data-tt="tooltip" data-placement="top" title="Excluir"><i class="bi bi-trash3" style="font-size: 1rem; color:#000;"></i></button>                                
                                @endif
                            </td>
                            @include('recepcao-AFI/pop-up-delete')
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
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
