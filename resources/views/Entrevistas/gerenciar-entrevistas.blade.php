@extends('layouts.app')

@section('title')
    Gerenciar Entrevista
@endsection

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ENTREVISTA
        </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('gerenciamento') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">
                            <div class="col">Nome
                                <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"{{-- Input de pesquisa de Nome --}}
                                    value={{ $pesquisaNome }}>
                            </div>

                            <div class="col">Status
                                <select class="form-select teste" id="4" name="status" type="number">{{-- Select de pesquisa de status --}}
                                    <option value=1 {{ $pesquisaValue == 1 ? 'selected' : '' }}>Aguardando agendamento</option>{{-- Se auto seleciona de acordo com o valor anterior --}}
                                    <option value=4 {{ $pesquisaValue == 4 ? 'selected' : '' }}>Aguardando entrevistador</option>
                                    <option value=2 {{ $pesquisaValue == 2 ? 'selected' : '' }}>Agendado</option>
                                    <option value=3 {{ $pesquisaValue == 3 ? 'selected' : '' }}>Entrevistado</option>
                                </select>
                            </div>
                            <div class="col"><br />
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">{{-- Botao de pesquisar --}}
                                <a href="/gerenciar-entrevistas"><input class="btn btn-light btn-sm me-md-2"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                        value="Limpar"></a>{{-- Botao de limpar pesquisa --}}
                            </div>
                        </div>
                    </form>
                    <br />
                </div>
                <hr />
                <div class="table">Total assistidos:
                    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                        <thead style="text-align: center;">
                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                <th class="col">Nr</th>
                                <th class="col">NOME</th>
                                <th class="col">DATA </th>
                                <th class="col">HORA </th>
                                <th class="col">ENTREVISTADOR</th>
                                <th class="col">SALA</th>
                                <th class="col">STATUS</th>
                                <th class="col">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px; color:#000000; text-align: center;">
                            @foreach ($informacoes as $informacao)
                                <tr>
                                    <td>{{ $informacao->ide }}</td>{{-- Traz o ID do encaminhamento --}}
                                    <td>{{ $informacao->nome_pessoa }}</td>{{-- Traz o nome do encaminhado --}}
                                    <td>{{ !is_null($informacao->data) ? date('d-m-Y', strtotime($informacao->data)) : '--' }}{{-- Valida a data e transforma para o padrao brasileiro --}}
                                    </td>
                                    <td>{{ !is_null($informacao->hora) ? date('G:i', strtotime($informacao->hora)) : '--' }}{{-- Valida a hora e transforma parar o forrmato 24h --}}
                                    </td>
                                    <td>{{ $informacao->nome_entrevistador }}</td>{{-- Quando existente, traz o nome do entrevistador --}}
                                    <td>{{ $informacao->local }}</td>{{-- Sala que foi agendada a entrevista --}}
                                    <td>
                                        @if ($informacao->status === 'Aguardando agendamento')
                                            Aguardando agendamento
                                        @else
                                            {{ $informacao->status }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($informacao->status == 'Aguardando agendamento' or $informacao->status == 'Entrevistado'){{-- Inicio botao editar --}}
                                            <a href="#" type="button" class="btn btn-outline-warning btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="Editar" disabled>
                                                <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <a href="/editar-entrevista/{{ $informacao->ide }}" type="button"
                                                class="btn btn-outline-warning btn-sm" data-tt="tooltip"
                                                data-placement="top" title="Editar">
                                                <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim botao editar --}}

                                        @if ($informacao->status !== 'Aguardando agendamento'){{-- Inicio botao Agendar --}}
                                            <a href="#" type="button" class="btn btn-outline-success btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="Agendar" disabled>
                                                <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('criar-entrevista', ['id' => $informacao->ide]) }}"
                                                type="button" class="btn btn-outline-success btn-sm" data-tt="tooltip"
                                                data-placement="top" title="Agendar ">
                                                <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim botao agendar --}}

                                        @if ($informacao->status !== 'Aguardando entrevistador'){{-- Inicio botao agendar entrevistador --}}
                                            <a href="#" type="button" class="btn btn-outline-primary btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="historico" disabled>
                                                <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('agendar-entrevistador', ['id' => $informacao->ide]) }}"
                                                type="button" class="btn btn-outline-success btn-sm" data-tt="tooltip"
                                                data-placement="top" title="Agendar entrevistador">
                                                <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim aguardando entrevistador --}}

                                        @if ($informacao->status == 'Aguardando agendamento'){{-- Inicio visualizar --}}
                                            <a href="#" type="button" class="btn btn-outline-primary btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="historico" disabled>
                                                <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <a href="/visualizar-entrevista/{{ $informacao->ide }}" type="button"
                                                class="btn btn-outline-primary btn-sm" data-tt="tooltip"
                                                data-placement="top" title="Histórico">
                                                <i class="bi bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim visualizar --}}

                                        @if ($informacao->status !== 'Agendado'){{-- Inicio Finalizar --}}
                                            <a href="#" type="button"
                                                class="btn btn-outline-success btn-sm disabled" data-tt="tooltip"
                                                data-placement="top" title="Finalizar" disabled>
                                                <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                data-tt="tooltip" data-placement="top" title="Finalizar"
                                                data-bs-toggle="modal" data-bs-target="#modalF{{ $informacao->ide }}">
                                                <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i>
                                            </button>
                                        @endif{{-- Fim Finalizar --}}


                                        @if ($informacao->status == 'Entrevistado'){{-- Inicio excluir --}}
                                            <i href="#" type="button"
                                                class="btn btn-outline-danger btn-sm disabled" data-tt="tooltip"
                                                data-placement="top" title="Inativar" disabled>
                                                <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                            @else
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                            data-tt="tooltip" data-placement="top" title="Inativar"
                                            data-bs-toggle="modal" data-bs-target="#modal{{ $informacao->ide }}">
                                            <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                        </button>{{-- Fim excluir --}}
                                        @endif



                                            {{--  Modal de Finalizacao --}}
                                            <div class="modal fade" id="modalF{{ $informacao->ide }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel" style="color: green;">Confirmação de
                                                                Finalização</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Tem certeza que deseja finalizar a entrevista de <p
                                                                style="color: green;">{{ $informacao->nome_pessoa }}&#63;</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <a type="button" class="btn btn-success"
                                                                href="/finalizar-entrevista/{{ $informacao->ide }}">Confirmar
                                                                Finalização</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Fim Modal de Finalizacao --}}

                                            {{--  Modal de Exclusao --}}
                                            <div class="modal fade" id="modal{{ $informacao->ide }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel" style="color:red;">Confirmação de
                                                                Exclusão</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Tem certeza que deseja excluir a entrevista de <p
                                                                style="color:red;">{{ $informacao->nome_pessoa }}&#63;</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <a type="button" class="btn btn-danger"
                                                                href="/inativar-entrevista/{{ $informacao->ide }}">Confirmar
                                                                Exclusão</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Fim Modal de Exclusao --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>//Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })



        if ({{ $pesquisaValue }} == 0) {//Deixa o select status como padrao vazio
            $(".teste").prop("selectedIndex", -1);
        }
    </script>
@endsection

@section('footerScript')
@endsection
