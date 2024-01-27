@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        EDITAR ATENDENTE
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-atendente/{{ $atendente->id }}">
                    @csrf

                    <div class="row">
                        <div class="col">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-select" name="id_pessoa">
                                <option value="{{ $atendente->id}}">{{ $atendente->nome_completo }}</option>
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                    <div class="col">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="{{ $atendente->id}}">{{ $atendente->tipos }}</option>
                            @foreach ($tipo_status_pessoa as $status)
                                <option value="{{ $status->id }}">{{ $status->tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                <div class="col">
                    <label for="data_fim" class="form-label">Data fim</label>
                    <input type="date" class="form-select" id="data_fim" name="data_fim" value="{{ $atendente->dt_fim }}">
                </div>
                        <div class="col">
                            <label for="motivo" class="form-label">Motivo</label>
                            <select class="form-select" name="motivo">
                                <option value="{{ $atendente->id}}">{{ $atendente->motivo }}</option>
                                @foreach ($atendente_grupo as $atendenteItem)
                                    <option value="{{ $atendenteItem->id }}">{{ $atendenteItem->motivo }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>


                    <div class="row mt-4">
                        <div class="col">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nome grupo</th>
                                        <th>Adicionar grupos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            @foreach ($gruposAtendente as $grupoAtendente)
                                                <div class="mb-3">
                                                    <select class="form-select" name="id_grupo[]">
                                                        @foreach ($grupo as $grupos)
                                                            <option value="{{ $grupos->id }}"
                                                                @if ($grupos->id == $grupoAtendente->id_grupo) selected @endif>
                                                                {{ $grupos->nome }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="adicionarMaisGrupos" name="adicionar_mais_grupos">
                                                <label class="form-check-label" for="adicionarMaisGrupos">

                                                </label>
                                            </div>
                                            <!-- Container para adicionar os campos dinamicamente -->
                                            <div id="gruposAdicionais" style="display: none;">
                                                <!-- Template do campo de seleção -->
                                                <div class="mb-3 grupo-adicional">
                                                    <label for="novo_grupo" class="form-label">Novo Grupo</label>
                                                    <select class="form-select" name="novo_grupo[]">
                                                        @foreach ($grupo as $grupos)
                                                            <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <style>

                        .table th input[type="checkbox"],
                        .table td input[type="checkbox"] {
                            width: 17px; /* Ajusta a largura do checkbox */
                            height: 17px; /* Ajusta a altura do checkbox */
                            cursor: pointer; /* Adiciona o cursor de ponteiro ao passar sobre o checkbox */
                            border: 2px solid #000; /* Adiciona borda preta ao checkbox */
                        }



                    </style>
                    <div class="row mt-4 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function () {
                                // Ao alterar a opção de adicionar mais grupos
                                $('#adicionarMaisGrupos').on('change', function () {
                                    var isChecked = $(this).prop('checked');

                                    // Mostrar ou ocultar o container de grupos adicionais
                                    $('#gruposAdicionais').toggle(isChecked);

                                    // Se marcado, clona o último grupo adicional e adiciona ao container
                                    if (isChecked) {
                                        var ultimoGrupoAdicional = $('.grupo-adicional').last().clone();
                                        $('#gruposAdicionais').append(ultimoGrupoAdicional);
                                    }
                                    // Se desmarcado, remove todos os grupos adicionais, exceto o primeiro
                                    else {
                                        $('.grupo-adicional:not(:first)').remove();
                                    }
                                });
                            });
                        </script>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
