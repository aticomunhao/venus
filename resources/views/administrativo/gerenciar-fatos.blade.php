@extends('layouts.app')

@section('title') Gerenciar Grupos @endsection

@section('content')
<div class="container-fluid">
  <br>
    <div class="row" style="text-align:center;">
        <h4 class="card-title col-10 " class="card-title"
          style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR FATOS
        </h4>
        <div class="col" style="text-align: right">
            <a href="/criar-fatos" class="btn btn-success btn-sm" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">Novo Fato</a>
        </div>
        <br><br>
        <div class="table">
          <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
            <th scope="col">ID</th>
            <th scope="col">DESCRIÇÃO</th>
            <th scope="col">AÇÕES</th>
            @foreach ($lista as $listas)
                <tr>
                    <td>{{ $listas->id }}</td>
                    <td>{{ $listas->descricao }}</td>
                    <td>
                        <a href="/editar-fatos/{{ $listas->id }}" type="button"
                                class="btn btn-outline-warning btn-sm"><i class="bi bi-pen"
                                    style="font-size: 1rem; color:#000;"></i>
                        </a>
                        <a href="/deletar-fatos/{{$listas->id}}" type="button"
                                class="btn btn-outline-danger btn-sm"><i
                                    class="bi bi-x-circle"
                                    style="font-size: 1rem; color:#000;"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
      </div>
</div>
@endsection
