@extends('layouts.app')

@section('title')
    Gerenciar Reuniões 
@endsection

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

    <div class="container";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR REUNIÕES </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('remdex') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">
                            <div class="col-2">Dia
                                <select class="form-select semana" id="4" name="semana" type="number">

                                    @foreach ($tpdia as $dias)
                                        <option value="{{ $dias->idtd }}" {{ $dias->idtd == $semana ? 'selected' : '' }}>
                                            {{ $dias->nomed }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">Grupo
                                <input class="form-control" type="text" id="" name="grupo"
                                    value="{{ $grupo }}">
                            </div>
                            <div class="col-2">Status
                                <select class="form-select status" id="4" name="status" type="number">

                                    @foreach ($situacao as $situ)
                                        <option value="{{ $situ->ids }}" {{ $situ->ids == $status ? 'selected' : '' }}>
                                            {{ $situ->descs }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col"><br />
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                                <a href="/gerenciar-reunioes"><input class="btn btn-light btn-sm me-md-2"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                        value="Limpar"></a>
                                <a href="/criar-reuniao"><input class="btn btn-success btn-sm me-md-2"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" autofocus
                                        value="Nova reunião &plus;"></a>
                    </form>
                </div>
            </div>
            <br />
        </div style="text-align:right;">
        <hr />
        <div class="table">Total reuniões: {{ $contar }}
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">Nr</th>
                        <th class="col-2">GRUPO</th>
                        <th class="col">DIA</th>
                        <th class="col">SALA</th>
                        <th class="col-2">TRATAMENTO</th>
                        <th class="col">HORÁRIO INÍCIO</th>
                        <th class="col">HORÁRIO FIM</th>
                        <th class="col">MAX ATENDIDOS</th>
                        <th class="col">STATUS</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align: center;">
                    <tr>
                        @foreach ($reuniao as $reuni)
                            <td>{{ $reuni->idr }}</td>
                            <td>{{ $reuni->nomeg }}</td>
                            <td>{{ $reuni->nomed }}</td>
                            <td>{{ $reuni->numero }}</td>
                            <td>{{ $reuni->tstd }}</td>
                            <td>{{ date('H:i:s', strtotime($reuni->h_inicio)) }}</td>
                            <td>{{ date('H:i:s', strtotime($reuni->h_fim)) }}</td>
                            <td>{{ $reuni->max_atend }}</td>
                            <td>{{ $reuni->descst }}</td>
                            <td>
                                <a href="/editar-reuniao/{{ $reuni->idr }}"><button type="button"
                                        class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                        title="Editar"><i class="bi bi-pencil"
                                            style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/visualizar-reuniao/{{ $reuni->idr }}"><button type="button"
                                        class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                        title="Visualizar"><i class="bi bi-search"
                                            style="font-size: 1rem; color:#000;"></i></button></a>

                                <button type="button" class="btn btn-outline-danger btn-sm" data-tt="tooltip"
                                    data-placement="top" title="Inativar" data-bs-toggle="modal"
                                    data-bs-target="#modal{{ $reuni->idr }}">
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;">



                            </td>
                    </tr>
                    

                     <!-- Modal de Exclusao -->
                      <div class="modal fade" id="modal{{ $reuni->idr }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color:#DC4C64">
                                    <h5 class="modal-title" id="exampleModalLabel" style="color:white">Confirmação de inativação </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="text-align: center; ">
                                    Tem certeza que deseja inativar a reunião de <br /><span style="color:#DC4C64; font-weight: bold;">{{ $reuni->nomeg }}</span>&#63;
                                </div>
                                <div class="modal-footer mt-2">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                    <a type="button" class="btn btn-primary" href="/excluir-pessoa/{{ $reuni->idr }}">Confirmar</a>
                                </div>
                            </div>
                        </div>
                    </div> 
                    
                {{-- Fim Modal de Exclusao --}}
           
                    @endforeach
                </tbody>
            </table>
        </div class="d-flex justify-content-center">
        {{ $reuniao->withQueryString()->links() }}
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })



        if (typeof {{ $semana }} === 'undefined') { //Deixa o select status como padrao vazio
            $(".semana").prop("selectedIndex", -1);
        }

        if (typeof {{ $status }} === 'undefined') { //Deixa o select status como padrao vazio
            $(".status").prop("selectedIndex", -1);
        }
    </script>
@endsection

@section('footerScript')
@endsection
