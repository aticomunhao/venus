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
                            <select class="form-control" name="id_pessoa" disabled>
                                <option value="{{ $atendente->id }}">{{ $atendente->nome_completo }}</option>
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="row mt-4">
                            <div class="col">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="{{ $atendente->status }}" selected>{{ $atendente->tipos }}</option>
                                    @foreach ($tipo_status_pessoa as $status)
                                        @if ($status->id != $atendente->id && $status->id != $atendente->status)
                                            <option value="{{ $status->id }}">{{ $status->tipo }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="data_fim" class="form-label">Data fim</label>
                                <input type="date" class="form-select" id="dt_fim" name="dt_fim" value="{{ $atendente->dt_fim }}">
                            </div>
                            <div class="col">
                                <label for="motivo_status" class="form-label">Motivo</label>
                                <select class="form-select" aria-label=".form-select-lg example" name="motivo_status" id="motivo_status" >
                                    <option value="" {{ is_null($atendente->motivo_status) ? 'selected' : '' }}></option>
                                    @foreach ($tipo_motivo_status_pessoa as $motivo)
                                        @if ($motivo->motivo == 'mudou' || $motivo->motivo == 'desencarnou')
                                            <option value="{{ $motivo->id }}" {{ $atendente->motivo_status == $motivo->id ? 'selected' : '' }}>
                                                {{ $motivo->motivo }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col">



                                <div class="col-12 mt-3 mb-3">
                                    <label for="id_grupo" class="form-label">Nome grupo</label>
                                    <select class="form-select select2" aria-label=".form-select-lg example" name="id_grupo[]" id="id_grupo" multiple>
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->id }}" >{{ $grupos->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>





                            </div>
                        </div>







                        <div class="row mt-4 justify-content-center">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                            </div>
                            <div class="d-grid gap-2 col-4 mx-auto">
                                <button type="submit" class="btn btn-primary">Confirmar</button>
                            </div>

                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

                            <script>
                                $(document).ready(function() {

                                    var infos = <?php echo json_encode($info); ?>;
                                    $('.select2').select2( { theme: 'bootstrap-5'});

                                    $('.select2').val();
                                    });
                                    $('.select2').trigger('change');

                                    $.each(infos, function( index, value ) {
                                        alert( index + ": " + value );
                                      });



                            </script>


                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
