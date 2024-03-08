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
                <div>
                    <form action="{{ route('start') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">
                            <div class="col"><br />

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
                                <th class="col">STATUS</th>
                                <th class="col">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px; color:#000000; text-align: center;">
                            @foreach ($informacoes as $informacao)
                                <tr>
                                    <td>{{ $informacao->ide }}</td>{{-- Traz o ID do encaminhamento --}}
                                    <td>{{ $informacao->nome_pessoa }}</td>{{-- Traz o nome do encaminhado --}}
                                    <td>{{ !is_null($informacao->data) ? date('d-m-Y', strtotime($informacao->data)) : '--' }}</td>
                                    {{-- Valida a data e transforma para o padrão brasileiro --}}
                                    <td>{{ !is_null($informacao->hora) ? date('G:i', strtotime($informacao->hora)) : '--' }}</td>
                                    {{-- Valida a hora e transforma para o formato 24h --}}
                                    <td>{{ $informacao->status }}</td>
                                    <td>
                                        <a href="{{ route('criar-evangelho', ['id' => $informacao->ide]) }}" type="button"
                                            class="btn btn-outline-success btn-sm" data-tt="tooltip" data-placement="top"
                                            title="Agendar">
                                            <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                         </a>

                                        <a href="/editar-evangelho/{{ $informacao->ide }}" type="button"
                                            class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top"
                                            title="Editar">
                                            <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                        </a>
                                        <a href="/visualizar-evangelho/{{ $informacao->ide }}" type="button"
                                            class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top"
                                            title="Visualizar">
                                            <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                        </a>

                                        <a href="/inativar-evangelho" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#confirmacaoDelecao"
                                        onclick="confirmarExclusao('{{ $informacao->ide }}', '{{ $informacao->nome_pessoa }}')"
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
        </div>
    </div>
@endsection

@section('footerScript')
@endsection
