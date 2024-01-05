@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        CADASTRAR MÉDIUM
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="mediumForm" class="form-horizontal mt-2" method="post" action="{{ route('medium.store') }}">
                    @csrf

                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->id }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <label for="id_setor" class="form-label">Setor</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="id_setor">
                                        @foreach ($setor as $setores)
                                            <option value="{{ $setores->nome }}">{{ $setores->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="id_funcao" class="form-label">Função</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="id_funcao">
                                        @foreach ($tipo_funcao as $funcao)
                                            <option value="{{ $funcao->nome }}">{{ $funcao->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="status" class="form-label text-start">Status</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="status"
                                            id="status" required="required">
                                        <option value="1">Ativo</option>
                                        <option value="2">Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipos de Mediunidade</label>
                            @foreach ($tipo_mediunidade as $tipo)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="id_tp_mediunidade[]"
                                           value="{{ $tipo->id }}" id="tipo_{{ $tipo->id }}">
                                    <label class="form-check-label"
                                           for="tipo_{{ $tipo->id }}">{{ $tipo->tipo }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            @foreach ($tipo_mediunidade as $tipo)
                                <div class="form-group data_manifestou" id="data_inicio_{{ $tipo->id }}">
                                    <label for="data_inicio[{{ $tipo->id }}]"
                                           class="form-label small mb-0">{{ $tipo->tipo }}</label>

                                    @php
                                        $mediunidadeData = collect($mediunidade_medium)->firstWhere('tipo_mediunidade_id', $tipo->id);
                                    @endphp

                                    @if ($mediunidadeData)
                                        <input type="date" class="form-control form-control-sm"
                                               name="data_inicio[{{ $tipo->id }}]"
                                               value="{{ $mediunidadeData['data_inicio'] }}" required="required">
                                    @else
                                        <input type="date" class="form-control form-control-sm"
                                               name="data_inicio[{{ $tipo->id }}]" required="required">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mt-1 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.data_manifestou').hide().prop("required", false);

            $('[name^=id_tp_mediunidade]').change(function () {
                $('.data_manifestou').hide();
                $('[name^=id_tp_mediunidade]:checked').each(function () {
                    var tipoId = $(this).val();
                    $('#data_inicio_' + tipoId).show().prop("required", true);
                });
            });

            $('#mediumForm').submit(function () {
                console.log('Formulário enviado.');
            });
        });
    </script>
@endsection
