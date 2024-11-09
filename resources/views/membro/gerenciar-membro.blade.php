@extends('layouts.app')
@section('title', 'Gerenciar Membros')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR GRUPO -
            {{ Str::upper($grupo->nome) }} - {{ Str::upper($grupo->dia) }}
        </h4>

        <div class="col-12">
            <form action="/gerenciar-membro/{{ $id }}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-4">
                        Nome
                        <select class="form-select select2" name="nome_pesquisa">
                            <option value=""></option>
                            @foreach ($membro as $membros)
                                <option value="{{ $membros->nome_completo }}"
                                    {{ request('nome_pesquisa') == $membros->nome_completo ? 'selected' : '' }}>
                                    {{ $membros->nome_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-2">
                        Status
                        <select class="form-select" name="status">
                            <option value=""></option>
                            @foreach ($statu as $status)
                                <option value="{{ $status->nome }}"
                                    {{ request('status') == $status->nome ? 'selected' : '' }}>
                                    {{ $status->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>



                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                        <a href="/gerenciar-membro/{{ $id }}" class="btn btn-light btn-sm me-md-2 offset-4"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;"
                            type="button">Limpar</a>

                        <a href="/gerenciar-grupos-membro" class="btn btn-primary btn-sm me-md-2  offset-1"
                            type="button">Retornar para tela inicial</a>
                        @if ($grupo->modificador == 4)
                            <a href="/ferias-reuniao/{{ $id }}/2"><input class="btn btn-warning btn-sm me-md-2"
                                    style="font-size: 0.9rem;" type="button" value="Retomar de Férias"></a>
                        @else
                            <a href="/ferias-reuniao/{{ $id }}/1"><input class="btn btn-danger btn-sm me-md-2"
                                    style="font-size: 0.9rem;" type="button" value="Declarar Férias"></a>
                        @endif

                        @if( in_array(29, session()->get('usuario.acesso')) )
                        <a href="/criar-membro-grupo/{{ $id }}"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                            type="button" value="Novo membro +"></a>
                            @endif
                    </div>
                </div>
            </form>

        </div>

        <hr>

        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
            <thead>
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th>NºASSOCIADO</th>
                    <th>NOME DO MÉDIUM</th>
                    <th>FUNÇÃO</th>
                    <th>STATUS PESSOA</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($membro as $membros)
                    <tr>
                        <td>{{ $membros->nr_associado }}</td>
                        <td>{{ $membros->nome_completo }}</td>
                        <td>{{ $membros->nome_funcao }}</td>
                        <td>{{ $membros->status }}</td>
                        <td>
                            <!-- Botão para editar -->
                            @if($membros->status == 'Inativo')
                                <button type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Editar" disabled>
                                    <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @else
                                <a href="/editar-membro/{{ $id }}/{{ $membros->idm }}" type="button" class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            @endif

                            <!-- Botão para inativar -->
                            @if($membros->status == 'Inativo')
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#confirmInactivate{{ $membros->idm }}" data-tt="tooltip"
                                    data-placement="top" title="Inativar" disabled>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmInactivate{{ $membros->idm }}">
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @endif

                            <!-- Botão para deletar -->
                            <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                data-bs-target="#confirmDelete{{ $membros->idm }}">
                                <span class="tooltiptext">Deletar</span>
                                <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal de confirmação para inativar -->
                    <div class="modal fade" id="confirmInactivate{{ $membros->idm }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color:#DC4C64">
                                    <h5 class="modal-title" id="exampleModalLabel" style="color:white">Inativar membro</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body " style="text-align: center;">
                                    Tem certeza que deseja inativar o membro<br /><span style="color:#DC4C64; font-weight: bold;">
                                        {{ $membros->nome_completo }}</span>?
                                        <form action="{{ route('membro.inactivate', ['idcro' => $id, 'id' => $membros->idm]) }}" method="POST">
                                            @csrf
                                            <center>
                                            <div class="col-10">
                                                <label for="data_inativacao" class="form-label mt-3">Escolha a data de inativação:</label>
                                                <input type="date" name="data_inativacao" id="data_inativacao{{ $membros->idm }}" class="form-control mb-3" required>
                                            </div>
                                        </center>

                                            <div class="modal-footer mt-3 ">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal de confirmação para deletar -->
                    <div class="modal fade" id="confirmDelete{{ $membros->idm }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color:#DC4C64">
                                    <h5 class="modal-title" id="exampleModalLabel" style="color:white">Deletar membro</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="text-align: center;">
                                    Tem certeza que deseja deletar o membro<br /><span style="color:#DC4C64; font-weight: bold;">
                                        {{ $membros->nome_completo }}</span>?
                                </div>
                                <div class="modal-footer mt-3">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                    <form action="{{ route('membro.destroy', ['idcro' => $id, 'id' => $membros->idm]) }}" method="POST" style="display:inline;">
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
        {{ $membro->links('pagination::bootstrap-5') }}
    </div>
@endsection
