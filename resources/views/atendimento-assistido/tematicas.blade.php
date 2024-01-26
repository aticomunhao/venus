@extends('layouts/app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

<br>               
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="row  mb-4">
                    <div class="col-2">Nr atendimento
                        <input class="form-control" type="numeric" name="id" value="{{$assistido[0]->idat}}" disabled>
                    </div>
                    <div class="col">Nome assistido
                        <input class="form-control" type="text" name="nome" value="{{$assistido[0]->nm_1}}" disabled>
                    </div>                   
            </div>
            <form class="form-horizontal mt-4" method="POST" action="/tematicas/{{$assistido[0]->idat}}">
                    @csrf
            <div class="row mb-4">
                <div class="col" style="text-align:left;">Anotações:
                    <textarea class="form-control" maxlength="300" rows="3"  type="text" name="nota" value=""></textarea>
                </div>
            </div>
                <fieldset class="border rounded border-secoundary ">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12" style="text-align:left;">
                                    <span  style="color:#525252; font-size:14px;">Temática do Atendimento</span>
                                </div>
                            </div>
                        </div>
                    <div class="card-body">
                        <div class="row">
                            @if ($verifi == 0)
                            <div class="col"  style="text-align:center">Espirituais
                                <div class="form-check m-2">
                                    <input id="21" type="checkbox" name="maf" data-size="small" data-size="small" data-toggle="toggle"  data-onstyle="success"  data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="maf" class="form-check-label" data-tt="tooltip" data-placement="top" title="Mediunidade aflorada" >1.1</label>
                                </div>  
                                <div class="form-check m-2">
                                    <input id="1" type="checkbox" name="ies" data-size="small" data-size="small" data-toggle="toggle"  data-onstyle="success"  data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" >
                                    <label for="ies" class="form-check-label" data-tt="tooltip" data-placement="top" title="Influenciação espiritual" >1.2</label>
                                </div>
                                <div class="form-check m-2">
                                    <input id="2" type="checkbox" name="obs" data-size="small"  data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="obs" class="form-check-label" data-tt="tooltip" data-placement="top" title="Obsessão" >1.3</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Comportamentais 
                                <div class="form-check  m-2">
                                    <input id="3" type="checkbox" name="abo" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="abo" class="form-check-label" data-tt="tooltip" data-placement="top" title="Aborto">5.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="4" type="checkbox" name="sui" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="sui" class="form-check-label" data-tt="tooltip" data-placement="top" title="Suicídio">5.2</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Relacionamento                   
                                <div class="form-check  m-2">
                                    <input id="5" type="checkbox" name="coj" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="coj" class="form-check-label" data-tt="tooltip" data-placement="top" title="Conjugal">2.1</label>
                                </div>
                                <div class="form-check   m-2">
                                    <input id="6" type="checkbox"  name="fam" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="fam" class="form-check-label" data-tt="tooltip" data-placement="top" title="Familiar">2.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="7" type="checkbox"  name="soc" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="soc" class="form-check-label" data-tt="tooltip" data-placement="top" title="Social">2.3</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="8" type="checkbox"  name="prf" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="prf" class="form-check-label" data-tt="tooltip" data-placement="top" title="Profissional">2.4</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Cotidiano                    
                                <div class="form-check  m-2">
                                    <input id="9" type="checkbox"  name="dou" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dou" class="form-check-label" data-tt="tooltip" data-placement="top" title="Interesse pela Doutrina">6.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input  id="10" type="checkbox"  name="son" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="son" class="form-check-label" data-tt="tooltip" data-placement="top" title="Sonhos">6.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="11" type="checkbox"  name="esp" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="esp" class="form-check-label" data-tt="tooltip" data-placement="top" title="Medo de espíritos">6.3</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="12" type="checkbox"  name="dpr" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dpr" class="form-check-label" data-tt="tooltip" data-placement="top" title="Dificuldades profissionais">6.4</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="13" type="checkbox"  name="deq" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" >
                                    <label for="dpr" class="form-check-label" data-tt="tooltip" data-placement="top" title="Desencarne de ente querido">6.5</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Físicas/mentais                    
                                <div class="form-check  m-2">
                                    <input id="14" type="checkbox"  name="sau" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="sau" class="form-check-label" data-tt="tooltip" data-placement="top" title="Saúde">3.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="15" type="checkbox"  name="pdg" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="pdg" class="form-check-label" data-tt="tooltip" data-placement="top" title="Psiquiátrica diagnosticada">3.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="16" type="checkbox"  name="sex" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="sex" class="form-check-label" data-tt="tooltip" data-placement="top" title="Sexualidade">3.3</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="17" type="checkbox"  name="dts" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dts" class="form-check-label" data-tt="tooltip" data-placement="top" title="Desânimo/Tristeza/Solidão">4.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="18" type="checkbox"  name="adp" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="adp" class="form-check-label" data-tt="tooltip" data-placement="top" title="Ansiedade/Depressão">4.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="19" type="checkbox"  name="dqu" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dqu" class="form-check-label" data-tt="tooltip" data-placement="top" title="Dependência química">4.3</label>
                                </div>                    
                                <div class="form-check  m-2">
                                    <input id="20" type="checkbox"  name="est" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="est" class="form-check-label" data-tt="tooltip" data-placement="top" title="Estresse">4.4</label>
                                </div>
                            @else

                                <div class="col" style="text-align:center;">Espirituais
                                <div class="form-check m-2">
                                    <input id="21" type="checkbox" name="maf" data-size="small" data-size="small" data-toggle="toggle"  data-onstyle="success"  data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->maf ? 'checked' : ''}}>
                                    <label for="maf" class="form-check-label" data-tt="tooltip" data-placement="top" title="Mediunidade aflorada" >1.1</label>
                                </div>    
                                <div class="form-check m-2">
                                    <input id="1" type="checkbox" name="ies" data-size="small" data-size="small" data-toggle="toggle"  data-onstyle="success"  data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->ies ? 'checked' : ''}}>
                                    <label for="ies" class="form-check-label" data-tt="tooltip" data-placement="top" title="Influenciação espiritual" >1.2</label>
                                </div>
                                <div class="form-check m-2">
                                    <input id="2" type="checkbox" name="obs" data-size="small"  data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->obs ? 'checked' : ''}}>
                                    <label for="obs" class="form-check-label" data-tt="tooltip" data-placement="top" title="Obsessão" >1.3</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Comportamentais 
                                <div class="form-check  m-2">
                                    <input id="3" type="checkbox" name="abo" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->abo ? 'checked' : ''}}>
                                    <label for="abo" class="form-check-label" data-tt="tooltip" data-placement="top" title="Aborto">5.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="4" type="checkbox" name="sui" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->sui ? 'checked' : ''}}>
                                    <label for="sui" class="form-check-label" data-tt="tooltip" data-placement="top" title="Suicídio">5.2</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Relacionamento                   
                                <div class="form-check  m-2">
                                    <input id="5" type="checkbox" name="coj" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->coj ? 'checked' : ''}}>
                                    <label for="coj" class="form-check-label" data-tt="tooltip" data-placement="top" title="Conjugal">2.1</label>
                                </div>
                                <div class="form-check   m-2">
                                    <input id="6" type="checkbox"  name="fam" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->fam ? 'checked' : ''}}>
                                    <label for="fam" class="form-check-label" data-tt="tooltip" data-placement="top" title="Familiar">2.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="7" type="checkbox"  name="soc" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->soc ? 'checked' : ''}}>
                                    <label for="soc" class="form-check-label" data-tt="tooltip" data-placement="top" title="Social">2.3</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="8" type="checkbox"  name="prf" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->prf ? 'checked' : ''}}>
                                    <label for="prf" class="form-check-label" data-tt="tooltip" data-placement="top" title="Profissional">2.4</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Cotidiano                    
                                <div class="form-check  m-2">
                                    <input id="9" type="checkbox"  name="dou" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->dou ? 'checked' : ''}}>
                                    <label for="dou" class="form-check-label" data-tt="tooltip" data-placement="top" title="Interesse pela Doutrina">6.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input  id="10" type="checkbox"  name="son" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->son ? 'checked' : ''}}>
                                    <label for="son" class="form-check-label" data-tt="tooltip" data-placement="top" title="Sonhos">6.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="11" type="checkbox"  name="esp" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->esp ? 'checked' : ''}}>
                                    <label for="esp" class="form-check-label" data-tt="tooltip" data-placement="top" title="Medo de espíritos">6.3</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="12" type="checkbox"  name="dpr" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->dpr ? 'checked' : ''}}>
                                    <label for="dpr" class="form-check-label" data-tt="tooltip" data-placement="top" title="Dificuldades profissionais">6.4</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="13" type="checkbox"  name="deq" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->deq ? 'checked' : ''}}>
                                    <label for="dpr" class="form-check-label" data-tt="tooltip" data-placement="top" title="Desencarne de ente querido">6.5</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Físicas/mentais                    
                                <div class="form-check  m-2">
                                    <input id="14" type="checkbox"  name="sau" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->sau ? 'checked' : ''}}>
                                    <label for="sau" class="form-check-label" data-tt="tooltip" data-placement="top" title="Saúde">3.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="15" type="checkbox"  name="pdg" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->pdg ? 'checked' : ''}}>
                                    <label for="pdg" class="form-check-label" data-tt="tooltip" data-placement="top" title="Psiquiátrica diagnosticada">3.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="16" type="checkbox"  name="sex" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->sex ? 'checked' : ''}}>
                                    <label for="sex" class="form-check-label" data-tt="tooltip" data-placement="top" title="Sexualidade">3.3</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="17" type="checkbox"  name="dts" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->dts ? 'checked' : ''}}>
                                    <label for="dts" class="form-check-label" data-tt="tooltip" data-placement="top" title="Desânimo/Tristeza/Solidão">4.1</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="18" type="checkbox"  name="adp" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->adp ? 'checked' : ''}}>
                                    <label for="adp" class="form-check-label" data-tt="tooltip" data-placement="top" title="Ansiedade/Depressão">4.2</label>
                                </div>
                                <div class="form-check  m-2">
                                    <input id="19" type="checkbox"  name="dqu" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->dqu ? 'checked' : ''}}>
                                    <label for="dqu" class="form-check-label" data-tt="tooltip" data-placement="top" title="Dependência química">4.3</label>
                                </div>                    
                                <div class="form-check  m-2">
                                    <input id="20" type="checkbox"  name="est" data-size="small" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" {{$result[0]->est ? 'checked' : ''}}>
                                    <label for="est" class="form-check-label" data-tt="tooltip" data-placement="top" title="Estresse">4.4</label>
                                </div>
                            @endif                                               
                            </div>
                        </div>
                    <br>
                    <hr>
                        <div class="row">
                            <div class="col" style="text-align: right;">
                                    <a class="btn btn-danger" href="/atendendo" style="text-align:right; margin-right: 50px" role="button">Cancelar</a>
                                    <button type="submit" class="btn btn-primary" style="background-color:#007bff; color:#fff;" data-bs-dismiss="modal">Confirmar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>


<script>

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

</script>

@endsection