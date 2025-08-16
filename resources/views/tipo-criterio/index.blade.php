@extends('layouts.app')
@section('content')
    <br>
    <div class="container">
        <div class="card" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            <div class="card-header">
                Gerenciar Critério de Atividade
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    {{-- // Formulário de Pesquisa --}}
                    <div class="row justify-content-between">
                        <div class="col-sm-12 col-md-3">
                            <a href="{{ route('criar.tipo_criterio_controller') }}" class="btn btn-primary">Adicionar
                                Critério</a>
                        </div>


                    </div>
                </h5>

                <p class="card-text">
                <table class="table  table-striped table-bordered border-secondary table-hover align-middle">
                    <thead>
                        <thead style="text-align: center;">
                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                <th class="col-auto">ID</th>
                                <th class="col-auto">CRITÉRIO</th>
                                <th class="col-auto">TIPO</th>
                                <th class="col-auto">AÇÕES</th>
                            </tr>
                        </thead>
                    </thead>
                    <tbody>
                        @foreach ($tipos_criterio as $tipo)
                            <tr style="text-align: center;">
                                <td>{{ $tipo->id }}</td>
                                <td>{{ $tipo->descricao }}</td>
                                <td>{{ $tipo->tipo_valor }}</td>
                                <td>
                                    <a href="{{ route('editar.tipo_criterio_controller', $tipo->id) }}"
                                        class="btn btn-sm btn-warning">Editar</a>
                                    {{-- <form action="{{ route('excluir.tipo_criterio_controller', $tipo->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                    </form> --}}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                </p>
            </div>
        </div>
    </div>
@endsection
