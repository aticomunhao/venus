@extends('layouts.app')

@section('content')


    <div class="container-fluid">
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
                                <option value="{{ $dirigente->id }}" {{ $dirigente->id == $selected_grupo ? 'selected' : '' }}>{{ $dirigente->nome }} - {{ $dirigente->dia }}</option>
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
                        <a href="/gerenciar-membro/{{ $selected_grupo }}"><input class="btn btn-primary btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Gerenciar Grupo"></a>


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

                                <button type="button"
                                    class="btn btn-outline-warning btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modal{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Declarar Alta</span>
                                    <i class="bi bi-clipboard-plus" style="font-size: 1rem; color:#000;"></i>
                                </button>
                                <a href="/visualizar-pti/{{ $encaminhamento->id }}" type="button"
                                    class="btn btn-outline-primary btn-sm tooltips" >
                                    <span class="tooltiptext">Visualizar</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>



                            </a>


                            {{--  Modal de Exclusao --}}
                            <div class="modal fade" id="modal{{ $encaminhamento->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:rgb(196, 27, 27);">
                                            <h5 class="modal-title" id="exampleModalLabel" style=" color:white">Confirmação de
                                                Alta</h5>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja declarar alta para <br /><span
                                                style="color:rgb(196, 27, 27);">{{ $encaminhamento->nome_completo }}</span>&#63;

                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-danger"
                                                href="/alta-pti/{{ $encaminhamento->id }}">Confirmar
                                                Alta</a>
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

