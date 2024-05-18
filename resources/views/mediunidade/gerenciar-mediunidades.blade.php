@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size: 20px; text-align: left; color: gray; font-family: calibri;">
            GERENCIAR MEDIUNIDADE
        </h4>

        <div class="col-12">
            <form action="{{ route('names') }}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-3">
                        Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-2">
                        CPF
                        <input class="form-control" type="text" id="cpf_pesquisa" name="cpf_pesquisa" value="{{ request('cpf_pesquisa') }}">
                    </div>
                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;" type="submit" value="Pesquisar">
                        <a href="/gerenciar-mediunidades" class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin: 5px;">Limpar</a>
                        <a href="criar-mediunidade" class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;">Nova mediunidade +</a>
                    </div>
                </div>
            </form>
        </div>

        <hr>

        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                <thead>
                    <tr style="background-color: #d6e3ff; font-size: 14px; color: #000000;">
                        <th>ID</th>
                        <th>NOME</th>
                        <th>CPF</th>
                        <th>STATUS</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mediunidade as $mediunidades)
                        <tr>
                            <td>{{ $mediunidades->idp }}</td>
                            <td>{{ $mediunidades->nome_completo }}</td>
                            <td>{{ $mediunidades->cpf }}</td>
                            <td>{{ $mediunidades->status ? 'Ativo' : 'Inativo' }}</td>
                            <td>
                                <a href="/editar-mediunidade/{{ $mediunidades->idp }}" class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Editar">
                                    <i class="bi bi-pen" style="font-size: 1rem; color: #000;"></i>
                                </a>
                                <a href="/visualizar-mediunidade/{{ $mediunidades->idp }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Visualizar">
                                    <i class="bi bi-search" style="font-size: 1rem; color: #000;"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmacaoDelecao{{ $mediunidades->idp }}" data-bs-toggle="tooltip" title="Deletar">
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color: #000;"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal de Confirmação de Exclusão -->
                        <div class="modal fade" id="confirmacaoDelecao{{ $mediunidades->idp }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #DC4C64;">
                                        <h5 class="modal-title" id="exampleModalLabel" style="color: white;">Exclusão de membro</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="text-align: center;">
                                        Tem certeza que deseja excluir o membro<br><span style="color: #DC4C64; font-weight: bold;">{{ $mediunidades->nome_completo }}</span>?
                                    </div>
                                    <div class="modal-footer mt-3">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                        <a class="btn btn-primary" href="/deletar-mediunidade/{{ $mediunidades->idp }}">Confirmar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
