@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <br>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                VISUALIZAR MÉDIUM
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="col">
                                <div class="row justify-content-center">

                                        <div class="col-1 text-end offset-11">Status <label for="status"></label>
                                            <input type="checkbox" name="status" style=text-align: right;
                                                data-toggle="toggle" data-onlabel="A" data-offlabel="D"
                                                data-onstyle="success" data-offstyle=""@checked($medium->status)>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                Nome
                                                <select class="form-select" name="id_pessoa" aria-label="form-select-lg example"disabled>
                                                    @foreach ($pessoas as $pessoa)
                                                        <option value="{{$pessoa->id}}" {{ $medium->id_pessoa == $pessoa->id ? 'selected' : '' }}>
                                                            {{$pessoa->nome_completo}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>
                                            <div class="col">
                                                Tipo mediunidade
                                                <select class="form-select" aria-label=".form-select-lg example" name="id_tp_mediunidade" disabled>
                                                    @foreach ($tipo_mediunidade as $tipo)
                                                        <option value="{{$tipo->id}}" {{ $medium->id_tp_mediunidade == $tipo->id ? 'selected' : '' }}>
                                                            {{$tipo->tipo}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="d-grid gap-1 col-4 mx-auto">
                                                <br>
                                                <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Fechar</a>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
