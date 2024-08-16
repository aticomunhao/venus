@extends('layouts.app')
@section('title', 'Dar Presença')
@section('content')

    <div class="container-fluid">
        <h4 class="card-title" style="font-size: 20px; text-align: left; color: gray; font-family: calibri;">
            DAR PRESENÇA
        </h4>

        <div class="col-12">
            <form action="{{ route('pesquisar.grupo') }}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-8">
                        Grupo
                        <select class="form-select" id="grupo" name="grupo">
                            <option value="">Selecione um grupo</option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id }}" {{ request('grupo') == $grupo->id ? 'selected' : '' }}>
                                    {{ $grupo->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <br>
                        <input class="btn btn-primary btn-sm me-md-2" style="font-size: 0.9rem;" type="submit" value="Pesquisar">
                        <a href="{{ route('dar.presenca') }}" class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem;">Limpar</a>
                    </div>
                </div>
            </form>
        </div>

        <hr>

        @if($pessoas->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                    <thead>
                        <tr style="background-color: #d6e3ff; font-size: 14px; color: #000000;">
                            <th>ID</th>
                            <th>NOME</th>
                            <th>PRESENÇA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pessoas as $pessoa)
                            <tr>
                                <td>{{ $pessoa->id }}</td>
                                <td>{{ $pessoa->nome }}</td>
                                <td>
                                    <form action="{{ route('dar.presenca.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="pessoa_id" value="{{ $pessoa->id }}">
                                        <button type="submit" class="btn btn-success btn-sm">Presente</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center">Selecione um grupo para ver as pessoas.</p>
        @endif

    </div>

@endsection
