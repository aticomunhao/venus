@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR MÉDIUNS
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
                    {{-- <div class="col-2">
                        Grupo
                        <select class="form-select" id="grupo_pesquisa" name="grupo_pesquisa">
                            <option value="" selected>Selecione o grupo</option>
                            @foreach ($grupos as $grupoId => $grupoNome)
                                <option value="{{ $grupoId }}" {{ $grupoPesquisa == $grupoId ? 'selected' : '' }}>
                                    {{ $grupoNome }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}
                    {{-- <div class="col-2">
                        Setor
                        <select class="form-select" id="setor_pesquisa" name="setor_pesquisa">
                            <option value="" selected>Selecione o setor</option>
                            @foreach ($setores as $setorId => $setorNome)
                                <option value="{{ $setorId }}" {{ $setorPesquisa == $setorId ? 'selected' : '' }}>
                                    {{ $setorNome }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}

                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                        <a href="/gerenciar-mediuns"><input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Limpar"></a>
                        <a href="/criar-mediuns"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                                type="button" value="Novo médium +"></a>
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
                    <th>SETOR</th>
                    <th>STATUS</th>
                    <th>AÇÕES</th>
                </tr>

                <tbody>
                    @foreach ($medium as $mediuns)
                        <tr>
                            <td>{{ $mediuns->idm }}</td>
                            <td>{{ $mediuns->nome_completo }}</td>
                            <td>{{ $mediuns->nome_grupo }}</td>
                            <td>{{ $mediuns->nome_funcao }}</td>
                            <td>{{ $mediuns->nome_setor }}</td>
                            <td>{{ $mediuns->status ? 'Ativo' : 'Inativo' }}</td>
                            <td>

                                <a href="/editar-mediuns/{{ $mediuns->idm }}" type="button"
                                    class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Editar">
                                    <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                <a href="/visualizar-mediuns/{{ $mediuns->idm }}" type="button"
                                    class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Visualizar">
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>
                                <a href="/deletar-mediuns" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#confirmacaoDelecao"
                                    onclick="confirmarExclusao('{{ $mediuns->idm }}', '{{ $mediuns->nome_completo }}')"
                                    data-tt="tooltip" data-placement="top" title="Deletar">
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            </td>
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
                    Tem certeza que deseja excluir o Médium "<span id="modal-body-text"></span>"?
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
            window.location.href = '/deletar-mediuns/' + id;
        }
    </script>
@endsection

