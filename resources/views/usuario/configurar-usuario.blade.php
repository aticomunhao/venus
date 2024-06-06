@extends('layouts.app')

@section('title')
    Cadastrar usuário
@endsection

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

    <br>
    <div class="container">
        <form class="form-horizontal mt-4" method="POST" action="/cad-usuario/inserir">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card m-1">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    GERENCIAR USUÁRIO
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <p>NOME:<strong> {{ $result[0]->nome_completo }}</strong></p>
                                        <p>IDENTIDADE:<strong> {{ $result[0]->idt }}</strong> </p>
                                    </div>
                                    <div class="col">
                                        <p>CPF: <strong> {{ $result[0]->cpf }}</strong> </p>
                                        <p>DATA NASCIMENTO:<strong>
                                                {{ date('d-m-Y', strtotime($result[0]->dt_nascimento)) }}</strong> </p>
                                    </div>
                                    <div class="col">
                                        <p>EMAIL: <strong> {{ $result[0]->email }}</strong> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card m-1">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    SELECIONAR PERFIS
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <input type="hidden" name="idPessoa" value="{{ $result[0]->id }}">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <tr>
                                        <td style="text-align:right;">Ativo</td>
                                        <td>
                                            <input id="ativo" type="checkbox" name="ativo" data-size="small"
                                                data-size="small" data-toggle="toggle" data-onstyle="success"
                                                data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                            <label for="ativo" class="form-check-label"></label>
                                        </td>
                                        <td style="text-align:right;">Bloqueado</td>
                                        <td>
                                            <input id="bloqueado" type="checkbox" name="bloqueado" data-size="small"
                                                data-size="small" data-toggle="toggle" data-onstyle="success"
                                                data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                            <label for="bloqueado" class="form-check-label"></label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">

                                    @foreach ($resultPerfil as $resultPerfils)
                                        <tr>
                                            <td>
                                                {{ $resultPerfils->descricao }}
                                            </td>
                                            <td>
                                                <input id="{{ $resultPerfils->descricao }}" type="checkbox"
                                                    name="{{ $resultPerfils->descricao }}" value="{{ $resultPerfils->id }}"
                                                    data-size="small" data-size="small" data-toggle="toggle"
                                                    data-onstyle="success" data-offstyle="danger" data-onlabel="Sim"
                                                    data-offlabel="Não">
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    {{--  <div class="card m-1">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                SELECIONAR ESTOQUE
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            @foreach ($resultDeposito as $resultDepositos)
                                                <tr>
                                                    <td>
                                                        {{ $resultDepositos->nome }}
                                                    </td>
                                                    <td>
                                                        <input id="{{ $resultDepositos->nome }}" type="checkbox"
                                                            name="{{ $resultDepositos->nome }}"
                                                            value="{{ $resultDepositos->id }}" data-size="small"
                                                            data-size="small" data-toggle="toggle" data-onstyle="success"
                                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  --}}
                    <div class="card m-1">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    SETOR
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input class="form-control" type="text" id="nome_pesquisa"
                                                name="nome_pesquisa">
                                            <label for="floatingTextarea">Pesquisa de Setor</label>
                                        </div>
                                        <br />
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped mb-0" id="myTable"
                                                name="myTable">
                                                @foreach ($resultSetor as $resultSetors)
                                                    <tr>
                                                        <td>
                                                            {{ $resultSetors->nome }}
                                                        </td>
                                                        <td>
                                                            <input id="{{ $resultSetors->nome }}" type="checkbox"
                                                                name="{{ $resultSetors->nome }}"
                                                                value="{{ $resultSetors->id }}" data-size="small"
                                                                data-size="small" data-toggle="toggle"
                                                                data-onstyle="success" data-offstyle="danger"
                                                                data-onlabel="Sim" data-offlabel="Não">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <center>
                                    <div class="row">
                                        <div class="col">
                                            <a href="/gerenciar-usuario ">
                                                <input class="btn btn-danger btn-block col-3" type="button"
                                                    value="Cancelar">
                                            </a>
                                        </div>
                                        <div class="col ">
                                            <button type="submit"
                                                class="btn btn-primary btn-block col-3">Cadastrar</button>
                                        </div>
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#nome_pesquisa").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                console.log(value)
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endsection

@section('footerScript')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ URL::asset('/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/js/pages/form-advanced.init.js') }}"></script>
@endsection
