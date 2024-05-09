@extends('layouts.app')

@section('title') Gerenciar Presença Entrevista @endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

<?php
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
?>

<div class="container";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PRESENÇA ENTREVISTA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <form action="{{ route('listas') }}" class="form-horizontal mt-4" method="GET">
                <div class="row align-items-center">
                    <div class="col-5">
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar nome" value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-auto">
                        <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;" type="submit" value="Pesquisar">
                        <a href="/gerenciar-presenca">
                            <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;" type="button" value="Limpar">
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <br>
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
                                
                               
                                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal{{ $listas->id }}" data-tt="tooltip" data-placement="top" title="Presença">
                                    <i class="bi bi-exclamation-triangle" style="font-size: 1rem; color:#000;"></i>
                                </button>

                               <button type="button" class="btn btn-outline-danger btn-sm"  data-tt="tooltip" data-placement="top" data-bs-toggle="modal" data-bs-target="#modalp{{ $listas->id }}" title="Inativar">
                                <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button>
                               {{--  Modal de Presença --}}
                               <div class="modal fade" id="modal{{ $listas->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: rgba(255, 165, 0, 1);">

                                            <h5 class="modal-title" id="exampleModalLabel" >Confirmação de
                                                Presença </h5>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja declarar alta para <br /><span
                                                style="color:rgb(196, 27, 27);">{{ $listas->nome_completo }}</span>&#63;

                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-danger"
                                                href="/criar-presenca/{{ $listas->id }}">Confirmar
                                                Presença </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Fim Modal de Presença --}}
                                 

                               {{--  Modal de exclusão --}}
                               <div class="modal fade" id="modalp{{ $listas->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:rgb(196, 27, 27);">
                                            <h5 class="modal-title" id="exampleModalLabel" style=" color:white">Confirmar
                                                Exclusão </h5>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja declarar alta para <br /><span
                                                style="color:rgb(196, 27, 27);">{{ $listas->nome_completo }}</span>&#63;

                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-danger"
                                                href="/inativar-presenca/{{$listas->id}}">Confirmar
                                                Exclusão </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Fim Modal de exclusao --}}
                                 
                              {{-- Conferir se realmente é necesssário botão de inativar --}}
                               
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
