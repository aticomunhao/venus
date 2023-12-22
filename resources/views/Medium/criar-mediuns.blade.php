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
                <form class="form-horizontal mt-2" method="post" action="/incluir-mediuns">
                    @csrf

                    <div class="row mt-3">
                        <div class="col-8">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->id }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="status">
                                @foreach ($tipo_status_pessoa as $status)
                                    <option value="{{ $status->tipo }}">{{ $status->tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label ">Tipos de Mediunidade</label>
                            @foreach ($tipo_mediunidade as $tipo)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="id_tp_mediunidade[]" value="{{ $tipo->id }}" id="tipo_{{ $tipo->id }}">
                                    <label class="form-check-label" for="tipo_{{ $tipo->id }}">{{ $tipo->tipo }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            @foreach ($tipo_mediunidade as $tipo)
                                <div class="form-group" id="data_manifestou_{{ $loop->index + 1 }}">
                                    <label for="data_manifestou_mediunidade[{{ $tipo->id }}]" class="form-label small mb-0">Data que manifestou {{ $tipo->tipo }}</label>
                                    <input type="date" class="form-control form-control-sm" name="data_manifestou_mediunidade[{{ $tipo->id }}]" required>
                                </div>
                            @endforeach
                        </div>


                    </div>
                    <br>
                    <div class="row mt-1 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button class="btn btn-primary" type="submit">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
      $(document).ready(function () {
    console.log('Documento pronto.');

    $('[id^=data_manifestou_]').hide();

    $('[name^=id_tp_mediunidade]').change(function () {
        console.log('Checkbox alterado.');
        var numSelecoes = $('[name^=id_tp_mediunidade]:checked').length;
        console.log('Número de seleções:', numSelecoes);
        $('[id^=data_manifestou_]').hide();
        for (var i = 1; i <= numSelecoes; i++) {
            $('#data_manifestou_' + i).show();
            $('#data_manifestou_' + i + ' input').prop('disabled', false);
        }
    });

    $('form').submit(function () {
        console.log('Formulário enviado com sucesso!');
    });
});

    </script>

@endsection
