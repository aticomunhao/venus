@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <div class="row" style="text-align:center;">
        <h4 class="card-title col-12" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR MÉDIUNS
        </h4>
        <div class="col">
            <form action="{{ route('lista') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <label for="nome_pesquisa"></label>
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar nome" value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                            <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-mediuns"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                        </form>

                             <a href="/criar-mediuns" class="btn btn-success btn-sm ms-2" id="name" type="button">Novo médium</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">ID</th>
                            <th class="col">NOME</th>
                            <th class="col">TIPO MEDIUNIDADE</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($medium as $mediuns)
                        <tr>
                            <td>{{$mediuns->id}}</td>
                            <td>{{$mediuns->nome_completo}}</td>
                            <td>{{$mediuns->tipo}}</td>
                            <td>{{$mediuns->status ? 'Ativo' : 'Inativo'}}</td>
                            <td>
                                <a href="/editar-mediuns/{{$mediuns->id}}" type="button" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                <a href="/visualizar-mediuns/{{$mediuns->id}}" type="button" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-search" style="font-size: 1rem;color:#000;" data-bs-toggle="modal" data-bs-target="#pessoa"></i>
                                </a>
                                <a href="/deletar-mediuns/{{$mediuns->id}}" type="button" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
