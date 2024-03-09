@extends('layouts.app')

@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">AGENDAR EVANGELHO</div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="{{ route('agendar') }}">
                    @csrf
                    {{-- <div class="row mb-5">
                        <div class="col">
                            <label for="id_encaminhamento" class="form-label">Nome</label>
                            <select class="form-control" id="id_encaminhamento" name="id_encaminhamento" disabled>
                                @foreach ($informacoes as $informacao)
                                    <option value="{{ $informacao->id_assistido }}">{{ $informacao->nome_pessoa }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    <div class="row mb-3">
                        <div class="col">
                            <label for="number" class="form-label">Quantidade de adultos</label>
                            <input type="number" class="form-control" id="qtd_adultos" name="qtd_adultos">
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="number" class="form-label">Quantidade de crianças</label>
                                <input type="number" class="form-control" id="qtd_criancas" name="qtd_criancas">
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="text" class="form-label">Grupo</label>
                                    <input type="text" class="form-control" id="id_grupo" name="id_grupo">
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="data" class="form-label">Data</label>
                                        <input type="date" class="form-control" id="data" name="data">
                                    </div>
                                    <div class="col">
                                        <label for="hora" class="form-label">Hora</label>
                                        <input type="time" class="form-control" id="hora" name="hora">
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
                                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
