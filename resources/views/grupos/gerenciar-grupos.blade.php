@extends('layouts.app')

@section('title') Gerenciar Grupos @endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet">

<div class="container-fluid">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR GRUPOS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <form action="{{route('nomes')}}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-3">Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar nome {{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-3">Setor
                        <select class="form-select select2" name="nome_setor">
                            <option value="">Selecione o Setor</option>
                            @foreach ($setores as $setor)
                                <option value="{{ $setor->nome }}" {{ request('nome_setor') == $setor->nome ? 'selected' : '' }}>{{ $setor->nome }}</option>
                            @endforeach
                        </select>
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
                        <th class="col">SETOR</th>
                        <th class="col">STATUS GRUPO</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align:center;">
                    @foreach ($grupo as $grupos)
                    <tr>
                        <td> {{$grupos->nome}} </td>
                        <td> {{date('d/m/Y', strtotime($grupos->data_inicio))}} </td>
                        <td> {{$grupos->data_fim}} </td>
                        <td> {{$grupos->nm_tipo_grupo}} </td>
                        <td> {{$grupos->nm_setor}} </td>
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
                                        <div class="modal-header" style="background-color:#DC4C64">
                                            <h5 class="modal-title" id="exampleModalLabel" style="color:white">Exclusão de grupo </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" style="text-align: center; ">
                                            Tem certeza que deseja excluir o grupo<br /><span style="color:#DC4C64; font-weight: bold;">{{ $grupos->nome }}</span>&#63;
                                        </div>
                                        <div class="modal-footer mt-3">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-primary" href="/deletar-grupos/{{ $grupos->id }}">Confirmar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        {{--  Fim do modal de Exclusao --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endsection
