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
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="{{ route('criar') }}">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <label for="id_pessoa" class="form-label">Nome </label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa" disabled>
                                @foreach ($pessoas as $pessoas)
                                    <option value="{{ $pessoas->idp }}">{{ $pessoas->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="id_grupo" class="form-label">Nome grupo</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="id_grupo" disabled>
                                @foreach ($grupo as $grupos)
                                    <option value="{{ $grupos->id }}">{{ $grupos->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" aria-label=".form-select-lg example" name="status">
                                @foreach ($tipo_status_pessoa as $status)
                                    <option value="{{ $status->id }}">{{ $status->tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <label for="motivo" class="form-label">Motivo</label>
                        <select class="form-select" aria-label=".form-select-lg example" name="motivo" disabled>
                            @foreach ($atendentes as $atendente)
                                <option value="{{ $atendente->id }}">{{ $atendente->motivo }}</option>
                            @endforeach
                        </select>
                    </div>

                    Data fim
                    <input type="date" class="form-control" id="h_fim" name="data_fim"
                        value="{{ $atendentes[0]->data_fim }}" disabled>
            </div>
            <br>
            <br>
            <div class="row mt-1 justify-content-center">
                <div class="d-grid gap-1 col-4 mx-auto">
                    <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                </div>
                <div class="d-grid gap-2 col-4 mx-auto">
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
@endsection
