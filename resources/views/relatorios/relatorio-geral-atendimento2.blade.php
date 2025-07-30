@extends('layouts.app')
@section('title')
    Relatório de Disponibilidade de Vagas
@endsection
@section('content')
    <br />
    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>
    <div class="container">
        <form method="GET" action="{{ url('/relatorio-geral-atendimento2') }}">
            <div class="row align-items-end mb-4">
                <div class="col-md-2">
                    <label for="ano" class="form-label">Ano</label>
                    <select class="form-select" id="ano" name="ano">
                        @php
                            $anoAtual = now()->year;
                            $anoInicio = 2024;
                        @endphp
                        @for ($ano = $anoAtual; $ano >= $anoInicio; $ano--)
                            <option value="{{ $ano }}" @if (request('ano') == $ano) selected @endif>
                                {{ $ano }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="tipo_tratamento" class="form-label">Tipo Tratamento</label>
                    <select class="form-select" id="tipo_tratamento" name="tipo_tratamento">
                        <option value="1" @if (request('tipo_tratamento') == 1) selected @endif>PTD</option>
                        <option value="2" @if (request('tipo_tratamento') == 2) selected @endif>PTI</option>
                        <option value="4" @if (request('tipo_tratamento') == 4) selected @endif>PROAMO</option>
                        <option value="6" @if (request('tipo_tratamento') == 6) selected @endif>Integral</option>
                        <option value="5" @if (request('tipo_tratamento') == 5) selected @endif>Todos</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-light w-100 me-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                        Pesquisar
                    </button>
                    <a href="{{ url('/relatorio-geral-atendimento2') }}" class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                        Limpar
                    </a>
                </div>
            </div>
        </form>


        <br />
        <div class="card">
            <div class="card-header">ESTATÍSTICA FREQUÊNCIA</div>
            <div class="card-body" id="printTable">
                <table class="table table-striped table-bordered border-secondary table-hover align-middle">
                    <thead class="text-center">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>TIPOS</th>
                            @foreach (array_keys($dadosChart) as $mes)
                                <th>{{ $mes }}</th>
                            @endforeach
                            <th>MÉDIA</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="font-size: 14px; color:#000000;">
                        <tr>
                            <td>TOTAL</td>
                            @foreach ($dadosChart as $dado)
                                <td>{{ $dado['Total'] ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'Total'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores)) : '--' }}</td>
                            <td>{{ round(array_sum(array_column($dadosChart, 'Total'))) }}</td>
                        </tr>
                        <tr>
                            <td rowspan="2">AUSENTES</td>
                            @foreach ($dadosChart as $dado)
                                <td>{{ $dado['Ausentes'] ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'Ausentes'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores)) : '--' }}</td>
                            <td>{{ round(array_sum(array_column($dadosChart, 'Ausentes'))) }}</td>
                        </tr>
                        <tr>
                            @foreach ($dadosChart as $dado)
                                <td>{{ isset($dado['PCT Ausentes']) ? $dado['PCT Ausentes'] . '%' : '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'PCT Ausentes'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) . '%' : '--' }}
                            </td>
                            <td>--</td>
                        </tr>

                        <tr>
                            <td rowspan="2">PRESENTES</td>
                            @foreach ($dadosChart as $dado)
                                <td>{{ $dado['Presenças'] ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'Presenças'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores)) : '--' }}</td>
                            <td>{{ round(array_sum(array_column($dadosChart, 'Presenças'))) }}</td>
                        </tr>
                        <tr>
                            @foreach ($dadosChart as $dado)
                                <td>{{ isset($dado['PCT Presenças']) ? $dado['PCT Presenças'] . '%' : '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'PCT Presenças'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) . '%' : '--' }}
                            </td>
                            <td>--</td>
                        </tr>
                        <tr>
                            <td>ALTA</td>
                            @foreach ($dadosChart as $dado)
                                <td>{{ $dado['Alta'] ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'Alta'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores)) : '--' }}</td>
                            <td>{{ round(array_sum(array_column($dadosChart, 'Alta'))) }}</td>
                        </tr>

                        {{-- TRANSFERIDOS --}}
                        <tr>
                            <td>TRANSFERIDOS</td>
                            @foreach ($dadosChart as $dado)
                                <td>{{ $dado['Transferidos'] ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'Transferidos'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores)) : '--' }}</td>
                            <td>{{ round(array_sum(array_column($dadosChart, 'Transferidos'))) }}</td>
                        </tr>

                        {{-- DESISTÊNCIAS --}}
                        <tr>
                            <td>DESISTÊNCIAS</td>
                            @foreach ($dadosChart as $dado)
                                <td>{{ $dado['Desistência'] ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosChart, 'Desistência'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores)) : '--' }}</td>
                            <td>{{ round(array_sum(array_column($dadosChart, 'Desistência'))) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-5">
            <div class="card-header">CAPACIDADE E VAGAS</div>
            <div class="card-body">
                <table class="table table-striped table-bordered border-secondary table-hover align-middle mt-3">
                    <thead class="text-center">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>VAGAS ASSISTIDOS</th>
                            <th>VAGAS TRABALHADORES</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="font-size: 14px; color:#000000;">
                        <tr>
                            <TD>
                                {{ $maxAtend->max_atend }}
                            </TD>
                            <td>
                                {{ $maxAtend->max_trab }}
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br /><br /><br /><br />

    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }
    </style>

    <script>
        $(document).ready(function() {
            let tratamento = @JSON(request('tipo_tratamento'));

            if (tratamento === null) {
                $('#tipo_tratamento').prop('selectedIndex', 4)
            }

        });
    </script>
@endsection
