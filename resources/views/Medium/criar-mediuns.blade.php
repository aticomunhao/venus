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

                                        </div>
                                        <br>
                                        <div class="col">Nome
                                            <select class= "form-select " aria-label=".form-select-lg example" name="id_pessoa">
                                                <option selected></option>
                                            </div>
                                                @foreach ($pessoas as $pessoa)
                                                    <option value={{$pessoa->id}}>{{$pessoa->nome_completo}}</option>
                                                @endforeach

                                           </select>

                                           <br>
                                            <div class="col">Tipo mediunidade
                                                <select class= "form-select " aria-label=".form-select-lg example" name="id_tp_mediunidade">
                                                    <option selected></option>
                                                </div>
                                                    @foreach ($tipo_mediunidade as $tipo)
                                                        <option value={{$tipo->id}}>{{$tipo->tipo}}</option>
                                                    @endforeach



                                                </select>
                                                <br>
                                            </div>

                                                    <br>

                                                </div>
                                                <br>

                                                <br>
                                                  <div class="row justify-content-center">
                                                        <div class="d-grid gap-1 col-4 mx-auto">
                                                            <br>
                                                            <a class="btn btn-danger" href="/gerenciar-mediuns"
                                                                role="button">Cancelar</a>
                                                        </div>
                                                        <div class="d-grid gap-2 col-4 mx-auto">
                                                            <br>
                                                            <button class="btn btn-primary">Confirmar</button>
                                                        </div>
                                                    </div>
                                                </div>



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