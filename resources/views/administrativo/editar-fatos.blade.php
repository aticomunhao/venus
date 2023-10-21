@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-3">
            <div class="card">
            </div>
            <div class="card-body">
                <br></br>
                <h5 class="card-title">EDITAR</h5>


                <div class="card-text">
                <form class="form-horizontal mt-4" method="post" action="/atualizar-fatos/@foreach($lista as $item){{$item->id}}@endforeach">
                    @csrf
                    <div class="mb-3">
                        <label for="descricao" class="form-label"></label>
                        <input type="text" name="descricao" class="form-control" id="descricao" placeholder="" 
                        value="@foreach($lista as $item){{$item->descricao}}@endforeach">
                    </div>
                </div>
                    <button class="btn btn-primary" >Confirmar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
