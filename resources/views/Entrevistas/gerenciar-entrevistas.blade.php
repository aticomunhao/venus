@extends('layouts.app')

@section('title')
    Gerenciar Entrevista
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ENTREVISTA
        </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('gerenciamento') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row align-items-end">
                            <div class="col-4">
                                Nome
                                <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                                    value="{{ $nome_pesquisa }}">
                            </div>

                            <div class="col-3">
                                Status
                                <select class="form-select" id="status" name="status">
                                    {{-- Select de pesquisa de status --}}
                                    <option value="" {{ $pesquisaValue ? 'selected' : '' }}>Todos</option>
                                    @foreach ($status as $statu)
                                        <option value="{{ $statu->id }}"
                                            {{ $statu->id == $pesquisaValue ? 'selected' : '' }}>
                                            {{ $statu->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3">
                                Tipo Entrevista
                                <select class="form-select" id="tipo_entrevista" name="tipo_entrevista">
                                    {{-- Select de pesquisa de tipo entrevista --}}
                                    <option value="">Todos</option>
                                    @foreach ($tipo_entrevista as $tp_ent)
                                        <option value="{{ $tp_ent->id_ent }}"
                                            {{ request('tipo_entrevista') == $tp_ent->id_ent ? 'selected' : '' }}>
                                            {{ $tp_ent->ent_desc }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-auto">
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                                {{-- Botão de pesquisar --}}
                                <a href="/gerenciar-entrevistas">
                                    <input class="btn btn-light btn-sm me-md-2"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar">
                                    {{-- Botão de limpar pesquisa --}}
                                </a>
                            </div>
                        </div>
                    </form>
                    <br />
                </div>
                <hr />
                <div class="table">Quantidade de assistidos: {{ $totalAssistidos }}
                    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                        <thead style="text-align: center;">
                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                {{-- <th class="col">Nr</th> --}}
                                <th class="col">DATA E HORÁRIO</th>
                                <th class="col">PRIORIDADE</th>
                                <th class="col">NOME</th>
                                <th class="col">ENTREVISTADOR</th>
                                <th class="col">TIPO ENTREVISTA</th>
                                <th class="col">SALA</th>
                                <th class="col">STATUS</th>
                                <th class="col">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px; color:#000000; text-align: center;">
                            @foreach ($informacoes as $informacao)
                                <tr>
                                    {{-- <td>{{ $informacao->ide }}</td>Traz o ID do encaminhamento --}}
                                    <td>{{ date('d/m/Y  G:i', strtotime($informacao->inicio)) }}</td>{{-- Prioridade do atendimento --}}
                                    <td>{{ $informacao->emergencia }}</td>{{-- Prioridade do atendimento --}}
                                    <td>{{ $informacao->nome_pessoa }}</td>{{-- Traz o nome do encaminhado --}}
                                    <td>{{ $informacao->nome_entrevistador }}</td>{{-- Quando existente, traz o nome do entrevistador --}}
                                    <td>{{ $informacao->entrevista_sigla }}</td>
                                    <td>{{ $informacao->numero }}</td>{{-- Sala que foi agendada a entrevista --}}
                                    <td>
                                        @if ($informacao->status === 1 && $informacao->status_encaminhamento_id === 6)
                                            {{ $informacao->status_encaminhamento_descricao }}
                                        @elseif ($informacao->status === 1)
                                            Aguardando agendamento
                                        @else
                                            {{ $informacao->d1 }}
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Inicio botao editar --}}
                                        @if ($informacao->status == 1 or $informacao->status == 6 or $informacao->status == 5)
                                            <a href="#" type="button" class="btn btn-outline-warning btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="Editar" disabled>
                                                <i class="bi bi-pencil"style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <a href="/editar-entrevista/{{ $informacao->ide }}" type="button"
                                                class="btn btn-outline-warning btn-sm tooltips">
                                                <span class="tooltiptext">Editar</span>
                                                <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim botao editar --}}

                                        @if ($informacao->status !== 1 or $informacao->status_encaminhamento_id === 6)
                                            {{-- Inicio botao Agendar --}}
                                            <a href="#" type="button" class="btn btn-outline-primary btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="Agendar" disabled>
                                                <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"
                                                    disabled></i>
                                            </a>
                                        @else
                                            <a href="{{ route('criar-entrevista', ['id' => $informacao->ide]) }}"
                                                type="button" class="btn btn-outline-primary btn-sm tooltips">
                                                <span class="tooltiptext">Agendar</span>
                                                <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim botao agendar --}}
                                        @if ($informacao->status !== 2)
                                            {{-- Inicio botao agendar entrevistador --}}
                                            <a href="#" type="button" class="btn btn-outline-primary btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="historico" disabled>
                                                <i class="bi bi-person-add" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('agendar-entrevistador', ['id' => $informacao->ide]) }}"
                                                type="button" class="btn btn-outline-success btn-sm tooltips">
                                                <span class="tooltiptext">Confirmar Entrevistador</span>
                                                <i class="bi bi-person-add" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim aguardando entrevistador --}}
                                        @if ($informacao->status == 1)
                                            {{-- Inicio visualizar --}}
                                            <a href="#" type="button" class="btn btn-outline-primary btn-sm disabled"
                                                data-tt="tooltip" data-placement="top" title="historico" disabled>
                                                <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <a href="/visualizar-entrevista/{{ $informacao->ide }}" type="button"
                                                class="btn btn-outline-primary btn-sm tooltips">
                                                <span class="tooltiptext">Histórico</span>
                                                <i class="bi bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @endif{{-- Fim visualizar --}}
                                        @if ($informacao->status !== 3)
                                            {{-- Inicio Confirmar --}}
                                            <a href="#" type="button"
                                                class="btn btn-outline-success btn-sm disabled" data-tt="tooltip"
                                                data-placement="top" title="Confirmar" disabled>
                                                <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-sm tooltips"
                                                data-bs-toggle="modal" data-bs-target="#modalF{{ $informacao->ide }}">
                                                <span class="tooltiptext">Confirmar</span>
                                                <i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i>
                                            </button>
                                        @endif{{-- Fim Confirmar --}}

                                        {{--  Modal de Finalizacao --}} <div class="modal fade" id="modalF{{ $informacao->ide }}"
                                            tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header"
                                                        style="background-color: green; color: white">
                                                        <h5 class="modal-title" id="exampleModalLabel">Confirmação
                                                            de
                                                            Finalização</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja finalizar a entrevista de <p
                                                            style="color: green;">
                                                            {{ $informacao->nome_pessoa }}&#63;</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <a type="button" class="btn btn-primary"
                                                            href="/finalizar-entrevista/{{ $informacao->ide }}">Confirmar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Inativar encaminhamento --}}
                                        @if ($informacao->status_encaminhamento_id != 6)
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#inativar{{ $informacao->ide }}" type="button"
                                                class="btn btn-outline-danger btn-sm tooltips">
                                                <span class="tooltiptext">Inativar</span>
                                                <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                                            </a>

                                            <form action="{{ route('cancelar', ['id' => $informacao->ide]) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal fade" id="inativar{{ $informacao->ide }}"
                                                    data-bs-keyboard="false" tabindex="-1"
                                                    aria-labelledby="inativarLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                style="background-color:#DC4C64;color:white">
                                                                <h1 class="modal-title fs-5" id="inativarLabel">Inativação
                                                                </h1>
                                                                <button data-bs-dismiss="modal" type="button"
                                                                    class="btn-close" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <center>
                                                                    <label for="recipient-name" class="col-form-label"
                                                                        style="font-size:17px">
                                                                        Tem certeza que deseja inativar:<br />
                                                                        <span
                                                                            style="color:#DC4C64; font-weight: bold;">{{ $informacao->nome_pessoa }}</span>?
                                                                    </label>
                                                                    <br />
                                                                </center>
                                                                <center>
                                                                    <div class="mb-2 col-10">
                                                                        <label class="col-form-label">Insira o motivo da
                                                                            <span
                                                                                style="color:#DC4C64">inativação:</span></label>
                                                                        <select class="form-select teste1"
                                                                            name="motivo_entrevista" required>
                                                                            @foreach ($motivo as $motivos)
                                                                                <option value="{{ $motivos->id }}">
                                                                                    {{ $motivos->descricao }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </center>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" data-bs-dismiss="modal"
                                                                    class="btn btn-danger">Cancelar</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Confirmar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                           @else
                                           <a href="#" data-bs-toggle="modal"
                                           data-bs-target="#inativar{{ $informacao->ide }}" type="button"
                                           class="btn btn-outline-danger btn-sm tooltips disabled" >
                                           <span class="tooltiptext">Inativar</span>
                                           <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                                       </a>
                                        @endif

                                        {{-- fim modal de inativação --}}


                                        {{-- Modal AFE --}}
                                        <div class="modal fade" id="modalAFE{{ $informacao->ide }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                            style="color: green;">Confirmar
                                                            Finalização </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja finalizar <p style="color: green;">
                                                            {{ $informacao->nome_pessoa }}&#63;</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Sair</button>
                                                        <a type="button" class="btn btn-danger"
                                                            href="/nao-aceito-entrevista/{{ $informacao->ide }}">Cancelar
                                                            tratamento</a>
                                                        <a type="button" class="btn btn-primary"
                                                            href="/finalizar-entrevista/{{ $informacao->ide }}">Confirmar
                                                            tratamento</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Fim Modal AFE --}}



                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div class="d-flex justify-content-center">
      
            </div>
        </div>
    </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            if ({{ $pesquisaValue == 'limpo' }}) { //Deixa o select status como padrao vazio
                $(".teste").prop("selectedIndex", -1);
            }
        })
    </script>
@endsection

@section('footerScript')
@endsection
