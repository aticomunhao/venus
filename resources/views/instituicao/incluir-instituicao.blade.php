@extends('layouts.app')

@section('title')
    Criar Estudos Externos
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form class="form-horizontal" method="post" action="/salvar-instituicao" enctype="multipart/form-data">
                    @csrf
                    <!-- Card principal -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    INSERIR INSTITUIÇÕES
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="nome_fantasia">Nome Fantasia</label>
                                        <input type="text" class="form-control" name="nome_fantasia" required>
                                    </div>

                                    <div class="col-6">
                                        <label for="razao_social">Razão Social</label>
                                        <input type="text" class="form-control" name="razao_social" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="cnpj">CNPJ</label>
                                        <input type="text" class="form-control" name="cnpj" required>
                                    </div>

                                    <div class="col-4">
                                        <label for="email_contato">Email de Contato</label>
                                        <input type="email" class="form-control" name="email_contato" required>
                                    </div>

                                    <div class="col-4">
                                        <label for="site">Site</label>
                                        <input type="url" class="form-control" name="site">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Botões de ação -->
                    <br>
                    <div class="row mb-3">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-institucao" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
