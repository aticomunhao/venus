@extends('layouts.app')

@section('title')
    Gerenciar Plantonistas
@endsection

@section('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container-fluid";>

    <div class="container";>

        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR PLANTONISTAS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <form action="" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col-4">Nome
                            <input class="form-control" type="text" maxlength="45"
                                oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="1" name="nome">
                        </div>
                        <div class="col-2">CPF
                            <input class="form-control" type="text" maxlength="45"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="1" name="cpf">
                        </div>

                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                value="Pesquisar">
                            <a href="/gerenciar-atendentes-plantonistas"><input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>
                </form>
                <a href="/incluir-atendentes-plantonistas"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                        type="button" value="Novo Atendente+"></a>
            </div>
        </div>
    </div>
    <hr>
    Quantidade filtrada:{{ $conta }}
    <div class="table">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
            <thead style="text-align: center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th class="col">NOME</th>
                    <th class="col">CPF</th>
                    <th class="col">STATUS</th>
                    <th class="col">AÇÕES</th>
                </tr>
            </thead>
            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                @foreach ($atendente as $atendentes)
                    <tr>
                        <td>{{ $atendentes->nome_completo }}</td>
                        <td>{{ str_pad($atendentes->cpf, 11, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $atendentes->tipo }}</td>
                        <td scope="">

                            <a href="/editar-atendentes-plantonistas/{{ $atendentes->id }}" type="button"
                                class="btn btn-outline-warning btn-sm"  data-tt="tooltip" data-placement="top" title="Editar">
                                <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                            </a>

                            <a href="/visualizar-atendentes-plantonistas/{{ $atendentes->id }}" type="button"
                                class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                title="Visualizar">
                                <i class="bi bi-search" style="font-size: 1rem; color:#000;" data-bs-target="#pessoa"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <script>
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            </script>
        @endsection