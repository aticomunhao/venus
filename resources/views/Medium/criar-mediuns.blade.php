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
                        <div class="col-5">
                           Nome
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->id }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            Status
                            <select class="form-select" aria-label=".form-select-lg example" name="status">
                                @foreach ($tipo_status_pessoa as $status)
                                    <option value="{{ $status->tipo }}">{{ $status->tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            Motivo Status
                            <select class="form-select" aria-label=".form-select-lg example" name="motivo_status">
                                @foreach ($tipo_motivo_status_pessoa as $motivo)
                                    <option value="{{ $motivo->motivo }}">{{ $motivo->motivo }}</option>
                                @endforeach
                            </select>
                        </div>

                    <div class="row mt-4">

                        <div class="col">
                            Tipo mediunidade
                            <select class="form-select" aria-label=".form-select-lg example" name="id_tp_mediunidade">
                                @foreach ($tipo_mediunidade as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                    <div class="row mt-3 justify-content-center">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
