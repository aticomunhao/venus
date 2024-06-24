@extends('layouts/app')

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
                            FINALIZAR O ATENDIMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col-2">Nr atendimento
                        <input class="form-control" type="numeric" name="id_atend" value="{{$assistido[0]->idat}}" disabled>
                    </div>
                    <div class="col">Nome do assistido:
                        <input class="form-control" type="text" name="nome" value="{{$assistido[0]->nm_1}}" disabled>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col" style="color:red;">
                        <input class="form-control" style=" height:80px; background-color: #f7ccca; text-align:center; font-size:medium;" type="text" value="Tem certeza que deseja finalizar o atendimento Nr {{$assistido[0]->idat}} ?" disabled>
                    </div>
                </div>
                <form class="form-horizontal mt-4" method="POST" action="/finalizar-afe/{{$assistido[0]->idat}}">
                @csrf

            </div>
                <br>
                    <hr>
                    <div class="row">
                        <div class="col" style="text-align: center;">
                                <a class="btn btn-danger" href="/atendendo-afe" style="text-align:right;" role="button">Cancelar</a>
                        </div>
                        <div class="col" style="text-align: center;">
                        </div>
                        <div class="col" style="text-align: left;">
                            <button type="submit" class="btn" style="background-color:#007bff; color:#fff;" data-bs-dismiss="modal">Confirmar</button>
                        </form>
                        </div>
                    </div><br>
                </div>
            <div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>

@endsection
