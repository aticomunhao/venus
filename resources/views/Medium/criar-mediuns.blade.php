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


                    <div class="row ">
                        <div class="col">

                            Nome
                            <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{$pessoa->id}}">{{$pessoa->nome_completo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">

                            Tipo de Mediunidade
                            <select class="form-select" aria-label=".form-select-lg example" name="id_tp_mediunidade">
                                @foreach ($tipo_mediunidade as $tipo)
                                    <option value="{{$tipo->id}}">{{$tipo->tipo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                   <div class="row ">
                    <div class="col">
                        <br>
                        Status
                        <input class="form-control" type="text" name="status" id="status">

                        </select>
                    </div>
                    <br>
                    <div class="col">
                        <br>
                        Motivo
                        <input class="form-control" type="text"  name="motivo_status" id="moltivo_status">

                        </select>
                    </div>
                </div>

                </body>
                    <div class="row mt-2 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <br>
                            <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <br>
                            <button class="btn btn-primary" type="submit">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
