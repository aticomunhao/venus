@extends('layouts.app')

@section('title') Histórico  @endsection

@section('content')


<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">            
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            HISTÓRICO DO ASSISTIDO
                        </div>
                    </div>
                </div>                
                <div class="card-body">                    
                    <fieldset class="border rounded border-secondary p-4">
                    <div class="form-group row">
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Assistido:</label>
                            <input type="text" id="" value="{{$analisa[0]->nm_1}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-2">
                            <label for="disabledTextInput" class="form-label">Sexo:</label>
                            <input type="text" id="" value="{{$analisa[0]->tipo}}" style="text-align:center;" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-3">
                            <label for="disabledTextInput" class="form-label">Dt nascimento:</label>
                            <input type="date" class="form-control" id=""  name="date"  value="{{$analisa[0]->dt_nascimento}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Lista de atendimentos</legend>
                    <?php $a=1; $b=1; $c=1; $d=1; $e=1; ?>
                    @foreach($analisa as $analisas)
                    <div class="accordion accordion-flush" id="accordionFlushExample"> 
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="{{$a++}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$b++}}" aria-expanded="false" aria-controls="flush-collapse{{$c++}}">
                            {{date('d/m/Y', strtotime($analisas->dh_chegada))}}
                            </button>
                            </h2>
                            <div id="flush-collapse{{$d++}}" class="accordion-collapse collapse" aria-labelledby="{{$e++}}" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                                <td class="col">NR</td>    
                                                <td class="col-3">REPRESENTANTE</td>
                                                <td class="col-1">PARENTESCO</td>
                                                <td class="col-3">ATENDENTE</td>
                                                <td class="col-1">DT/H INÍCIO</td>
                                                <td class="col-1">DT/H FIM</td>
                                                <td class="col-2">STATUS</td>
                                                
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:13px">       
                                                <td>{{$analisas->ida}}</td>
                                                <td>{{$analisas->nm_2}}</td>
                                                <td>{{$analisas->nome}}</td>
                                                <td>{{$analisas->nm_4}}</td>
                                                <td>{{$analisas->dh_inicio}}</td>
                                                <td>{{$analisas->dh_fim}}</td>
                                                <td>{{$analisas->tst}}</td>
                                                
                                            </tr>                                          
                                        </tbody>
                                    </table>
                                    <br/>
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                                <td class="col">OBSERVAÇÃO</td>                                                                                               
                                            </tr>
                                            <tr style="text-align:center;font-size:11px">
                                                <td> {{$analisas->observacao}}</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table> 
                                    <br/>                     
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">                                
                                                <td class="col span-1">TEMAS</td>                                                                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:11px">
                                                
                                                <td>                                             
                                                {{$analisas->t1}} {{$analisas->t2}} {{$analisas->t3}} {{$analisas->t4}} {{$analisas->t5}} {{$analisas->t6}} {{$analisas->t7}} {{$analisas->t8}} {{$analisas->t9}} {{$analisas->t10}} {{$analisas->t11}} {{$analisas->t12}} {{$analisas->t13}} {{$analisas->t14}} {{$analisas->t15}} {{$analisas->t16}} {{$analisas->t17}} {{$analisas->t18}} {{$analisas->t19}}                                 
                                                </td>
                                                                                                    
                                            </tr>
                                            
                                        </tbody>
                                    </table>                             
                                    <br/>                                
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                                <td class="col">ENCAMINHAMENTO TRATAMENTO</td>                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:11px">@foreach($analisas->tratamentos as $tratas)       
                                                <td>{{$tratas->tdt}}</td>
                                            @endforeach</tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">                                                
                                                <td class="col">ENCAMINHAMENTO ENTREVISTA</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:11px">@foreach($analisas->entrevistas as $entres)                                                
                                                <td>{{$entres->tde}}</td>
                                                @endforeach</tr>
                                        </tbody>
                                    </table>                
                                </div>
                            </div>
                        </div>                        
               @endforeach
                    <br>
                    <div class="row">
                        <div class="col">    
                            <a class="btn btn-danger" href="/atendendo" style="text-align:right;" role="button">Fechar</a>                            
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('footerScript')


<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection