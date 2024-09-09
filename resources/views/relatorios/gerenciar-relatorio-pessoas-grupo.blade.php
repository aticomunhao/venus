@extends('layouts.app')

@section('title')
    Relatório de Trabalhadores
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            RELATÓRIO DE TRABALHADORES
        </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <br />
                    <form action="{{ url('/gerenciar-relatorio-pessoas-grupo') }}" method="get">
                        <div class="row">
                            <div class="col">
                                Setor
                                <select class="form-select select2" id="setor" name="setor">
                                    <option value="">Todos</option>
                                    @foreach ($setor as $setores)
                                        <option value="{{ $setores->id }}" {{ request('setor') == $setores->id ? 'selected' : '' }}>
                                            {{ $setores->nome }} - {{ $setores->sigla }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                Grupo
                                <select class="form-select select2" id="grupo" name="grupo">
                                    <option value="">Todos</option>
                                    @foreach ($grupo as $grupos)
                                        <option value="{{ $grupos->id }}" {{ request('grupo') == $grupos->id ? 'selected' : '' }}>
                                            {{ $grupos->nome_grupo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Dia
                                <select class="form-select select2" id="dia" name="dia">
                                    <option value="">Todos</option>
                                    @foreach ($dias as $dia)
                                        <option value="{{ $dia->id }}" {{ request('dia') == $dia->id ? 'selected' : '' }}>
                                            {{ $dia->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                Nome do Membro
                                <select class="form-select select2" id="nome" name="nome">
                                    <option value="">Todos</option>
                                    @foreach ($atendentesParaSelect as $atendente)
                                        <option value="{{ $atendente->ida }}" {{ request('nome') == $atendente->ida ? 'selected' : '' }}>
                                            {{ $atendente->nm_4 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mt-3">
                                <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                                <a href="{{ url('/gerenciar-relatorio-pessoas-grupo') }}" class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;">Limpar</a>
                            </div>
                        </div>
                    </form>
                    <hr />
                </div>
            </div>
        </div>

        <table class="table table-striped table-bordered border-secondary table-hover align-middle">
            <thead style="text-align: center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th>NÚMERO</th>
                    <th>NOME DO MEMBRO</th>
                    <th>GRUPO</th>
                    <th>FUNÇÃO</th>
                    <th>DIA</th>
                    <th>HORA INÍCIO</th>
                    <th>HORA FIM</th>
                    <th>SETOR</th>
                </tr>
            </thead>
            <tbody style="font-size: 14px; color:#000000; text-align: center;">
                @foreach ($membros as $membro)
                    <tr>
                        <td>{{ $membro->id }}</td>
                        <td>{{ $membro->nome_completo }}</td>
                        <td>{{ $membro->grupo_nome }}</td>
                        <td>{{ $membro->nome_funcao }}</td>
                        <td>{{ $membro->dia_nome }}</td>
                        <td>{{ $membro->h_inicio }}</td>
                        <td>{{ $membro->h_fim }}</td>
                        <td>{{ $membro->setor_nome }} - {{ $membro->setor_sigla }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $membros->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
