@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR GRUPO - {{ Str::upper($grupo->nome) }} - {{ Str::upper($grupo->dia) }}
        </h4>

        <div class="col-12">
            <form action="/gerenciar-membro/{{ $id }}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-4">
                        Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                             value="{{ request('nome_pesquisa') }}">
                    </div>


                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                            <a href="/gerenciar-membro/{{ $id }}" class="btn btn-light btn-sm me-md-2 offset-4" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;"
                            type="button">Limpar</a>



                        <a href="/gerenciar-grupos-membro" class="btn btn-primary btn-sm me-md-2  offset-2"
                         type="button">Retornar para tela inicial</a>
                         @if($grupo->status_reuniao == 4)
                         <a href="/ferias-reuniao/{{ $id }}/2"><input class="btn btn-warning btn-sm me-md-2" style="font-size: 0.9rem;"
                             type="button" value="Retomar de Férias"></a>
                         @else
                         <a href="/ferias-reuniao/{{ $id }}/1"><input class="btn btn-danger btn-sm me-md-2" style="font-size: 0.9rem;"
                             type="button" value="Declarar Férias"></a>
                         @endif
                        <a href="/criar-membro-grupo/{{ $id }}"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
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

                    <th>FUNÇÃO</th>
                    <th>STATUS PESSOA</th>
                    <th>AÇÕES</th>
                </tr>

                <tbody>
                    @foreach ($membro as $membros)
                        <tr>
                            <td>{{ $membros->idm }}</td>
                            <td>{{ $membros->nome_completo }}</td>
                            <td>{{ $membros->nome_funcao }}</td>
                            <td>{{ $membros->status ? 'Ativo' : 'Inativo' }}</td>
                            <td>

                                <a href="/editar-membro/{{ $id }}/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Editar">
                                    <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                <a href="/visualizar-membro/{{ $id }}/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Visualizar">
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>
                                <a href="" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#confirmacaoDelecao{{ $membros->idm }}"
                                    data-tt="tooltip" data-placement="top" title="Deletar">
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            </td>
                        </tr>
                        <div class="modal fade" id="confirmacaoDelecao{{ $membros->idm }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Tem certeza que deseja excluir o Membro "{{ $membros->nome_completo }}"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <a href="/deletar-membro/{{ $id }}/{{ $membros->idm }}" class="btn btn-danger">Excluir Permanentemente</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
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


    </script>
@endsection

