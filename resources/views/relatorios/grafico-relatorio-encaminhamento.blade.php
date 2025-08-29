@extends('layouts.app')

@section('title', 'Gráfico de Encaminhamentos')

@section('content')
    <div class="container">
        <h4 class="card-title mb-3" style="font-size:20px; color:gray; text-align:center;">

        </h4>
        <br>
        {{-- Filtros (fora do card) --}}
        <form method="GET" class="mb-3 row g-3 justify-content-center">
            <div class="col-md-3">
                <label for="dt_inicio" class="form-label">Data Início</label>
                <input type="date" name="dt_inicio" id="dt_inicio" class="form-control"
                    value="{{ request('dt_inicio', $dt_inicio->format('Y-m-d')) }}">
            </div>

            <div class="col-md-3">
                <label for="dt_fim" class="form-label">Data Fim</label>
                <input type="date" name="dt_fim" id="dt_fim" class="form-control"
                    value="{{ request('dt_fim', $dt_fim->format('Y-m-d')) }}">
            </div>

            <div class="col-md-3">
                <label for="tipo" class="form-label">Tipo de Tratamento</label>
                <select name="tipo[]" id="tipo" class="form-select select2" multiple>
                    @foreach ($tiposTratamento as $tipo)
                        <option value="{{ $tipo->id }}"
                            {{ is_array(request('tipo')) && in_array($tipo->id, request('tipo')) ? 'selected' : '' }}>
                            {{ $tipo->descricao }}
                        </option>
                    @endforeach
                    <option value="sem_encaminhamento"
                        {{ is_array(request('tipo')) && in_array('sem_encaminhamento', request('tipo')) ? 'selected' : '' }}>
                        Sem Encaminhamento
                    </option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-success w-100">Pesquisar</button>
                <a href="/grafico-relatorio-encaminhamento" class="btn btn-light w-100"
                    style="box-shadow: 1px 1px 3px #000000;">Limpar</a>
            </div>
        </form>

        {{-- Card único com gráfico e tabela --}}
        <div class="card shadow-sm mt-4">
            <div class="card-header text-start">
                <h5 class="mb-0">Resumo de Atendimentos</h5>
            </div>
            <div class="card-body">

                {{-- Gráfico --}}
                <div style="max-width: 1500px; height: 600px; margin:auto;">
                    <canvas id="chartPorTipoMes"></canvas>
                </div>

                {{-- Tabela --}}
                <div class="mt-5">
                    <table class="table table-striped table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo de Tratamento</th>
                                <th>Total Encaminhamentos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quantidadePorTipo as $item)
                                <tr>
                                    <td>{{ $item->descricao }}</td>
                                    <td>{{ $item->total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">Nenhum encaminhamento encontrado para o período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartPorTipoMes').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($quantidadePorTipo->pluck('descricao')),
                datasets: [{
                    label: 'Qtd Encaminhamentos',
                    data: @json($quantidadePorTipo->pluck('total')),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endsection
