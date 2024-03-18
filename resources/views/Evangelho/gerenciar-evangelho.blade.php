@extends('layouts.app')

@section('title')
    Gerenciar Evangelho
@endsection

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR EVANGELHO
        </h4>

        <div class="col-12">
            <div class="row justify-content-center">
                <form action="{{ route('start') }}" class="form-horizontal mt-4" method="GET">

                    <div class="row align-items-center">
                        <div class="col-md-4">
                            Nome
                            <input type="text" class="form-control" name="nome" value="{{ $pesquisaNome }}">
                        </div>
                        <div class="col-md-4">
                            Status
                            <select class="form-select teste" id="4" name="status"
                                type="number">{{-- Select de pesquisa de status --}}
                                <option value=1 {{ $pesquisaValue == 1 ? 'selected' : '' }}>Aguardando agendamento</option>
                                {{-- Se auto seleciona de acordo com o valor anterior --}}
                                <option value=2 {{ $pesquisaValue == 2 ? 'selected' : '' }}>Agendado</option>
                                <option value=3 {{ $pesquisaValue == 3 ? 'selected' : '' }}>Entrevistado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">

                            <button type="submit" class="btn btn-light"
                                style="box-shadow: 1px 2px 5px #000000; margin-right: 5px;">Pesquisar</button>
                            <a href="{{ route('start') }}" class="btn btn-light"
                                style="box-shadow: 1px 2px 5px #000000;">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>
            <hr />
        </div>

        <div class="table">Total assistidos:
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">Nr</th>
                        <th class="col">NOME</th>
                        <th class="col">DATA</th>
                        <th class="col">HORA</th>
                        <th class="col">STATUS</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align: center;">
                    @foreach ($informacoes as $informacao)
                        <tr>
                            <td>{{ $informacao->ide }}</td>
                            {{-- Traz o ID do encaminhamento --}}
                            <td>{{ $informacao->nome_pessoa }}</td>
                            {{-- Traz o nome do encaminhado --}}
                            <td>{{ !is_null($informacao->data) ? date('d-m-Y', strtotime($informacao->data)) : '--' }}</td>
                            {{-- Valida a data e transforma para o padrão brasileiro --}}
                            <td>{{ !is_null($informacao->hora) ? date('G:i', strtotime($informacao->hora)) : '--' }}</td>
                            {{-- Valida a hora e transforma para o formato 24h --}}
                            <td>{{ $informacao->status }}</td>
                            <td>
                                @if ($informacao->status !== 'Agendado' && $informacao->status !== 'Entrevistado')
                                    <a href="{{ route('criar', ['ide' => $informacao->ide]) }}" type="button"
                                        class="btn btn-outline-success btn-sm " data-tt="tooltip" data-placement="top"
                                        title="Agendar">
                                        <i class=" bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @else
                                    <a href="{{ route('criar', ['ide' => $informacao->ide]) }}" type="button"
                                        class="btn btn-outline-success btn-sm disabled" data-tt="tooltip"
                                        data-placement="top" title="Agendar">
                                        <i class=" bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @endif

                                @if ($informacao->status == 'Aguardando agendamento')
                                    <a href="#" type="button" class="btn btn-outline-warning btn-sm disabled"
                                        data-tt="tooltip" data-placement="top" title="Editar" disabled>
                                        <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @else
                                    <a href="/editar-evangelho/{{ $informacao->ide }}" type="button"
                                        class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                        title="Editar">
                                        <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @endif
                                @if ($informacao->status == 'Aguardando agendamento')
                                    <a href="#" type="button" class="btn btn-outline-primary btn-sm disabled"
                                        data-tt="tooltip" data-placement="top" title="Historico" disabled>
                                        <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @else
                                    <a href="/visualizar-evangelho/{{ $informacao->ide }}" type="button"
                                        class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                        title="Histórico">
                                        <i class="bi bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @endif
                                @if ($informacao->status == 'Aguardando agendamento' || $informacao->status == 'Entrevistado')
                                    <a href="/finalizar-evangelho/{{ $informacao->ide }}" type="button"
                                        class="btn btn-outline-success btn-sm disabled" data-tt="tooltip"
                                        data-placement="top" title="Finalizar">
                                        <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"
                                            data-bs-target="#pessoa"></i>
                                    @else
                                        <a href="/finalizar-evangelho/{{ $informacao->ide }}" type="button"
                                            class="btn btn-outline-success btn-sm" data-tt="tooltip" data-placement="top"
                                            title="Finalizar">
                                            <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"
                                                data-bs-target="#pessoa"></i>
                                        </a>
                                @endif
                                @if ($informacao->status == 'Aguardando agendamento' || $informacao->status == 'Entrevistado')
                                    <a href="/inativar-evangelho/{{ $informacao->ide }}" type="button"
                                        class="btn btn-outline-danger btn-sm disabled" data-tt="tooltip"
                                        data-placement="top" title="Inativar">
                                        <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"
                                            data-bs-target="#pessoa"></i>
                                    @else
                                        <a href="/inativar-evangelho/{{ $informacao->ide }}" type="button"
                                            class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modal{{ $informacao->ide }}" data-tt="tooltip"
                                            data-placement="top" title="Inativar" data-tt="tooltip"
                                            data-placement="top">
                                            <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"
                                                data-bs-target="#pessoa"></i>
                                        </a>
                                @endif
                            </td>
                        </tr>
                        <!-- Modal de Exclusao -->
                        <div class="modal fade" id="modal{{ $informacao->ide }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel" style="color:red;">Confirmação de
                                            Exclusão</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Tem certeza que deseja excluir a entrevista de <p style="color:red;">
                                            {{ $informacao->nome_pessoa }}?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <a type="button" class="btn btn-danger"
                                            href="/inativar-evangelho/{{ $informacao->ide }}">Confirmar Exclusão</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Fim Modal de Exclusao --}}
                        </td>
                        </tr>
                        <script>
                            //Tooltips
                            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
                            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl)
                            })


                            if ({{ $pesquisaValue }} == 0) {//Deixa o select status como padrao vazio
                                $(".teste").prop("selectedIndex", -1);
                            }
                        </script>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
