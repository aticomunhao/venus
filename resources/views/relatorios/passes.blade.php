

@extends('layouts.app')
@section('title')
    Relatório de Passes
@endsection
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            RELATÓRIO DE PASSES PTD-PTI-PPH
        </h4>
        <br>
        <form action="/relatorio-passes" method="GET">
            <div class="row align-items-center">
                <!-- Data de Início -->
                <div class="col-md-2 mb-2">
                    <label for="dt_inicio" class="form-label">Data de Início</label>
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio"  value="{{$dt_inicio}}">
                </div>
                <!-- Data de Fim -->
                <div class="col-md-2 mb-2">
                    <label for="dt_fim" class="form-label">Data de Fim</label>
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{$dt_fim}}">
                </div>
                <!-- Tratamento -->
                <div class="col-md-2 mb-2">
                    <label for="tratamento" class="form-label">Tratamento</label>
                    <select class="form-select select2" id="tratamento" name="tratamento" data-width="100%">
                        <option value="">Todos</option>
                        @foreach ($trata as $tratas)
                            <option value="{{ $tratas->id }}" {{ request('tratamento') == $tratas->id ? 'selected' : '' }}>
                                {{ $tratas->sigla }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Botão Pesquisar -->
                <div class="col-md-1 mb-2">
                    <button type="submit" class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin-top: 27px;">
                        Pesquisar
                    </button>
                </div>
                <!-- Botão Limpar -->
                <div class="col-md-1 mb-2">
                    <a href="/relatorio-passes">
                        <button type="button" class="btn btn-light w-100"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin-top: 27px;">
                            Limpar
                        </button>
                    </a>
                </div>
            </div>
        </form>

        <br />
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center; background-color: #d6e3ff; font-size: 14px; color: #000000;">
                        <tr>
                            <th class="col-3">TRATAMENTO</th>
                            <th class="col-3">SIGLA</th>
                            <th class="col-1">ASSISTIDOS</th>
                            <th class="col-1">ACOMPANHANTES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color: #000000; text-align: center;">
                        @foreach ($passe as $passes)
                            <tr>
                                    <td>{{ $passes->tnome }}</td>
                                    <td>{{ $passes->tsigla }}</td>
                                    <td>{{ $passes->assist  }}</td>
                                    <td>{{ $passes->acomp  }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


