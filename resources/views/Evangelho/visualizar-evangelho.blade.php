@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">EDITAR EVANGELHO</div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/visualizar-evangelho/{{ $encaminhamento->id }}">
                @csrf

                <div class="row mb-5">
                    <div class="col">
                        <label for="id_encaminhamento" class="form-label">Nome</label>
                        <select class="form-select" id="id_encaminhamento" name="id_encaminhamento"disabled>
                            <option value="{{ $encaminhamento->id }}">{{ $evangelho->nome_completo }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-select" id="data" name="data"  value="{{ $evangelho->data }}">
                    </div>
                    <div class="col">
                        <label for="hora" class="form-label">Hora</label>
                        <input type="time" class="form-select" id="hora" name="hora" value="{{ $evangelho->hora }}" >
                    </div>
                </div>
                <br>
                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-evangelho" role="button">Cancelar</a>
                    </div>
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>


            </form>
        </div>
    </div>
</div>
@endsection
