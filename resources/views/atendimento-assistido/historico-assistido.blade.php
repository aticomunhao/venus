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
                            <input type="text" id="" value="{{$assistido[0]->nm_1}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-2">
                            <label for="disabledTextInput" class="form-label">Sexo:</label>
                            <input type="text" id="" value="{{$assistido[0]->tipo}}" style="text-align:center;" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-3">
                            <label for="disabledTextInput" class="form-label">Dt nascimento:</label>
                            <input type="date" id=""  name="date"  value="{{$assistido[0]->dt_nascimento}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Lista de atendimentos</legend>
                    <?php $a=1; $b=1; $c=1; $d=1; $e=1; ?>
                    @foreach($assistido as $assistidos)
                    <div class="accordion accordion-flush" id="accordionFlushExample"> 
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="{{$a++}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$b++}}" aria-expanded="false" aria-controls="flush-collapse{{$c++}}">
                            {{date('d/m/Y', strtotime($assistidos->dh_chegada))}}
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
                                            <tr style="text-align:center;font-size:11px">       
                                                <td>{{$assistidos->ida}}</td>
                                                <td>{{$assistidos->nm_2}}</td>
                                                <td>{{$assistidos->nome}}</td>
                                                <td>{{$assistidos->nm_4}}</td>
                                                <td>{{$assistidos->dh_inicio}}</td>
                                                <td>{{$assistidos->dh_fim}}</td>
                                                <td>{{$assistidos->descricao}}</td>
                                                <td>{{$assistidos->observacao}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br> 
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                                <td class="col">TEMAS</td>
                                                <td class="col">OBSERVAÇÃO</td>
                                                <td class="col">ENCAMINHAMENTO TRATAMENTO</td>
                                                <td class="col">ENCAMINHAMENTO ENTREVISTA</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:11px">
                                                <td>{{$assistidos->tt}}</td>
                                                <td>{{$assistidos->te}}</td>
                                                <td></td>
                                            </tr>                                  
                                        </tbody>
                                    </table>                                  
                                </div>
                            </div>
                        </div>
                        @endforeach
                        {{--{{$tema->nm_tca}} {{$assistidos->t1}} {{$assistidos->t2}} {{$assistidos->t3}} {{$assistidos->t4}} {{$assistidos->t5}} {{$assistidos->t6}} {{$assistidos->t7}} {{$assistidos->t8}} {{$assistidos->t9}} {{$assistidos->t10}} {{$assistidos->t11}} {{$assistidos->t12}} {{$assistidos->t13}}{{$assistidos->t14}} {{$assistidos->t15}} {{$assistidos->t16}} {{$assistidos->t17}} {{$assistidos->t18}} {{$assistidos->t19}}--}}
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