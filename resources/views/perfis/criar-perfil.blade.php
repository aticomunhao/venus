@extends('layouts.app')
@section('head')
    <title>Incluir Rotas</title>
@endsection
@section('content')
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Incluir Rotas
            </div>
            <div class="card-body">
                <br>
                <div class="row justify-content-start">
                    <form method="POST" action="/armazenar-atendentes-apoio">
                        @csrf
                        <div class="row col-10 offset-1" style="margin-top:none">
                            <div class="col-12">
                                Nome
                                <input type="text" class="form-control" id="nome" name="nome" maxlength="30" required="required">
                                <br />
                            </div>
                            <hr />





                        </div>

                        <center>
                            <div class="table-responsive col-10">
                                <div class="table">
                                    <table
                                        class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                        <thead>
                                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                <th scope="col"></th>
                                                <th scope="col">Rota</th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rotas as $rota)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input checkbox-trigger" type="checkbox"
                                                            name="checkbox[]" id="{{ $rota->id }}"
                                                            value="{{ $rota->id }}">
                                                    </td>

                                                    <td>{{ $rota->nome }}</td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </center>
                        <center>
                            <div class="col-12" style="margin-top: 70px;">
                                <a href="/gerenciar-atendentes-apoio" class="btn btn-danger col-3">
                                    Cancelar
                                </a>
                                <button type = "submit" class="btn btn-primary col-3 offset-3">
                                    Confirmar
                                </button>
                            </div>
                        </center>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@endsection
