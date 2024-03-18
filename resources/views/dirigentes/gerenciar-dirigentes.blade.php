@extends('layouts.app')

@section('title')
    Gerenciar Dirigentes
@endsection

@section('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container-fluid";>

    <div class="container";>

        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR DIRIGENTES</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <form action="" class="form-horizontal mt-4" method="GET">
                    <div class="row">
                        <div class="col-4">Nome
                            <input class="form-control" type="text" maxlength="45"
                                oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="1" name="nome" value={{ $pesquisaNome }}>
                        </div>
                        <div class="col-2">CPF
                            <input class="form-control" type="text" maxlength="45"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                id="1" name="cpf" value={{ $pesquisaCpf }}>
                        </div>

                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                value="Pesquisar">
                            <a href="/gerenciar-dirigentes"><input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>
                </form>
                <a href="/incluir-dirigentes"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                        type="button" value="Novo Atendente+"></a>
            </div>
        </div>
    </div>
    <hr>
    Quantidade filtrada:{{ $conta }}
    <div class="table">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
            <thead style="text-align: center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th class="col">NOME</th>
                    <th class="col">CPF</th>
                    <th class="col">AÇÕES</th>
                </tr>
            </thead>
            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                @foreach ($dirigente as $dirigentes)
                    <tr>
                        <td>{{ $dirigentes->nome_completo }}</td>
                        <td>{{ str_pad($dirigentes->cpf, 11, '0', STR_PAD_LEFT) }}</td>

                        <td scope="">

                            <a href="/editar-dirigentes/{{ $dirigentes->id }}" type="button"
                                class="btn btn-outline-warning btn-sm"  data-tt="tooltip" data-placement="top" title="Editar">
                                <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                            </a>

                            <a href="/visualizar-dirigentes/{{ $dirigentes->id }}" type="button"
                                class="btn btn-outline-primary btn-sm"  data-tt="tooltip" data-placement="top" title="Editar">
                                <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                            </a>

                            <button btn btn-outline-danger btn-sm data-bs-toggle="modal"
                            data-bs-target="#confirmarExclusao{{ $dirigentes->id }}" data-tt="tooltip"
                            data-placement="top" title="Excluir" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                        </button>
                        {{-- Fim do botão Modal Exclusão --}}

                        {{-- Modal Exclusao --}}
                            <div class="modal fade" id="confirmarExclusao{{ $dirigentes->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel" style="color:red;">Confirmação
                                                de
                                                Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja excluir o dirigente <p style="color:red;">
                                                {{ $dirigentes->nome_completo }}&#63;</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-danger"
                                                href="/excluir-dirigentes/{{ $dirigentes->id }}">Confirmar
                                                Exclusão</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <script>
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            </script>
@endsection
