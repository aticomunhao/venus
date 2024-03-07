@extends('layouts.app')

@section('title') Gerenciar Grupos @endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

<div class="container">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR GRUPOS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <form action="{{route('nomes')}}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-3">Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar nome {{ request('nome_pesquisa') }}">
                    </div>

                    <div class="col"><br>
                        <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                        <a href="/gerenciar-grupos"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>
                    <a href="/criar-grupos"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;" type="button" value="Novo grupo +"></a>

                    </div>
                </div>
        </div>


    <hr>

    <div class="row" style="text-align:center;">
        <div class="col">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col-3 ">NOME</th>
                        <th class="col"> DATA INÍCIO</th>
                        <th class="col"> DATA FIM</th>
                        <th class="col">TIPO GRUPO</th>
                        <th class="col">STATUS GRUPO</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align:center;">
                    @foreach ($grupo as $grupos)
                    <tr>
                        <td> {{$grupos->nome}} </td>
                        <td> {{$grupos->data_inicio}} </td>
                        <td> {{$grupos->data_fim}} </td>
                        <td> {{$grupos->nm_tipo_grupo}} </td>
                        <td> {{$grupos->descricao1}} </td>
                        <td>
                            <a href="/editar-grupos/{{$grupos->id}}" type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Editar">
                                <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                            </a>
                            <a href="/visualizar-grupos/{{$grupos->id}}" type="button" class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top" title="Visualizar">
                                <i class="bi bi-search" style="font-size: 1rem; color:#000;" data-bs-target="#pessoa"></i>
                            </a>
                            <a href="/deletar-grupos" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal{{$grupos->id}}"  data-tt="tooltip" data-placement="top" title="Deletar">
                                <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                            </a>
 {{--  Modal de Exclusao --}}
 <div class="modal fade" id="modal{{$grupos->id}}" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="color:red;">Confirmação de
                    Exclusão</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align: center">
                Tem certeza que deseja excluir o grupo <p
                    style="color:red;">{{$grupos->nome}}&#63;</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancelar</button>
                <a type="button" class="btn btn-danger"
                    href="/deletar-grupos/{{$grupos->id}}">Confirmar
                    Exclusão</a>
            </div>
        </div>
    </div>
</div>
{{-- Fim Modal de Exclusao --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



<script src="caminho/para/bootstrap/js/bootstrap.bundle.min.js" async defer></script>
<link href="caminho/para/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })






</script>
@endsection
