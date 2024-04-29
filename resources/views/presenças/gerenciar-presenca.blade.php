@extends('layouts.app')

@section('title') Gerenciar Presença @endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

<?php
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
?>

<div class="container";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PRESENÇA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
                <br/>
            </div style="text-align:right;">
            <hr/>
            {{-- <div class="table">Total assistidos: {{$contar}} --}}
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">ASSISTIDO</th>
                            <th class="col">CPF</th>
                            <th class="col">DIA</th>
                            <th class="col">HORÁRIO INICIO</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($lista as $listas)
                            <td>{{$listas->id}}</td>
                            <td>{{$listas->nome_completo}}</td>
                            <td>{{$listas->cpf}}</td>
                            <td>{{date ('d/m/Y', strtotime($listas->dh_marcada))}}</td>
                            <td>{{date ('H:i', strtotime($listas->dh_marcada))}}</td>
                            <td>
                                
                               
                                    <a href="/criar-presenca/{{ $listas->id }}"><button type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Histórico"><i class="bi bi-exclamation-triangle" style="font-size: 1rem; color:#000;"></i></button></a>
                        </div>
                                <div class="modal fade" id="modalF{{ $listas->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel" style="color: green;">Confirmação de Finalização</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Tem certeza que deseja registrar presença <p style="color: green;">{{ $listas->nome_completo }}&#63;</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <a type="button" class="btn btn-danger" href="/deletar-presenca/{{ $listas->id }}">Cancelar </a>
                                                <a type="button" class="btn btn-success" href="/criar-presenca/{{ $listas->id }}">Confirmar </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 
                                <a href="/visualizar-presenca/{{$listas->id}}"><button type="button" class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top" title="Histórico"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/inativar/{{$listas->id}}"><button type="button" class="btn btn-outline-danger btn-sm"  data-tt="tooltip" data-placement="top" title="Inativar"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>
                            </td>
                       
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
