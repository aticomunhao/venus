@extends('layouts.app')

@section('title')
    Gerenciar Atendimentos
@endsection

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <?php
    //echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
    ?>


<div class="container">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ATENDIMENTOS</h4>
    <div class="col-12">
        <form action="{{ route('atedex') }}" class="form-horizontal mt-4" method="GET">
        <div class="row mt-3 justify-content-center">
            <div class="col-12 col-md-2 mb-2">
                <label for="dt_ini">Data início</label>
                <input class="form-control" type="date" id="dt_ini" name="dt_ini" value="{{ $data_inicio ?? now()->toDateString() }}">
            </div>
            <div class="col-12 col-md-3 mb-3">
                <label for="assist">Atendido</label>
                <input class="form-control pesquisa" type="text" id="assist" name="assist" value="{{ $assistido }}">
            </div>
            <div class="col-12 col-md-2 mb-2">
                <label for="status">Status</label>
                <select class="form-select pesquisa" id="status" name="status" type="number">
                <option value=""></option>
                @foreach ($st_atend as $statusz)
                    <option @if (old('status') == $statusz->id) selected="selected" @endif value="{{ $statusz->id }}">{{ $statusz->descricao }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-12 col-md-5 mb-3 d-flex align-items-end justify-content-between">
                <input class="btn btn-light btn-sm w-100" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                <a href="/gerenciar-atendimentos" class="btn btn-light btn-sm w-100" style="box-shadow: 1px 2px 5px #000000; margin:5px;">Limpar</a>       
        </form> 
                <a href="/gerenciar-pessoas" class="btn btn-warning btn-sm w-100" style="box-shadow: 1px 2px 5px #000000; margin:5px;">Nova Pessoa</a>
                <a href="/gerenciar-atendente-dia" class="btn btn-warning btn-sm w-100" style="box-shadow: 1px 2px 5px #000000; margin:5px;">Escala AFI</a>
                <a href="/criar-atendimento" class="btn btn-success btn-sm w-100" style="box-shadow: 1px 2px 5px #000000; margin:5px;">Criar Novo</a>
            </div>
        </div>
        <div class="row">
            <div class="table">Total Atendidos: {{ $contar }}
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">AFI PREF</th>
                            <th class="col">TIPO AFI</th>
                            <th class="col">HORÁRIO CHEGADA</th>
                            <th class="col">PRIOR</th>
                            <th class="col">ATENDIDO</th>
                            <th class="col">REPRESENTANTE</th>
                            <th class="col">ATENDENTE</th>
                            <th class="col">SALA</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                            @foreach ($lista as $listas)
                            <td scope="">{{ $listas->ida }}</td>
                            <td scope="">{{ $listas->nm_4 }}</td>
                            <td scope="">{{ $listas->tipo }}</td>
                            <td scope="">{{ date('d/m/Y H:i:s', strtotime($listas->dh_chegada)) }}</td>
                            <td scope="">{{ $listas->prdesc }}</td>
                            <td scope="">{{ $listas->nm_1 }}</td>
                            <td scope="">{{ $listas->nm_2 }}</td>
                            <td scope="">{{ $listas->nm_3 }}</td>
                            <td scope="">{{ $listas->nr_sala }}</td>
                            <td scope="">{{ $listas->descricao }}</td>
                            <td scope="">
                                <!--<a href="/desce-status/{{ $listas->ida }}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-left-square" style="font-size: 1rem; color:#000;"></i></button></a>
                                    <button class="btn btn-outline-warning btn-sm" style="font-size: 1rem; color:#000;" type="button" id="" data-bs-toggle="modal" data-bs-target="#atendimento{{ $listas->ida }}"><i class="bi bi-person" style="font-size: 1rem; color:#000;"></i></button>
                                    @include('recepcao-AFI.popUp-sel-atendente')
                                    <a href="/sobe-status/{{ $listas->ida }}"><button type="button" class="btn btn-outline-warning btn-sm"><i class="bi bi-caret-right-square" style="font-size: 1rem; color:#000;"></i></button></a>-->
                                <a href="/editar-atendimento/{{ $listas->ida }}"><button type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Editar"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/visualizar-atendimentos/{{ $listas->idas }}"><button type="button" class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top" title="Visualizar"><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/cancelar-atendimento/{{ $listas->ida }}"><button type="button" class="btn btn-outline-danger btn-sm" data-tt="tooltip" data-placement="top" title="Cancelar"><i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
            {{ $lista->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>    
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
            let hoje =  @json($now);
            let assistido =  @json($assistido);
            let situacao =  @json($situacao);

            if(assistido != null || situacao != null){
                $('#dt_ini').val("")
            }
        $('.pesquisa').change(function(){
            let assis =  $('#assist').val()
            let status = $('#status').prop('selectedIndex')

            if(assis == '' && status == 0){
               $('#dt_ini').val(hoje)

            }else{
               $('#dt_ini').val("")

            }

        })

    </script>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
