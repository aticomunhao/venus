@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <br>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Editar Sala</h5>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="post" action="/atualizar-fatos/@foreach($lista as $item){{$item->id}}@endforeach">
                            @csrf
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição:</label>
                                <input type="text" name="descricao" class="form-control" id="descricao" placeholder=""
                                value="@foreach($lista as $item){{$item->descricao}}@endforeach">
                            </div>
                            <div class="row justify-content-center">
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <a class="btn btn-danger btn-block" href="/gerenciar-fatos" role="button">Cancelar</a>
                                </div>
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <button class="btn btn-primary btn-block">Confirmar</button>
                                </div>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
