@extends('layouts.app')
@section('title')
    Relatório de Atendimentos
@endsection
@section('content')
    <br />



    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>



    <div class="container">

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
                <div class="col-md-3">
                    <label for="status_atendimento" class="form-label">Tipo Relatorio</label>
                    <select class="form-select" id="status_atendimento" name="status_atendimento">
                        <option value="1" @if (old('status_atendimento') == 1) selected @endif>Status</option>
                        <option value="2" @if (old('status_atendimento') == 2) selected @endif>Sexo</option>
                    </select>
                </div>
                <!-- Botões -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-light w-100 me-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                        Pesquisar
                    </button>
                    <a href="{{ url('/gerenciar-relatorio-atendimento') }}" class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px  #000000; margin-top: 27px;">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
        <br />
        <div class="card">
            <div class="card-header">
                Relatório de Atendimentos
            </div>
            <div class="card-body" id="printTable">
                <canvas id="myChart"></canvas>

                <table class="table  table-striped table-bordered border-secondary table-hover align-middle mt-5">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>TIPO</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        @foreach ($dadosChart as $key => $dado)
                            <tr>
                                <td> {{ $key }} </td>
                                <td> {{ $dado }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br />
    <br />
    <br />
    <br />


    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }
    </style>

    <script>
        const ctx = document.getElementById('myChart');

        let a = []
        let i = 0
        let atendimentos = @JSON($dadosChart);

        for (const [key, value] of Object.entries(atendimentos)) {
            a[i] = {
                label: `${key}`,
                data: [`${value}`],
                borderWidth: 2,
            }
            i++
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Atendimentos (' + @JSON(date('d/m/Y', strtotime($dt_inicio))) + ' - ' +
                    @JSON(date('d/m/Y', strtotime($dt_fim))) +
                    ')'
                ],
                datasets: a
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,

            }
        });
    </script>
@endsection
