@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                CADASTRAR GRUPOS
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <form class="form-horizontal mt-2" method="post" action="/incluir-grupos">
                                @csrf



                                <div class="row">
                                    <div class="col-6">
                                        Nome
                                        <input type="text" class="form-control" id="nome" name="nome"  maxlength="30" required="required">
                                    </div>

                                    <div class="col">
                                        Tipo de tratamento
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_tratamento" required="required">
                                            @foreach ($tipo_tratamento as $tipo)
                                            <option value="{{ $tipo->id }}"> {{ $tipo->descricao }} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col">
                                        Status
                                        <select class="form-select" aria-label=".form-select-lg example" name="status_grupo"required="required">
                                            @foreach ($tipo_status_grupo as $tipos)
                                            <option value="{{ $tipos->ids}}"> {{ $tipos->descricao }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="row">
                                    <div class="col">
                                        <br>
                                        Hora Início
                                        <input type="time" class="form-control" id="h_inicio" name="h_inicio" required="required">
                                    </div>

                                    <div class="col">
                                        <br>
                                        Hora Fim
                                        <input type="time" class="form-control" id="h_fim" name="h_fim" required="required">
                                    </div>
                                      <br>
                                      <div class="col">
                                        <br>
                                        Max atendido
                                        <input type="number" class="form-control" id="max_atend" min="1" max="100" name="max_atend" oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3);" required="required">
                                    </div>

                                    <div class="col">
                                        <br>
                                        Tipo grupo
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_tipo_grupo" required="required">
                                            @foreach ($tipo_grupo as $item)
                                            <option value="{{ $item->idg }}">{{ $item->nm_tipo_grupo }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                        <br>
                                        <div class="row justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <br>
                                        <a class="btn btn-danger" href="/gerenciar-grupos" role="button">Cancelar</a>
                                    </div>
                                    <div class="d-grid gap-2 col-4 mx-auto">
                                        <br>
                                        <button class="btn btn-primary">Confirmar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
