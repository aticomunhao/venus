@extends('layouts/app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<br>               
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">               
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            ENCAMINHAR PARA TRATAMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">                
                    <div class="row">
                        <div class="col-2">Nr Atendimento
                            <input class="form-control" type="numeric" name="id" value="{{$assistido[0]->idat}}" disabled>
                        </div>
                        <div class="col">Nome assistido
                            <input class="form-control" type="text" name="nome" value="{{$assistido[0]->nm_1}}" disabled>
                        </div>
                    </div>
                    <form class="form-horizontal mt-4" method="POST" action="/tratamentos/{{$assistido[0]->idat}}">
                    @csrf                
                    <div class="row form-group">
                        <div class="form-check form-check-inline">                        
                            <input type="checkbox" id="pph" name="pph" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                            <label for="pph" class="form-check-label">Palestra/Passe de Harmonização - PPH</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="ptd" name="ptd" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                            <label for="ptd" class="form-check-label">Passe Tratamento Desobessessivo - PTD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="ptig" name="ptig" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                            <label for="ptig" class="form-check-label">Passe Tratamento Integral - PTIg</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="pti" name="pti" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                            <label for="pti" class="form-check-label">Passe Tratamento Intensivo - PTI</label>
                        </div>                    
                    </div>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col" style="text-align: left;">
                                <a class="btn btn-danger" href="/atendendo" style="text-align:right;" role="button">Cancelar</a>
                            </div>
                        <div class="col" style="text-align: left;">
                            <button type="submit" class="btn" style="background-color:#007bff; color:#fff;" data-bs-dismiss="modal">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>

@endsection