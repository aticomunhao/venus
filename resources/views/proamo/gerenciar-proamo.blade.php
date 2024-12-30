@extends('layouts.app')
@section('title', 'Gerenciar Assistidos Proamo')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ASSISTIDOS
            PROAMO
        </h4>

        <div class="col-12">
            <form action="/gerenciar-proamo" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label for="nome_pesquisa" class="form-label">Nome</label>
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                            value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="grupo" class="form-label">Grupos</label>
                        <select class="form-select" id="grupo" name="grupo">
                            @foreach ($dirigentes as $dirigente)
                                <option value="{{ $dirigente->id }}"
                                    {{ $dirigente->id == $selected_grupo ? 'selected' : '' }}>{{ $dirigente->nome }} -
                                    {{ $dirigente->dia }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-flex gap-2">
                            <button class="btn btn-light btn-sm" type="submit" style="box-shadow: 1px 2px 5px #000000;">Pesquisar</button>
                            <a href="/gerenciar-proamo" class="btn btn-light btn-sm" style="box-shadow: 1px 2px 5px #000000;">Limpar</a>
                            <a href="/gerenciar-membro/{{ $selected_grupo }}" class="btn btn-primary btn-sm" style="box-shadow: 1px 2px 5px #000000;">Gerenciar Grupo</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <hr>
        <span class="text-danger" style="font-size: 14px;">*Assistidos sem PTD</span>
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover text-center align-middle">
                <thead style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <tr>
                        <th>NOME</th>
                        <th>GRUPO</th>
                        <th>INICIO</th>
                        <th>FIM</th>
                        <th>STATUS</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($encaminhamentos as $encaminhamento)
                        <tr class="{{ $encaminhamento->ptd ? '' : 'table-danger' }}">
                            <td>{{ $encaminhamento->id }}</td>
                            <td>{{ $encaminhamento->nome_completo }}</td>
                            <td>{{ $encaminhamento->nome }}</td>
                            <td>{{ $encaminhamento->h_inicio }}</td>
                            <td>{{ $encaminhamento->h_fim }}</td>
                            <td>{{ $encaminhamento->status }}</td>
                            <td>
                                @if ($encaminhamento->id_status != 1)
                                <button type="button" class="btn btn-outline-warning btn-sm tooltips"
                                data-bs-toggle="modal" data-bs-target="#modalA{{ $encaminhamento->id }}">
                                <span class="tooltiptext">Declarar Alta</span>
                                <i class="bi bi-clipboard-plus" style="font-size: 1rem; color:#000;"></i>
                            </button>
                                @else
                                <button type="button" disabled class="btn btn-outline-warning btn-sm tooltips"
                                data-bs-toggle="modal" data-bs-target="#modalA{{ $encaminhamento->id }}">
                                <span class="tooltiptext">Declarar Alta</span>
                                <i class="bi bi-clipboard-plus" style="font-size: 1rem; color:#000;"></i>
                            </button>
                                @endif

                                <a href="/visualizar-proamo/{{ $encaminhamento->id }}" type="button"
                                    class="btn btn-outline-primary btn-sm tooltips">
                                    <span class="tooltiptext">Visualizar</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;" data-bs-target="#pessoa"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Declarar Alta -->
                        <div class="modal fade" id="modalA{{ $encaminhamento->id }}" tabindex="-1" aria-labelledby="modalLabelA{{ $encaminhamento->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Declarar Alta</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Tem certeza que deseja declarar alta para <span class="text-danger">{{ $encaminhamento->nome_completo }}</span>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <a href="/alta-proamo/{{ $encaminhamento->id }}" class="btn btn-primary">Confirmar</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                      
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
