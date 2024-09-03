@extends('layouts.app')
@section('title')
    Relatório Presença AFI
@endsection
@section('content')
    <div class="container">
        <br />
        <form action="/presenca-afi">
            <div class="row">
                <div class="col-5">
                    Nome
                    <select class="form-select select2" id="afi" name="afi">
                        @foreach($atendentes as $atendente)
                        <option value="{{ $atendente->id_associado }}" {{ $idAssociado ==  $atendente->id_associado  ? 'selected' : '' }}>{{ $atendente->nome_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    Data de Início
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ $dt_inicio }}">
                </div>
                <div class="col-2">
                    Data de fim
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{ $dt_fim }}">
                </div>
                <div class="col mt-3">
                    <input class="btn btn-light btn-sm me-md-2"
                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                    value="Pesquisar">
                <a href="/presenca-afi"><input class="btn btn-light btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                        value="Limpar"></a>
                </div>
            </div>
        </form>
        <br />
        <div class="card">
            <div class="card-header">
                Relatório de Presença - {{ isset($afiSelecionado->nome_completo) ? $afiSelecionado->nome_completo : ''}}
            </div>
            <div class="card-body">


             @if($idAssociado == null)
             <div style="">

                    <center>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="250" height="250" fill="#60bbf0" class="bi bi-search" viewBox="0 0 16 16" style="opacity:80%; margin-top: 100px; ">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                              </svg>
                            <p style="color:#60bbf0; font-weight:bolder; margin-top:20px; font-style:italic;margin-bottom:100px">Faça uma Busca para Gerar o Relatório</p>
                        </div>
                    </center>


             </div>
             @else
             <center>
                 <div class='col-3'>
                     <canvas id="myChart"></canvas>
                    </div>
                </center>

            <br />

                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">REUNIAO</th>
                            <th class="col">DATA</th>
                            <th class="col">DIA</th>
                            <th class="col">HORARIO</th>
                            <th class="col">PRESENÇA</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align:center;">
                        @foreach ($dados as $dado)
                                <tr>
                                    <td>{{ $dado['nome'] }}</td>
                                    <td>{{ date( 'd/m/Y' , strtotime($dado['data']))}}</td>
                                    <td>{{ $dado['dia'] }}</td>
                                    <td>{{ $dado['h_inicio'] }}</td>
                                    @if($dado['presenca'] == 1)
                                    <td style="background-color:#90EE90;">Presente</td>
                                    @else
                                    <td style="background-color:#FA8072;">Ausente</td>
                                    @endif
                                </tr>
                        @endforeach
                    </tbody>
                </table>





             @endif
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            if(@JSON($afiSelecionado) == null){
                $('#afi').prop('selectedIndex', -1)
            }

        });
    </script>

    <script>
        const ctx = document.getElementById('myChart');


        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Faltas',
                    'Presenças',
                ],
                datasets: [{
                    label: 'Numero',
                    data: @JSON($contaFaltas),
                    backgroundColor: [
                        'rgb(217, 83, 79)',
                        'rgb(92, 184, 92)',
                    ],

                }]
            },

        });
    </script>
@endsection
