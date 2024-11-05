@extends('layouts.app')

@section('title')
    Gerenciar Passes
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align:left; color:gray; font-family:calibri">
            GERENCIAR PASSES
        </h4>
        <br>
        <div class="col-12">
            <div class="row justify-content-center">
                <form method="GET" action="/gerenciar-passe" class="d-flex align-items-center">
                    <div class="col-md-5">
                        <div>Grupo</div>
                        <select class="form-select select2" name="grupo">
                            <option value="">Selecione</option>
                            @foreach ($grupos as $gruposs)
                                <option value="{{ $gruposs->idg }}"
                                    {{ request('grupo') == $gruposs->idg ? 'selected' : '' }}>
                                    {{ $gruposs->nomeg }} - {{ $gruposs->sigla }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <br />
                        <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;"
                            type="submit" value="Pesquisar">
                        <a href="/gerenciar-passe"><input class="btn btn-light btn-sm me-md-2"
                                style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </div>
                </form>
            </div>
        </div>
        <br>
        <div style="text-align:left;">
            <hr />
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center; position: sticky; top: 0; z-index: 10; background-color: #d6e3ff;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            {{-- <th>NR</th> --}}
                            <th>GRUPO</th>
                            <th>DATA</th>
                            <th>HORÁRIO INÍCIO</th>
                            <th>HORÁRIO FIM</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        @foreach ($reuniao as $reuni)
                            <tr>
                                {{-- <td>{{ $reuni->idr }}</td> --}}
                                <td>{{ $reuni->nomeg }}</td>
                                <td>{{ $reuni->nomed }}</td>
                                <td>{{ date('H:i:s', strtotime($reuni->h_inicio)) }}</td>
                                <td>{{ date('H:i:s', strtotime($reuni->h_fim)) }}</td>
                                <td>
                                    <!-- Botão para abrir o modal de presença -->

                                    <button type="button" class="btn btn-outline-success btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#presenca{{ $reuni->idr }}">
                                        <span class="tooltiptext" style="z-index:10000">Novo passe</span>
                                        <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                    {{-- Modal de presença --}}
                                    <div class="modal fade" id="presenca{{ $reuni->idr }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <form method="post" action="/incluir-passe/{{ $reuni->idr }}">
                                            @csrf
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color:#198754;color:white">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Quantidade de
                                                            passes
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="recipient-name" class="col-form-label"
                                                                style="font-size:17px">
                                                                Registrar de quantidade de passes no grupo
                                                                <br />
                                                                <span style="color:#198754">{{ $reuni->nomeg }}</span>&#63;
                                                            </label>
                                                        </div>
                                                        <center>
                                                            <div class="mb-2 col-10">
                                                                <label class="col-form-label">Insira o
                                                                    <span style="color:#198754">número de passes:</span>
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                    name="acompanhantes"  placeholder="0" min="1" max="100" required>
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
                                        </form>
                                    </div>
                                    <a href="/editar-passe/{{ $reuni->idr }}"
                                        class="btn btn-outline-warning btn-sm tooltips">
                                        <span class="tooltiptext" style="z-index:10000">Editar</span>
                                        <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                    </a>

                                    <a href="/visualizar-passe/{{ $reuni->idr }}"
                                        class="btn btn-outline-primary btn-sm tooltips">
                                        <span class="tooltiptext" style="z-index:10000">Visualizar</span>
                                        <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
