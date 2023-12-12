@extends('layouts.app')

@section('title') Gerenciar Reuniões mediúnicas @endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

<br/>
<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR REUNIÕES MEDIÚNICAS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('recdex')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class ="col">Data início
                        <input class="form-control" type="date" id="" name="dt_enc" value="">
                    </div>
                    <div class="col-5">Grupo
                        <input class="form-control" type="text" id="3" name="grupo" value="">
                    </div>
                    <div class="col">Status
                        <select class="form-select" id="4" name="status" type="number">
                            <option value=""></option>
                            @foreach ($status as $statu)
                            <option value="{{$statu->id}}">{{$statu->descricao}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                        <div class="col"><br/>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-recepcao"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                            <a href="/criar-reuniao"><input class="btn btn-success btn-sm me-md-2" type="button" autofocus value="Nova reunião &plus;"></a>
                    </form>
                        </div>
                </div>
                <br/>
            </div style="text-align:right;">
            <hr/>
            <div class="table">Total reuniões: {{$contar}}
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col-2">GRUPO</th>
                            <th class="col">DIA</th>
                            <th class="col">SALA</th>
                            <th class="col-2">TRATAMENTO</th>
                            <th class="col">HORÁRIO INÍCIO</th>
                            <th class="col">HORÁRIO FIM</th>
                            <th class="col">MAX ATENDIDOS</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($reuniao as $reuni)
                            <td>{{$reuni->idr}}</td>   
                            <td>{{$reuni->id_grupo}}</td>
                            <td>{{$reuni->dia}}</td>
                            <td>{{$reuni->id_sala}}</td>
                            <td>{{$reuni->id_tratamento}}</td>
                            <td>{{date ('d/m/Y H:m:s', strtotime($reuni->dh_inicio))}}</td>
                            <td>{{date ('d/m/Y H:m:s', strtotime($reuni->dh_fim))}}</td>
                            <td>{{$reuni->max_atendidos}}</td>                                      
                            <td>                                
                                <a href="/agenda/{{$reuni->idr}}"><button type="button" class="btn btn-outline-success btn-sm" data-tt="tooltip" data-placement="top" title="Agendar"><i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/faltas/{{$reuni->idr}}"><button type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Presença"><i class="bi bi-exclamation-triangle" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/visualizar/{{$reuni->idr}}"><button type="button" class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top" title="Histórico"><i class="bi bi-search" style="font-size: 1rem; color:#000;" data-tt="tooltip" data-placement="top" title="Inativar"></i></button></a>
                                <a href="/inativar/{{$reuni->idr}}"><button type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>    
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
            {{$reuniao->withQueryString()->links()}}
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
