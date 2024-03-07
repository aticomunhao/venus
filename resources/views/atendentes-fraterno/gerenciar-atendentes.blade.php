@extends('layouts.app')

@section('title')
    Gerenciar Atendente Fraterno
@endsection

@section('content')
    <div class="container";>

        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ATENDENTES FRATERNOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <form action="{{ route('list') }}" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col-3">Nome
                            <input class="form-control" type="text" maxlength="45"
                                oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="1" name="nome" value="{{ $nome }}">
                        </div>
                        <div class="col-2">CPF
                            <input class="form-control" type="text" maxlength="11"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="2" name="cpf" value="{{ $cpf }}">
                        </div>

                        <div class="col-2">Status
                            <select class="form-select status" id="4" name="status" type="numeric" >
                                <option value="1" {{ $status == 1 ? 'selected' : '' }}>Ativo</option>
                                <option value="2" {{ $status == 2 ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                value="Pesquisar">
                            <a href="/gerenciar-atendentes"><input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>
                </form>
                <a href="/criar-atendente"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                        type="button" value="Novo atendente +"></a>
            </div>
        </div>
    </div>

    <hr>
    Quantidade filtrada: {{ $soma }}
    <div class="table">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
            <thead style="text-align: center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th class="col">ID</th>
                    <th class="col">NOME</th>
                    <th class="col">CPF</th>
                    <th class="col">NASCIMENTO</th>
                    <th class="col">SEXO</th>
                    <th class="col">DDD</th>
                    <th class="col">CELULAR</th>
                    <th class="col">STATUS</th>
                    <th class="col">AÇÕES</th>
                </tr>
            </thead>
            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                @foreach ($atendente as $atendentes)
                    <tr>
                        <td scope="">{{ $atendentes->id }}</td>
                        <td scope="" style="text-align: left;">{{ $atendentes->nome_completo }}</td>
                        <td scope="">{{ str_pad($atendentes->cpf, 11, '0', STR_PAD_LEFT) }}</td>
                        <td scope="">{{ date('d/m/Y', strtotime($atendentes->dt_nascimento)) }}</td>
                        <td scope="">{{ $atendentes->tipo }}</td>
                        <td scope="">{{ $atendentes->ddd }}</td>
                        <td scope="">{{ $atendentes->celular }}</td>
                        <td scope="">{{ $atendentes->tpsta }}</td>
                        <td scope="">


                            <a href="/editar-atendente/{{ $atendentes->id }}" type="button"
                                class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                title="Editar">
                                <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                            </a>
                            <a href="/visualizar-atendente/{{ $atendentes->id }}" type="button"
                                class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                title="Visualizar">
                                <i class="bi bi-search" style="font-size: 1rem; color:#000;" data-bs-target="#pessoa"></i>
                            </a>
                             <!-- Botão de Exclusão com Modal de Confirmação -->
                             <a href="/excluir-atendente/ class="btn btn-outline-danger btn-sm data-bs-toggle="modal"
                             data-bs-target="#confirmarExclusao{{ $atendentes->id }}" data-tt="tooltip"
                             data-placement="top" title="Excluir"  class="btn btn-outline-danger btn-sm">
                             <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                         </a>


                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

                            <script>
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
                                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl)
                                })

                                if (typeof {{ $status }} === 'undefined') { //Deixa o select status como padrao vazio
                                    $(".status").prop("selectedIndex", -1);
                                }

                            </script>

                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>


    </div>
    </div>
    </div>
@endsection

@section('footerScript')
@endsection
