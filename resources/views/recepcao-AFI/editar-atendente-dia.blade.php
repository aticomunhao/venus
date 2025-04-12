@extends('layouts.app')

@section('title')
    Editar Atendente Dia
@endsection

@section('content')
    <br />
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                   <div class="card-header">
                        <div class="row">
                            <div class="col">
                                EDITAR ATENDENTE DO DIA
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="POST"
                        action="/altera-atendente-dia/{{ $atende->idatd }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-6">
                                <label for="disabledTextInput" class="form-label">Número:</label>
                                <input type="number" id="" value="{{ $atende->idatd }}" class="form-control"
                                    placeholder="Disabled input" disabled>
                            </div>
                            <div class="col-6">
                                <label for="disabledTextInput" class="form-label">Grupo:</label>
                                    <select class="form-select text-center" id="grupo" name="grupo">
                                    @foreach ($grupos as $grupo)
                                        <option value="{{$grupo->id_cronograma}}" {{ $atende->nomeg == $grupo->nome ? 'selected' : ''}}>{{$grupo->nome}}</option>
                                    @endforeach

                                </select>
                            </div>


                        </div>

                        <div>

                                <div class="row">
                                    <div class="col-6">
                                        <label for="disabledTextInput" class="form-label">Nome AFI:</label>
                                        <input type="text" id="" value="{{ $atende->nm_4 }}"
                                            class="form-control" placeholder="Disabled input" disabled>
                                    </div>

                                    <div class="col-6 mt-2">Número da Sala:
                                        <?php $i = 0; ?>
                                        <select class="form-select text-center" id="" name="sala"
                                            type="number">

                                            @foreach ($sala as $salas)
                                                @if ($salas->numero > $atende->nm_sala and $atende->nm_sala > $i)
                                                    <option value="{{ $atende->id_sala }}" selected>
                                                        {{ $atende->nm_sala }}</option>
                                                @endif
                                                <option value="{{ $salas->id }}">{{ $salas->numero }} </option>
                                                {{ $i = $salas->numero }};
                                            @endforeach
                                        </select>
                                    </div>
                                
                                    <div class="col"></div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <a class="btn btn-danger" href="/gerenciar-atendente-dia"
                                            role="button">Cancelar</a>
                                    </div>
                                    <div class="d-grid gap-2 col-4 mx-auto">
                                        <button type="submit" class="btn btn-primary"
                                            style="color:#fff;">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
@endsection
