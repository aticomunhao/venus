@extends('layouts.app')
@section('title', 'Administrar Grupos')
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">ADMINISTRAR GRUPOS
        </h4>
        <div class="col-12">
            <form action="/gerenciar-grupos-membro" class="form-horizontal mt-4" method="GET">
                <div class="row d-flex flex-wrap align-items-center">
                    <!-- Grupo Field -->
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <label for="nome_grupo" class="form-label">Grupo</label>
                        <select class="form-select select2 grupo" id="nome_grupo" name="nome_grupo">
                            <option value=""></option>
                            @foreach ($grupos2 as $gr)
                                <option value="{{ $gr->idg }}" {{ request('nome_grupo') == $gr->idg ? 'selected' : '' }}>
                                    {{ $gr->nomeg }} ({{ $gr->sigla }})-{{ $gr->dia_semana }}
                                    | {{ date('H:i', strtotime($gr->h_inicio)) }}/{{ date('H:i', strtotime($gr->h_fim)) }}
                                    | Sala {{ $gr->sala }}
                                    | {{ $gr->status == 'Inativo' ? 'Inativo' : $gr->descricao_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Membro Field -->
                    <div class="col-12 col-sm-6 col-md-3 mb-2">
                        <label for="nome_membro" class="form-label">Membro</label>
                        <select class="form-select select2 membro" id="nome_membro" name="nome_membro">
                            <option></option>
                            @foreach ($membro as $membros)
                                <option value="{{ $membros->id_associado }}">{{ $membros->nome_completo }} - {{ $membros->nr_associado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pesquisar Button -->
                    <div class="col-6 col-md-1 mt-3">
                        <input class="btn btn-light btn-sm w-100" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;" type="submit" value="Pesquisar">
                    </div>

                    <!-- Limpar Button -->
                    <div class="col-6 col-md-1 mt-3">
                        <a href="/gerenciar-grupos-membro">
                            <input class="btn btn-light btn-sm w-100" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;" type="button" value="Limpar">
                        </a>
                    </div>

                    <!-- Novo Membro Button -->
                    @if (in_array(13, session()->get('usuario.acesso')))
                        <div class="col-12 col-md-1 mt-3">
                            <a href="/criar-membro">
                                <input class="btn btn-success btn-sm w-100" style="font-size: 0.9rem; margin: 5px;" type="button" value="Novo membro +">
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <br>
    Total de Grupos: {{ $contar }}

    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
            <thead>
                <tr style="background-color: #d6e3ff; font-size: 14px; color: #000000">
                    <th class="small-column">GRUPO</th>
                    <th>SETOR</th>
                    <th>DETALHES</th>
                    <th>STATUS</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($membro_cronograma as $membros)
                    <tr>
                        <td>{{ $membros->nome_grupo }}</td>
                        <td>{{ $membros->sigla }}</td>

                        <!-- Coluna de Detalhes com o Popover -->
                        <td>
                            <button type="button" class="btn btn-info btn-sm"
                                data-bs-toggle="popover"
                                data-bs-placement="top"
                                data-bs-html="true"
                                data-bs-title="Detalhes do Cronograma"
                                data-bs-content="
                                    <strong>Dia:</strong> {{ $membros->dia }}<br>
                                    <strong>Início:</strong> {{ \Carbon\Carbon::parse($membros->h_inicio)->format('H:i') }}<br>
                                    <strong>Fim:</strong> {{ \Carbon\Carbon::parse($membros->h_fim)->format('H:i') }}<br>
                                    <strong>Sala:</strong> {{ $membros->sala }}"
                                style="font-size: 0.8rem; padding: 5px 10px;">
                                Detalhes
                            </button>
                        </td>

                        <td>{{ $membros->status }}</td>

                        <td>
                            <a href="/gerenciar-membro/{{ $membros->id }}" type="button"
                                class="btn btn-outline-warning btn-sm tooltips">
                                <span class="tooltiptext">Gerenciar</span>
                                <i class="bi bi-gear" style="font-size: 1rem; color:#000;"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    </div class="d-flex justify-content-center">
    {{ $membro_cronograma->links('pagination::bootstrap-5') }}
    </div>

    <script>

    document.addEventListener('DOMContentLoaded', function () {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    });


        $('.select2').select2({
            width: '100%' // Garante que o select2 ocupe 100% da largura disponível
        });

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            //Deixa o select status como padrão vazio
            $(".grupo").prop("selectedIndex", 0);
            $(".membro").prop("selectedIndex", 0);
        });
    </script>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        function confirmarExclusao(id, nome) {
            document.getElementById('btn-confirmar-exclusao').setAttribute('data-id', id);
            document.getElementById('modal-body-text').innerText = nome;
            $('#confirmacaoDelecao').modal('show');
        }

        function confirmarDelecao() {
            var id = document.getElementById('btn-confirmar-exclusao').getAttribute('data-id');
            window.location.href = '/deletar-membro/' + id;
        }
    </script>

    <style>
        .small-column {
            width: 22%;
        }

        @media (max-width: 576px) {
            .small-column {
                width: 25%;
            }

            .table th,
            .table td {
                font-size: 12px;
                /* Ajusta o tamanho da fonte da tabela em dispositivos móveis */
            }

            .btn {
                font-size: 0.8rem;
                /* Ajusta o tamanho do botão para dispositivos móveis */
            }

            .card-title {
                font-size: 16px;
            }
        }
    </style>
@endsection
