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
                            AGENDAR TRATAMENTO - DIA
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
                    <div class="row"><div class="col">Vermelho: 10% das vagas livres</div></div>
                    <form class="form-horizontal mt-2" method="get" action="/agendar-tratamento/{{$result[0]->ide}}">
                        @csrf              
                    <div class="row g-2 justify-content-evenly" style="text-align:center;  column-gap:10px;">
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">                   
                            <div class="form-check form-check-inline p-3 d-grid gap-2">
                                <input type="radio" class="btn-check" name="dia" id="option1" value="1" autocomplete="off" checked>
                                <label class="btn btn-outline-dark" for="option1">Segunda</label>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Nr Grupos</td>
                                            <td>Max vagas</td>
                                            <td>Vagas Disp</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$contgrseg[0]->ttreu}}</td>
                                            <td>{{$contgrseg[0]->maxat}}</td>
                                            @if (($contgrseg[0]->maxat / 100 * 90) < $conttratseg[0]->trat)
                                            <td style="background-color:#90EE90;">{{$conttratseg[0]->trat}}</td>
                                            @else
                                            <td style="background-color:#FA8072;">{{$conttratseg[0]->trat}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>                 
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">  
                            <div class="form-check form-check-inline p-3 d-grid gap-2">
                                <input type="radio" class="btn-check" name="dia" id="option2" value="2" autocomplete="off">
                                <label class="btn btn-outline-dark" for="option2">Terça</label>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Nr Grupos</td>
                                            <td>Max vagas</td>
                                            <td>Vagas Disp</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$contgrter[0]->ttreu}}</td>
                                            <td>{{$contgrter[0]->maxat}}</td>
                                            @if (($contgrter[0]->maxat / 100 * 90) < $conttratter[0]->trat)
                                            <td style="background-color:#90EE90;">{{$conttratter[0]->trat}}</td>
                                            @else
                                            <td style="background-color:#FA8072;">{{$conttratter[0]->trat}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>                 
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">  
                            <div class="form-check form-check-inline p-3 d-grid gap-2">
                                <input type="radio" class="btn-check" name="dia" id="option3" value="3" autocomplete="off">
                                <label class="btn btn-outline-dark" for="option3">Quarta</label>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Nr Grupos</td>
                                            <td>Max vagas</td>
                                            <td>Vagas Disp</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$contgrqua[0]->ttreu}}</td>
                                            <td>{{$contgrqua[0]->maxat}}</td>
                                            @if (($contgrqua[0]->maxat / 100 * 90) < $conttratqua[0]->trat)
                                            <td style="background-color:#90EE90;">{{$conttratqua[0]->trat}}</td>
                                            @else
                                            <td style="background-color:#FA8072;">{{$conttratqua[0]->trat}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>                
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">                   
                            <div class="form-check form-check-inline p-3 d-grid gap-2">
                                <input type="radio" class="btn-check" name="dia" id="option4" value="4" autocomplete="off">
                                <label class="btn btn-outline-dark" for="option4">Quinta</label>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Nr Grupos</td>
                                            <td>Max vagas</td>
                                            <td>Vagas Disp</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$contgrqui[0]->ttreu}}</td>
                                            <td>{{$contgrqui[0]->maxat}}</td>
                                            @if (($contgrqui[0]->maxat / 100 * 90) < $conttratqui[0]->trat)
                                            <td style="background-color:#90EE90;">{{$conttratqui[0]->trat}}</td>
                                            @else
                                            <td style="background-color:#FA8072;">{{$conttratqui[0]->trat}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>        
                    </div>
                    <br>                   
                    <div class="row g-2 " style="text-align:center;  column-gap:215px;">
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">  
                            <div class="form-check form-check-inline p-3 d-grid gap-2">
                                <input type="radio" class="btn-check" name="dia" id="option5" value="5" autocomplete="off">
                                <label class="btn btn-outline-dark" for="option5">Sexta</label>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Nr Grupos</td>
                                            <td>Max vagas</td>
                                            <td>Vagas Disp</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$contgrsex[0]->ttreu}}</td>
                                            <td>{{$contgrsex[0]->maxat}}</td>
                                            @if (($contgrsex[0]->maxat / 100 * 90) < $conttratsex[0]->trat)
                                            <td style="background-color:#90EE90;">{{$conttratsex[0]->trat}}</td>
                                            @else
                                            <td style="background-color:#FA8072;">{{$conttratsex[0]->trat}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;"> 
                            <div class="form-check form-check-inline p-3 d-grid gap-2">
                                <input type="radio" class="btn-check" name="dia" id="option6" value="6" autocomplete="off">
                                <label class="btn btn-outline-dark" for="option6">Sábado</label>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Nr Grupos</td>
                                            <td>Max vagas</td>
                                            <td>Vagas Disp</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$contgrsab[0]->ttreu}}</td>
                                            <td>{{$contgrsab[0]->maxat}}</td>
                                            @if (($contgrsab[0]->maxat / 100 * 90) < $conttratsab[0]->trat)
                                            <td style="background-color:#90EE90;">{{$conttratsab[0]->trat}}</td>
                                            @else
                                            <td style="background-color:#FA8072;">{{$conttratsab[0]->trat}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>               
                        <div class="col" style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">                   
                            <div class="form-check form-check-inline p-3 d-grid gap-2">
                            <input type="radio" class="btn-check" name="dia" id="option7" value="0" autocomplete="off">
                                <label class="btn btn-outline-dark" for="option7">Domingo</label>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Nr Grupos</td>
                                            <td>Max vagas</td>
                                            <td>Vagas Disp</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{$contgrdom[0]->ttreu}}</td>
                                            <td>{{$contgrdom[0]->maxat}}</td>
                                            @if (($contgrdom[0]->maxat / 100 * 90) < $conttratdom[0]->trat)
                                            <td style="background-color:#90EE90;">{{$conttratdom[0]->trat}}</td>
                                            @else
                                            <td style="background-color:#FA8072;">{{$conttratdom[0]->trat}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>               
                    </div>
                    <br/>
                    <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-encaminhamentos" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>                       
                    </form> 
                    </div> <br/>
                </div>  
            </div>
                                              
        </div>
        
    </div>
   
</div>


@endsection

@section('footerScript')


@endsection
