@extends('layouts.app')
@section('content')
    <br>
    <div class="container">
        <div class="card" style="text-align: left; color: gray; font-family:calibri">
            <div class="card-header">
                Gerenciar Critério de Atividade
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    {{-- Formulário de Pesquisa --}}
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                        <form action="{{ route('index.tipo_criterio_controller') }}" method="GET"
                            class="d-flex flex-wrap gap-2">
                            <div class="col-auto">
                                <label for="idsearch">Nome Criterio</label>
                                <input type="text" class="form-control" name="search" placeholder="Pesquisar Critério"
                                    id="idsearch" value="{{ $pesquisa_search ?? '' }}">
                            </div>
                            <div class="col-auto">
                                <label for="tipo" class="form-label">Tipo de Critério</label>
                                <select class="form-select" id="tipo" name="tipo_criterio">
                                    <option value="">Todos</option>
                                    @foreach ($tipo_valores as $valor)
                                        <option value="{{ $valor }}"
                                            {{ $valor === $pesquisa_tipo_criterio ? 'selected' : '' }}>
                                            {{ $valor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="tipo" class="form-label">Status</label>
                                <select class="form-select" id="tipo" name="pesquisa_status">
                                    <option value="">Todos</option>
                                    @foreach ($status as $valor)
                                        <option value="{{ $valor['valor'] }}"
                                            {{ $valor['valor'] === $pesquisa_status ? 'selected' : '' }}>
                                            {{ $valor['nome'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-secondary">Pesquisar</button>
                            </div>
                        </form>

                        <div class="col-auto">
                            <a href="{{ route('criar.tipo_criterio_controller') }}" class="btn btn-primary">Adicionar
                                Critério</a>
                        </div>
                    </div>
                </h5>
                <p class="card-text">
                <div class="row" style="text-align:center;">
                    <div class="table">
                        <table
                            class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                            <th scope="col">ID</th>
                            <th scope="col">DESCRIÇÃO</th>
                            <th scope="col">TIPO VALOR</th>
                            <th scope="col">AÇÕES</th>

                            @foreach ($tipos_criterio as $tipo_criterio)
                                <tr>
                                    <td>{{ $tipo_criterio->id }}</td>
                                    <td>{{ $tipo_criterio->descricao }}</td>
                                    <td>{{ $tipo_criterio->tipo_valor }}</td>
                                    <td>
                                        <a href="{{ route('editar.tipo_criterio_controller', $tipo_criterio->id) }}"
                                            type="button" class="btn btn-outline-warning btn-sm tooltips">
                                            <span class="tooltiptext">Editar</span>
                                            <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                            data-bs-target="#confirmacaoDelecaoModal{{ $tipo_criterio->id }}">
                                            <span class="tooltiptext">Deletar</span>
                                            <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                        </button>
                                        <div class="modal fade" id="confirmacaoDelecaoModal{{ $tipo_criterio->id }}"
                                            tabindex="-1" aria-labelledby="inativarLabel{{ $tipo_criterio->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color:#DC4C64;color:white">
                                                        <h1 class="modal-title fs-5"
                                                            id="inativarLabel{{ $tipo_criterio->id }}">Confirmar
                                                            Inativação</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label for="recipient-name" class="col-form-label"
                                                            style="font-size:17px">
                                                            Tem certeza que deseja inativar:<br />
                                                            <span
                                                                style="color:#DC4C64; font-weight: bold;">{{ $tipo_criterio->id }}
                                                                - {{ $tipo_criterio->descricao }} </span>&#63;
                                                        </label>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" data-bs-dismiss="modal"
                                                            class="btn btn-danger">Cancelar</button>
                                                        <a href="{{ route('inativar.tipo_criterio_controller', $tipo_criterio->id) }}"
                                                            class="btn btn-primary">Confirmar</a>
                                                    </div>
                                                    <br>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                </p>
            </div>
        </div>
    </div>
@endsection
