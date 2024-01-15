@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="/venus/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                <form class="form-horizontal mt-2" method="post" action="{{ route('medium.store') }}">
                    @csrf

                    <div class="row mt-3">
                        <div class="col-4">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-select lista" aria-label=".form-select-lg example" name="id_pessoa">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <label for="id_setor" class="form-label">Setor</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="id_setor">
                                        @foreach ($setor as $setores)
                                            <option value="{{ $setores->ids }}">{{ $setores->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="id_funcao" class="form-label">Função</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="id_funcao">
                                        @foreach ($tipo_funcao as $funcao)
                                            <option value="{{ $funcao->idf }}">{{ $funcao->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="id_grupo" class="form-label">Nome grupo</label>
                                    <select class="form-select" aria-label=".form-select-lg example" name="id_grupo">
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                        <label for="tipo_stautus_pessoa" class="form-label">Status</label>
                                        <select class="form-select" aria-label=".form-select-lg example" name="tipo_status_pessoa">
                                            @foreach ($tipo_status_pessoa as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->tipos }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="id_mediunidade" class="form-label"></label>
                            <div class="table-responsive">
                                <div class="table">
                                    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                        <thead>
                                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                <th scope="col"></th>
                                                <th scope="col">Tipo de Mediunidade</th>
                                                <th scope="col">Data que manifestou</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tipo_mediunidade as $tipo)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="id_tp_mediunidade[]"
                                                                value="{{ $tipo->id }}" id="tipo_{{ $tipo->id }}">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $tipo->tipo }}
                                                    </td>
                                                    <td>
                                                        <div class="form-group data_manifestou" name="id_mediunidade_medium"
                                                            id="data_inicio_{{ $tipo->id }}">
                                                            <input type="hidden" name="id_medium" value="{{ $id_medium }}">
                                                            @if(old("data_inicio.$tipo->id"))
                                                                @foreach(old("data_inicio.$tipo->id") as $oldDate)
                                                                    <input type="date" class="form-control form-control-sm"
                                                                        name="data_inicio[{{ $tipo->id }}][]"
                                                                        value="{{ $oldDate }}" required="required">
                                                                @endforeach
                                                            @else
                                                                <input type="date" class="form-control form-control-sm"
                                                                    name="data_inicio[{{ $tipo->id }}][]"
                                                                    value="" required="required">
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <br>

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

<script>
    jQuery(document).ready(function() {
        jQuery('.lista').select2({

            height: '150%',
            width: "100%",
        });
    });
</script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.data_manifestou')
                .hide()
                .find('input[type=date]')
                .prop('required', false);

            $('[name^=id_tp_mediunidade]').change(function() {
                $('.data_manifestou')
                    .hide()
                    .find('input[type=date]')
                    .prop('required', false);

                $('[name^=id_tp_mediunidade]:checked').each(function() {
                    var tipoId = $(this).val();
                    $('#data_inicio_' + tipoId)
                        .show()
                        .find('input[type=date]')
                        .prop('required', true);
                });
            });
        });
    </script>
@endsection
