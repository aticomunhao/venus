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
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                            value="{{ request('nome_pesquisa') }}">
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
                </div>x
            </form>

        </div>

        <hr>

            <table
                class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">

                <thead>
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th>ID</th>
                        <th>NOME DO MÉDIUM</th>
                        <th>FUNÇÃO</th>
                        <th>STATUS PESSOA</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($membro as $membros)
                        <tr>
                            <td>{{ $membros->idm }}</td>
                            <td>{{ $membros->nome_completo }}</td>
                            <td>{{ $membros->nome_funcao }}</td>
                            <td>{{ $membros->status }}</td>
                            <td>

                                @if($membros->status == 'Inativado')
                                <button href="/editar-membro/{{ $id }}/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Editar" disabled>
                                    <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                </button>
                                @else
                                <a href="/editar-membro/{{ $id }}/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                @endif
                                @if($membros->status == 'Inativado')
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#confirmacaoDelecao{{ $membros->idm }}" data-tt="tooltip"
                                    data-placement="top" title="Inativar" disabled>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                                @else
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmacaoDelecao{{ $membros->idm }}">
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        <div class="modal fade" id="confirmacaoDelecao{{ $membros->idm }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color:#DC4C64">
                                        <h5 class="modal-title" id="exampleModalLabel" style="color:white">Inativação de
                                            membro </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="text-align: center; ">
                                        Tem certeza que deseja inativar o membro<br /><span
                                            style="color:#DC4C64; font-weight: bold;">{{ $membros->nome_completo }}</span>&#63;
                                    </div>
                                    <div class="modal-footer mt-3">
                                        <button type="button" class="btn btn-danger"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <a type="button" class="btn btn-primary"
                                            href="/deletar-membro/{{ $id }}/{{ $membros->idm }}">Confirmar</a>
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
