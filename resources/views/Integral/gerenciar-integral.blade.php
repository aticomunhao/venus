@extends('layouts.app')
@section('title', 'Gerenciar Assistidos Integral')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ASSISTIDOS
            INTEGRAL
        </h4>

        <div class="col-12">
            <form action="/gerenciar-integral" class="form-horizontal mt-4" method="GET">
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
                                <option value="{{ $dirigente->id }}"
                                    {{ $dirigente->id == $selected_grupo ? 'selected' : '' }}>{{ $dirigente->nome }} -
                                    {{ $dirigente->dia }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                        <a href="/gerenciar-integral"><input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Limpar"></a>
                        <a href="/gerenciar-membro/{{ $selected_grupo }}"><input class="btn btn-primary btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Gerenciar Grupo"></a>


                    </div>
                </div>

            </form>

        </div>

        <hr>
        <span style="font-size: 14px; color:red">*Assistidos sem PTD</span>
        <div class="table">
            <table
                class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th>ID</th>
                    <th>NOME</th>
                    <th>NOME GRUPO</th>
                    <th>HORÁRIO INÍCIO</th>
                    <th>HORÁRIO FIM</th>
                    <th>FIM</th>
                    <th>STATUS</th>
                    <th>MACA</th>
                    <th>AÇÕES</th>
                </tr>

                <tbody>
                    @foreach ($encaminhamentos as $encaminhamento)
                        @if ($encaminhamento->ptd)
                            <tr>
                            @else
                            <tr class="table-danger">
                        @endif
                        <td>{{ $encaminhamento->id }}</td>
                        <td>{{ $encaminhamento->nome_completo }}</td>
                        <td>{{ $encaminhamento->nome }}</td>
                        <td>{{ $encaminhamento->h_inicio }}</td>
                        <td>{{ $encaminhamento->h_fim }}</td>
                        <td>{{ $encaminhamento->contagem ? $encaminhamento->contagem : 'PERMANENTE' }}</td>
                        <td>{{ $encaminhamento->status }}</td>
                        <td>{{ $encaminhamento->maca }}</td>
                        <td>

                            @if ($encaminhamento->dt_fim == null)
                                <button type="button" class="btn btn-outline-warning btn-sm tooltips"
                                    data-bs-toggle="modal" data-bs-target="#modalA{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Declarar Alta</span>
                                    <i class="bi bi-clipboard-plus" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @elseif($encaminhamento->id_status == 1)
                                <button type="button" disabled class="btn btn-outline-warning btn-sm tooltips"
                                    data-bs-toggle="modal" data-bs-target="#modalA{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Declarar Alta</span>
                                    <i class="bi bi-infinity" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-warning btn-sm tooltips"
                                    data-bs-toggle="modal" data-bs-target="#modal{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Sem limite</span>
                                    <i class="bi bi-infinity" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @endif
                            @if ($encaminhamento->id_status == 1)
                                <!-- Button trigger modal (Desabilitado) -->
                                <button type="button" style="font-size: 1rem; color:#000;"
                                    class="btn btn-outline-warning btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#maca{{ $encaminhamento->id }}" disabled>
                                    <span class="tooltiptext">Maca</span>
                                    <i class="bi bi-moon-stars" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @else
                                <!-- Button trigger modal (Ativo) -->
                                <button type="button" style="font-size: 1rem; color:#000;"
                                    class="btn btn-outline-warning btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#maca{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Maca</span>
                                    <i class="bi bi-moon-stars" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @endif



                           


                            </a>

                            <form action="/maca-integral/{{ $encaminhamento->id }}">
                                <!-- Modal -->
                                <div class="modal fade" id="maca{{ $encaminhamento->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color:orange">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel" style=" color:white">
                                                    Indicar Maca</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <center>
                                                    <div class="row col-10">
                                                        <div class="mb-3">
                                                            <label for="recipient-name" class="col-form-label"
                                                                style="font-size:17px">Designar maca
                                                                para:<br /><span
                                                                    style="color:orange">{{ $encaminhamento->nome_completo }}</span>&#63;</label>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="col-form-label">Escolha a maca
                                                                <span style="color:orange">desejada:</span></label>
                                                            <?php $i = 0; ?>
                                                            <select class="form-select" id="maca" name="maca"
                                                                type="number">
                                                                @foreach ($macasDisponiveis as $maca)
                                                                    @if ($encaminhamento->maca < $maca and $encaminhamento->maca > $i)
                                                                        <option value="{{ $encaminhamento->maca }}"
                                                                            selected>
                                                                            {{ $encaminhamento->maca }}</option>
                                                                    @endif
                                                                    <option value="{{ $maca }}">
                                                                        {{ $maca }}</option>
                                                                    {{ $i = $maca }};
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </center>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            {{--  Modal de Exclusao --}}
                            <div class="modal fade" id="modal{{ $encaminhamento->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#DC4C64">
                                            <h5 class="modal-title" id="exampleModalLabel" style=" color:white">
                                                Retirar
                                                Tempo Limite</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja retirar o limite de semanas de <br /><span
                                                style="color:#DC4C64; font-weight: bold;">{{ $encaminhamento->nome_completo }}</span>&#63;

                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-primary"
                                                href="/alta-integral/{{ $encaminhamento->id }}">Confirmar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Fim Modal de Exclusao --}}

                            {{--  Modal de Exclusao --}}
                            <div class="modal fade" id="modalA{{ $encaminhamento->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#DC4C64">
                                            <h5 class="modal-title" id="exampleModalLabel" style=" color:white">
                                                Declarar Alta</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja declarar alta para <br /><span
                                                style="color:rgb(196, 27, 27);">{{ $encaminhamento->nome_completo }}</span>&#63;

                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-primary"
                                                href="/alta-integral/{{ $encaminhamento->id }}">Confirmar
                                            </a>
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


@endsection
