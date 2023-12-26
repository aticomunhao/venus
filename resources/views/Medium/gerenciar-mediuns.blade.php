@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR MÉDIUNS</h4>

    <div class="col-12">
        <form action="{{route('lista')}}" class="form-horizontal mt-4" method="GET">
            <div class="row">
                <div class="col-3">
                    Nome
                    <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar nome {{ request('nome_pesquisa') }}">

                </div>

                <div class="col">
                    <br>
                    <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                    <a href="/gerenciar-mediuns"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    <a href="/criar-mediuns"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;" type="button" value="Novo médium +"></a>
                </div>
            </div>
        </form>

    </div>

    <hr>

    <div class="table">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                <th>ID</th>
                <th>NOME</th>
                <th>TIPO MEDIUNIDADE</th>
                <th>FUNÇÃO</th>
                <th>STATUS</th>
                <th>AÇÕES</th>
            </tr>

            <tbody>
                @foreach ($medium as $mediuns)
                <tr>
                    <td>{{$mediuns->idm}}</td>
                    {{-- <td>{{$mediuns->id_grupo}}</td> --}}
                    <td>{{$mediuns->nome_completo}}</td>
                    <td>{{$mediuns->tipo}}</td>
                    <td>{{$mediuns->nome}}</td>
                    <td>{{$mediuns->status ? 'Ativo' : 'Inativo' }}</td>
                    <td>
                        <a href="/editar-mediuns/{{$mediuns->idm}}" type="button" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i>
                        </a>
                        <a href="/visualizar-mediuns/{{$mediuns->idm}}" type="button" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-search" style="font-size: 1rem;color:#000;" data-bs-toggle="modal" data-bs-target="#pessoa"></i>
                        </a>
                        <a href="/deletar-mediuns/{{$mediuns->idm}}" type="button" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
