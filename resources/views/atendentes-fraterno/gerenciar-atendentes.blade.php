@extends('layouts.app')

@section('title')
    Gerenciar Atendente Fraterno
@endsection

@section('content')
    <div class="container";>

        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR ATENDENTES FRATERNOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <form action="{{ route('list') }}" class="form-horizontal mt-4" method="GET">
                    <div class="row">

                        {{-- Input Pesquisa Nome --}}
                        <div class="col-3">Nome
                            <input class="form-control" type="text" maxlength="45"
                                oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="1" name="nome" value="{{ $nome }}">
                        </div>
                        {{-- Fim input pesquisa Nome --}}

                        {{-- Input Pesquisa CPF --}}
                        <div class="col-2">CPF
                            <input class="form-control" type="text" maxlength="11"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="2" name="cpf" value="{{ $cpf }}">
                        </div>
                        {{-- Fim Input Pesquisa CPF --}}

                        {{-- Select Status --}}
                        <div class="col-2">Status
                            <select class="form-select status1" id="4" name="status" type="numeric">
                                {{-- Pega os valores de retorno e seleciona automaticamente o valor anterior --}}
                                <option value="1" {{ $status == 1 ? 'selected' : '' }}>Ativo</option>
                                <option value="2" {{ $status == 2 ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        {{-- Fim Select Status --}}
                        <div class="col"><br>

                            {{-- Botao Submit Pesquisar --}}
                            <input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                value="Pesquisar">
                            {{-- Fim Submit Pesquisar --}}

                            {{-- Botao Limpar com rota para mesma tela --}}
                            <a href="/gerenciar-atendentes"><input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>
                            {{-- Fim botão Limpar --}}
                </form>
                {{-- Botao Para Criar Novo Atendente --}}
                <a href="/criar-atendente"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                        type="button" value="Novo atendente +"></a>

            </div>
        </div>
    </div>

    <hr>
    {{-- Mostra a quantioade total de resultados mostrados na pagina --}}
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
                        <td scope="">{{ str_pad($atendentes->cpf, 11, '0', STR_PAD_LEFT) }}</td>{{-- Mostra o CPF com os "0" --}}
                        <td scope="">{{ date('d/m/Y', strtotime($atendentes->dt_nascimento)) }}</td>
                        <td scope="">{{ $atendentes->tipo }}</td>
                        <td scope="">{{ $atendentes->ddd }}</td>
                        <td scope="">{{ $atendentes->celular }}</td>
                        <td scope="">{{ $atendentes->tpsta }}</td>{{-- Mostra o Status do Atendente --}}
                        <td scope="">

                            {{-- Botao Com rota para Editar --}}
                            <a href="/editar-atendente/{{ $atendentes->id }}" type="button"
                                class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                title="Editar">
                                <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                            </a>
                            {{-- Fim botao Editar --}}

                            {{-- Botao Visualizar --}}
                            <a href="/visualizar-atendente/{{ $atendentes->id }}" type="button"
                                class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                title="Visualizar">
                                <i class="bi bi-search" style="font-size: 1rem; color:#000;" data-bs-target="#pessoa"></i>
                            </a>
                            {{-- Fim botao Visualizar --}}

                            {{-- Botaõ Inicializador da modal de Exclusão --}}
                            <button btn btn-outline-danger btn-sm data-bs-toggle="modal"
                            data-bs-target="#confirmarExclusao{{ $atendentes->id }}" data-tt="tooltip"
                            data-placement="top" title="Excluir" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                        </button>
                        {{-- Fim do botão Modal Exclusão --}}

                        {{-- Modal Exclusao --}}
                            <div class="modal fade" id="confirmarExclusao{{ $atendentes->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel" style="color:red;">Confirmação
                                                de
                                                Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja excluir o atendente <p style="color:red;">
                                                {{ $atendentes->nome_completo }}&#63;</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-danger"
                                                href="/excluir-atendente/{{ $atendentes->id }}">Confirmar
                                                Exclusão</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
{{-- Fim modal de Exclusao --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Import Jquery --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

                            <script>
                                //Tooltips
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
                                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl)
                                })

                                //Deixa o select status como padrao vazio
                                if (typeof {{ $status }} === 'undefined') {
                                    $(".status1").prop("selectedIndex", -1);
                                }
                            </script>
@endsection

@section('footerScript')
@endsection
