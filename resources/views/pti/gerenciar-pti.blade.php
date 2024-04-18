@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PTI
        </h4>

        <div class="col-12">
            <form action="/gerenciar-pti" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-4">
                        Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                             value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-4">
                        Grupos

                        <select class="form-select status" id="4" name="grupo" type="number">
                            @foreach ($dirigentes as $dirigente)
                                <option value="{{ $dirigente->id }}" {{ $dirigente->id == $selected_grupo ? 'selected' : '' }}>{{ $dirigente->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                        <a href="/gerenciar-pti"><input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Limpar"></a>

                        @foreach ($dirigentes as $dirigente)
                        @if($selected_grupo == $dirigente->id)

                            @if($dirigente->status_grupo == 4)
                            <a href="/ferias-pti/{{ request('grupo') }}/2"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                                type="button" value="Retomar de Férias"></a>
                            @else
                            <a href="/ferias-pti/{{ request('grupo') }}/1"><input class="btn btn-danger btn-sm me-md-2" style="font-size: 0.9rem;"
                                type="button" value="Declarar Férias"></a>
                            @endif


                            @endif
                                @endforeach
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
                    <th>HORÁRIO INÍCIO</th>
                    <th>HORÁRIO FIM</th>
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


                              <td>

                                <a href="/alta-pti" type="button"
                                    class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Declarar Alta">
                                    <i class="bi bi-clipboard-plus" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                <a href="/visualizar-pti/{{ $encaminhamento->id }}" type="button"
                                    class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                    title="Visualizar">
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
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

