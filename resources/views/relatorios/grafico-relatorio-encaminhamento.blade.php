@extends('layouts.app')

@section('title', 'Encaminhamentos por Tipo - Mês')

@section('content')
<div class="container-fluid">
    <h4 class="card-title mb-3" style="font-size:20px; color:gray;">Encaminhamentos por Tipo de Tratamento (por mês)</h4>

    {{-- Gráfico --}}
    <div style="width: 350px; height: 200px; margin:auto;">
        <canvas id="chartPorTipoMes"></canvas>
    </div>

    {{-- Botão de filtro por mês --}}
    <form method="GET" class="mt-3 d-flex justify-content-center gap-2">
        <input type="month" name="dt_inicio" class="form-control" value="{{ $dt_inicio->format('Y-m') }}">
        <button class="btn btn-primary">Filtrar</button>
    </form>
</div>

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
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endsection
