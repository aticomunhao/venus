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
                                CADASTRAR MÉDIUM
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="col-12">
                                <div class="row justify-content-center">
                                    <form class="form-horizontal mt-2" method="post" action="/incluir-mediuns">
                                        @csrf
                                        <div class="col-1 text-end offset-10">
                                            Status
                                            <label for="status"></label>
                                            <div class="col-1 text-end offset-11">
                                                <label for="status"></label>
                                                <input type="checkbox" name="status" style="text-align: right;" data-toggle="toggle" data-onlabel="A" data-offlabel="D" data-onstyle="success" data-offstyle="">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col">
                                                    Nome
                                                    <select class="form-select" aria-label=".form-select-lg example" name="id_pessoa">
                                                        @foreach ($pessoas as $pessoa)
                                                            <option value={{$pessoa->id}}>{{$pessoa->nome_completo}}</option>
                                                        @endforeach
                                                    </select>
                                                    <br>
                                                </p>
                                            </div>

                                            <div class="col">
                                                CPF
                                                <select class="form-select" aria-label=".form-select-lg example" name="cpf">
                                                    @foreach ($pessoas as $pessoa)
                                                        <option value={{$pessoa->id}}>{{$pessoa->cpf}}</option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>

                                            <div class="col">
                                                Data nascimento
                                                <select class="form-select" aria-label=".form-select-lg example" name="dt_nascimneto">
                                                    @foreach ($pessoas as $pessoa)
                                                        <option value={{$pessoa->id}}>{{$pessoa->cpf}}</option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>
                                        </div>
                                    <div class="row">
                                        <div class="col">
                                            SEXO
                                            <select class="form-select" aria-label=".form-select-lg example" name="sexo">
                                                @foreach ($pessoas as $pessoa)
                                                    <option value={{$pessoa->id}}>{{$pessoa->sexo}}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                        </div>
                                        <div class="col">
                                            EMAIL
                                            <select class="form-select" aria-label=".form-select-lg example" name="email">
                                                @foreach ($pessoas as $pessoa)
                                                    <option value={{$pessoa->id}}>{{$pessoa->email}}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                        </div>
                                        <br>
                                        <div class="col">
                                            Tipo mediunidade
                                            <select class="form-select" aria-label=".form-select-lg example" name="id_tp_mediunidade">
                                                @foreach ($tipo_mediunidade as $tipo)
                                                    <option value={{$tipo->id}}>{{$tipo->tipo}}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                        </div>
                                        <br>
                                    </div>
                                        <div class="row justify-content-center">
                                            <div class="d-grid gap-1 col-4 mx-auto">
                                                <br>
                                                <a class="btn btn-danger" href="/gerenciar-mediuns" role="button">Cancelar</a>
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
@endsection
