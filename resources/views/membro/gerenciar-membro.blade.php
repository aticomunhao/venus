@extends('layouts.app')
@section('title', 'Gerenciar Membros')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR GRUPO -
            {{ Str::upper($grupo->nome) }} ({{ $grupo->nsigla }}) - {{ Str::upper($grupo->dia) }}
            {{ date('H:i', strtotime($grupo->h_inicio)) }}/{{ date('H:i', strtotime($grupo->h_fim)) }}
        </h4>

        <div class="col-12">
            <form action="/gerenciar-membro/{{ $id }}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class=" col-xxl-4 col-lg-12">
                        <label for="nome_pesquisa">Nome</label>
                        <select class="form-select select2" id="nome_pesquisa" name="nome_pesquisa" data-width="100%">
                            <option value=""></option>
                            @foreach ($membro as $membros)
                                <option value="{{ $membros->nome_completo }}"
                                    {{ request('nome_pesquisa') == $membros->nome_completo ? 'selected' : '' }}>
                                    {{ $membros->nome_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class=" col-xxl-4 col-lg-12">
                        <label for="status">Status</label>
                        <select class="form-select select2" id="status" name="status" data-width="100%">
                            <option value=""></option>
                            @foreach ($statu as $status)
                                <option value="{{ $status->nome }}"
                                    {{ request('status') == $status->nome ? 'selected' : '' }}>
                                    {{ $status->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xxl-1 col-lg-4 mt-3">
                        <input class="btn btn-light col-12 btn-sm mt-3"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;" type="submit"
                            value="Pesquisar">
                    </div>
                    <div class="col-xxl-1 col-lg-4 mt-3">
                        <a href="/gerenciar-membro/{{ $id }}" class="btn btn-light col-12 btn-sm mt-3"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;" type="submit"
                            type="button">Limpar</a>
                    </div>
                    <div class="col-xxl-1 col-lg-4 mt-3">
                        <a href="/gerenciar-grupos-membro" class="btn btn-primary col-12 btn-sm mt-3"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;"
                            type="button">Retornar</a>
                    </div>
                    <!-- Botão para abrir o modal -->
                    @if (in_array(13, session()->get('usuario.acesso')))
                    <div class="col-xxl-1 col-lg-4 mt-3">
                        <button type="button" class="btn btn-success col-12 btn-sm mt-3"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;"data-bs-toggle="modal"
                            data-bs-target="#meuModal">
                            OPÇÕES
                        </button>
                     @endif
                    </div>
                </div>
        </div>
        </form>
    </div>
    <hr>
    Total de Membros: {{ $membro->count() }}

    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
        <thead>
            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                <th>Nº</th>
                <th>NOME</th>
                <th>FUNÇÃO</th>
                <th>STATUS</th>
                @if (in_array(13, session()->get('usuario.acesso')))
                    <th>AÇÕES</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($membro as $membros)
                <tr>
                    <td>{{ $membros->nr_associado }}</td>
                    <td>{{ $membros->nome_completo }}</td>
                    <td>{{ $membros->nome_funcao }}</td>
                    <td>{{ $membros->status }}</td>
                    @if (in_array(13, session()->get('usuario.acesso')))
                        <td>
                            <!-- Botão para editar -->
                            @if ($membros->status = 'Inativo' && in_array(13, session()->get('usuario.acesso')))
                                <a href="/editar-membro/{{ $id }}/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            @else
                                <a href="/editar-membro/{{ $id }}/{{ $membros->idm }}"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            @endif
                            <!-- Botão para inativar -->
                            @if ($membros->status == 'Inativo' && in_array(13, session()->get('usuario.acesso')))
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmInactivate{{ $membros->idm }}">
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmInactivate{{ $membros->idm }}">
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @endif
                            @if (in_array(13, session()->get('usuario.acesso')))
                                <!-- Botão para deletar -->
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmDelete{{ $membros->idm }}">
                                    <span class="tooltiptext">Deletar</span>
                                    <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                                </button>
                        </td>
                    @endif
                </tr>
            @endif

            <!-- Modal de confirmação para inativar -->
            <div class="modal fade" id="confirmInactivate{{ $membros->idm }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#DC4C64">
                            <h5 class="modal-title" id="exampleModalLabel" style="color:white">Inativar membro</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body " style="text-align: center;">
                            Tem certeza que deseja inativar o membro<br /><span style="color:#DC4C64; font-weight: bold;">
                                {{ $membros->nome_completo }}</span>?
                            <form action="{{ route('membro.inactivate', ['idcro' => $id, 'id' => $membros->idm]) }}"
                                method="POST">
                                @csrf
                                <center>
                                    <div class="col-10">
                                        <label for="data_inativacao" class="form-label mt-3">Escolha a data de
                                            inativação:</label>
                                        <input type="date" name="data_inativacao"
                                            id="data_inativacao{{ $membros->idm }}" class="form-control mb-3" required>
                                    </div>
                                </center>
                                <div class="modal-footer mt-3 ">
                                    <button type="button" class="btn btn-danger"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de confirmação para deletar -->
            <div class="modal fade" id="confirmDelete{{ $membros->idm }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#DC4C64">
                            <h5 class="modal-title" id="exampleModalLabel" style="color:white">Deletar membro
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="text-align: center;">
                            Tem certeza que deseja deletar o membro<br /><span style="color:#DC4C64; font-weight: bold;">
                                {{ $membros->nome_completo }}</span>?
                        </div>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('membro.destroy', ['idcro' => $id, 'id' => $membros->idm]) }}"
                                method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-primary">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
    <!-- Modal de opções do grupo -->
    <div class="modal fade" id="meuModal" tabindex="-1" aria-labelledby="meuModalLabel" aria-hidden="true">
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="meuModalLabel">Opções do Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    @if (in_array(13, session()->get('usuario.acesso')))
                        @if ($grupo->modificador == 4)
                            <a href="/ferias-reuniao/{{ $id }}/2" class="btn btn-warning btn-sm w-100 mb-2">
                                Retomar de Férias
                            </a>
                        @else
                            <a href="/ferias-reuniao/{{ $id }}/1" class="btn btn-danger btn-sm w-100 mb-2">
                                Declarar Férias
                            </a>
                        @endif
                    @endif

                    @if (in_array(13, session()->get('usuario.acesso')))
                        <a href="/criar-membro-grupo/{{ $id }}" class="btn btn-success btn-sm w-100 mb-2">
                            Novo membro +
                        </a>
                        <a href="/selecionar-membro/{{ $id }}" class="btn btn-warning btn-sm w-100 mb-2">
                            Transferir Membros
                        </a>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    {{ $membro->links('pagination::bootstrap-5') }}
    </div>

    <style>
@endsection
