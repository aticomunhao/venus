@extends('layouts.app')

@section('title', 'Relatório de Atendimentos')

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size: 22px; text-align: left; color: #333; font-family: 'Calibri', sans-serif;">
            Relatório de Atendimentos
        </h4>
        <br>
        <!-- Filtro por datas e botões -->
        <form method="GET" action="{{ url('/gerenciar-relatorio-atendimento') }}">
            <div class="row align-items-center mb-4">
                <!-- Data de Início -->
                <div class="col-md-3">
                    <label for="dt_inicio" class="form-label">Data de Início</label>
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ $dt_inicio }}">
                </div>
                <!-- Data de Fim -->
                <div class="col-md-3">
                    <label for="dt_fim" class="form-label">Data de Fim</label>
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{ $dt_fim }}">
                </div>
                {{-- <div class="col-md-3">
                    <label for="status_atendimento" class="form-label">Status</label>
                    <select class="form-select" id="status_atendimento" name="status_atendimento">
                        <option value="6" @if(old('status_atendimento') == 6) selected @endif>Finalizado</option>
                        <option value="7" @if(old('status_atendimento') == 7) selected @endif>Cancelado</option>
                    </select>
                </div> --}}
                <!-- Botões -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-light w-100 me-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                        Pesquisar
                    </button>
                    <a href="{{ url('/gerenciar-relatorio-atendimento') }}" <button type="button"
                        class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px  #000000; margin-top: 27px;">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
        <hr>
        <!-- Tabela de Resumo -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover align-middle">
                <thead class="text-center" style="background-color: #f0f0f0; color: #333;">
                    <tr>
                        <th>ATENDIDOS</th>
                        <th>CANCELADOS</th>
                        <th>MENORES ATENDIDOS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $finalizados }}</td>
                        <td class="text-center">{{ $cancelados }}</td>
                        <td class="text-center">{{ $menores }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    </div>
@endsection
