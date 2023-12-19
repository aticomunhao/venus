@extends('layouts.app')

@section('head')

<title>Agendar Tratamento</title>

@endsection

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
                        <legend style="color:#525252; font-size:12px; font-family:sans-serif">Dados do Encaminhamento</legend>
                        <fieldset class="border rounded border-secondary p-2">
                        <div class="form-group row">
                            <div class="col">Tipo Prioridade:
                                <input type="text" class="form-control" value="{{$result[0]->prdesc}}" Disabled="Disabled">                            
                            </div>         
                            <div class="col">Nome do assistido:
                                <input type="text" class="form-control" value="{{$result[0]->nm_1}}" Disabled="Disabled">                            
                            </div>
                            <div class="col">Nome do representante:
                                <input type="text" class="form-control" value="{{$result[0]->nm_2}}" Disabled="Disabled"> 
                            </div>    
                            <div class="col">Parentesco:
                                <input type="text" class="form-control" value="{{$result[0]->nome}}" Disabled="Disabled">        
                            </div>   
                            <div class="col">Tratamento:
                                <input type="text" class="form-control" value="{{$result[0]->desctrat}}" Disabled="Disabled">        
                            </div>                                            
                        </div>
                        </fieldset>                   
                    <br/>
                    <form class="form-horizontal mt-2" method="post" action="/novo-atendimento">
                        @csrf              
                    <div class="row g-2" style="text-align:center;">
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">                   
                            <div class="form-check form-check-inline p-3">
                                <input class="form-check-input" type="radio" name="dia" id="" value="0" checked>
                                <label class="form-check-label" for="">Segunda</label>
                                <br/>
                                Nr Grupos: {{$contgrseg[0]->ttreu}}
                                <br/>
                                Max Vagas: {{$contgrseg[0]->maxat}}
                            </div>
                        </div>
                        <div class="col-1"></div>
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">  
                            <div class="form-check form-check-inline p-3">
                                <input class="form-check-input" type="radio" name="dia" id="" value="1">
                                <label class="form-check-label" for="">Terça</label>
                                <br/>
                                Nr Grupos: {{$contgrter[0]->ttreu}}
                                <br/>
                                Max Vagas: {{$contgrter[0]->maxat}}
                            </div>
                        </div>
                        <div class="col-1"></div>    
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">  
                            <div class="form-check form-check-inline p-3">
                                <input class="form-check-input" type="radio" name="dia" id="" value="2">
                                <label class="form-check-label" for="">Quarta</label>
                                <br/>
                                Nr Grupos: {{$contgrqua[0]->ttreu}}
                                <br/>
                                Max Vagas: {{$contgrqua[0]->maxat}}
                            </div>
                        </div>               
                    </div>
                    <div class="row p-3"></div>
                    <div class="row g-2"  style="text-align:center;">
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">                   
                            <div class="form-check form-check-inline p-3">
                                <input class="form-check-input" type="radio" name="dia" id="" value="4" checked>
                                <label class="form-check-label" for="">Quinta</label>
                                <br/>
                                Nr Grupos: {{$contgrqui[0]->ttreu}}
                                <br/>
                                Max Vagas: {{$contgrqui[0]->maxat}}
                            </div>
                        </div>
                        <div class="col-1"></div>
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">  
                            <div class="form-check form-check-inline p-3">
                                <input class="form-check-input" type="radio" name="dia" id="" value="5">
                                <label class="form-check-label" for="">Sexta</label>
                                <br/>
                                Nr Grupos: {{$contgrsex[0]->ttreu}}
                                <br/>
                                Max Vagas: {{$contgrsex[0]->maxat}}
                            </div>
                        </div> 
                        <div class="col-1"></div>
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;"> 
                            <div class="form-check form-check-inline p-3">
                                <input class="form-check-input" type="radio" name="dia" id="" value="6">
                                <label class="form-check-label" for="">Sábado</label>
                                <br/>
                                Nr Grupos: {{$contgrsab[0]->ttreu}}
                                <br/>
                                Max Vagas: {{$contgrsab[0]->maxat}}
                            </div>
                        </div>               
                    </div>
                    <div class="row p-3"></div>
                    <div class="row g-2"  style="text-align:center;">
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col-3" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">                   
                            <div class="form-check form-check-inline p-3">
                                <input class="form-check-input" type="radio" name="dia" id="" value="4" checked>
                                <label class="form-check-label" for="">Domingo</label>
                                <br/>
                                Nr Grupos: {{$contgrdom[0]->ttreu}}
                                <br/>
                                Max Vagas: {{$contgrdom[0]->maxat}}
                            </div>
                        </div>
                        <div class="col"></div>
                        <div class="col"></div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-recepcao" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>
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
