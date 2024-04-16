@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR MEMBRO
        </h4>

        <div class="col-12">
            <form action="{{ route('lista') }}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-4">
                        Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                             value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-2">
                        CPF
                        <input class="form-control" type="text" id="cpf_pesquisa" name="cpf_pesquisa"
                             value="{{ request('cpf_pesquisa') }}">
                    </div>

                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                        <a href="/gerenciar-membro"><input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Limpar"></a>
                        <a href="/criar-membro"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                                type="button" value="Novo membro +"></a>
                    </div>
                </div>

            </form>

        </div>

        <hr>

        <div class="table">
            <table
                class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th>ID</th>
                    <th>NOME</th>
                    <th>NOME GRUPO</th>
                    <th>FUNÇÃO</th>
                    <th>STATUS</th>
                    <th>AÇÕES</th>
                </tr>

                <tbody>
                    @foreach ($encaminhamentos as $encaminhamento)
                        <tr>
                            <td>{{ $encaminhamento->id }}</td>
                            <td>{{ $encaminhamento->nome_completo }}</td>
                            <td>{{ $encaminhamento->nome }}</td>
                            <td>{{ $encaminhamento->h_inicio }}</td>
                            <td>{{ $encaminhamento->h_fim }}</td>

                            {{--  <td>

                                <a href="/editar-membro/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Editar">
                                    <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                <a href="/visualizar-membro/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Visualizar">
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>
                                <a href="/deletar-membro" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#confirmacaoDelecao"
                                    onclick="confirmarExclusao('{{ $membros->idm }}', '{{ $membros->nome_completo }}')"
                                    data-tt="tooltip" data-placement="top" title="Deletar">
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            </td>  --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="confirmacaoDelecao" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir o Membro "<span id="modal-body-text"></span>"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btn-confirmar-exclusao"
                        onclick="confirmarDelecao()">Confirmar Exclusão</button>
                </div>
            </div>
        </div>
    </div>

    <script src="caminho/para/bootstrap/js/bootstrap.bundle.min.js" async defer></script>
    <link href="caminho/para/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

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
@endsection

