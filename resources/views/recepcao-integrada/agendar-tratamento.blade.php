@extends('layouts.app')

@section('content')
<br>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">               
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            AGENDAR TRATAMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="POST" action="/" >
                    @csrf
                    <legend style="color:#525252; font-size:15px; font-family:sans-serif">Dados do assistido</legend>
                    <fieldset class="border rounded border-secondary p-4">
                    <div class="row">
                        <div class="col-2">Encaminhamento
                            <input class="form-control" style="text-align:left; font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$result[0]->ide}}" name="id_enc" id="" type="text" disabled>
                        </div>
                    
                        <div class="col">Assistido                    
                            <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="text" name="assist" id="" value="{{$result[0]->nm_1}}" disabled>
                        </div>
                                    
                        <div class="col">Representante                   
                            <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$result[0]->nm_2}}" name="repre" id="" type="text" disabled>
                        </div>     
                        <div class="col">Tratamento                   
                            <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$result[0]->desctrat}}" name="repre" id="" type="text" disabled>
                        </div>                       
                    </div>
                    </fieldset> 
                    <br>
                </div>
                <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-atendimentos"  role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-primary" >Confirmar</button>
                        </div>
                        </form>
                        
                    </div>
                    <br>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footerScript')



@endsection