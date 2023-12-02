<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">


<div class="modal fade" id="anotacoes{{$assistidos->idat}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Temas do atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row  mb-4">
                    <div class="col-2">Nr atendimento
                        <input class="form-control" type="numeric" name="id" value="{{$assistidos->idat}}" disabled>
                    </div>
                    <div class="col">Nome assistido
                        <input class="form-control" type="text" name="nome" value="{{$assistidos->nm_1}}" disabled>
                    </div>
                
                <form class="form-horizontal mt-4" method="POST" action="/tematicas/{{$assistidos->idat}}">
                @csrf
                <div class="row mb-4">
                    <div class="col" style="text-align:left;">Anotações
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
                               <div class="col" style="text-align:center;">Espirituais    
                                <div class="form-check pl-0">
                                    <input id="1" type="checkbox" name="ies" data-toggle="toggle" data-width="70" data-onstyle="success"  data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não" >
                                    <label for="ies" class="form-check-label" data-tt="tooltip" data-placement="top" title="Influenciação espiritual" >1.2</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="2" type="checkbox" name="obs" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="obs" class="form-check-label" data-tt="tooltip" data-placement="top" title="Obsessão" >1.3</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Comportamentais 
                                <div class="form-check  pl-0">
                                    <input id="3" type="checkbox" name="abo" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="abo" class="form-check-label" data-tt="tooltip" data-placement="top" title="Aborto">5.1</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="4" type="checkbox" name="sui" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="sui" class="form-check-label" data-tt="tooltip" data-placement="top" title="Suicídio">5.2</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Relacionamento                   
                                <div class="form-check  pl-0">
                                    <input id="5" type="checkbox" name="coj" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="coj" class="form-check-label" data-tt="tooltip" data-placement="top" title="Conjugal">2.1</label>
                                </div>
                                <div class="form-check   pl-0">
                                    <input id="6" type="checkbox"  name="fam" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="fam" class="form-check-label" data-tt="tooltip" data-placement="top" title="Familiar">2.2</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="7" type="checkbox"  name="soc" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="soc" class="form-check-label" data-tt="tooltip" data-placement="top" title="Social">2.3</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="8" type="checkbox"  name="prf" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="prf" class="form-check-label" data-tt="tooltip" data-placement="top" title="Profissional">2.4</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Cotidiano                    
                                <div class="form-check  pl-0">
                                    <input id="9" type="checkbox"  name="dou" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dou" class="form-check-label" data-tt="tooltip" data-placement="top" title="Interesse pela Doutrina">6.1</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input  id="10" type="checkbox"  name="son" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="son" class="form-check-label" data-tt="tooltip" data-placement="top" title="Sonhos">6.2</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="11" type="checkbox"  name="esp" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="esp" class="form-check-label" data-tt="tooltip" data-placement="top" title="Medo de espíritos">6.3</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="12" type="checkbox"  name="dpr" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dpr" class="form-check-label" data-tt="tooltip" data-placement="top" title="Dificuldades profissionais">6.4</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="13" type="checkbox"  name="deq" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dpr" class="form-check-label" data-tt="tooltip" data-placement="top" title="Desencarne de ente querido">6.5</label>
                                </div>
                            </div>
                            <div class="col" style="text-align:center;">Físicas/mentais                    
                                <div class="form-check  pl-0">
                                    <input id="14" type="checkbox"  name="sau" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="sau" class="form-check-label" data-tt="tooltip" data-placement="top" title="Saúde">3.1</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="15" type="checkbox"  name="pdg" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="pdg" class="form-check-label" data-tt="tooltip" data-placement="top" title="Psíquica diagnosticada">3.2</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="16" type="checkbox"  name="sex" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="sex" class="form-check-label" data-tt="tooltip" data-placement="top" title="Sexualidade">3.3</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="17" type="checkbox"  name="dts" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dts" class="form-check-label" data-tt="tooltip" data-placement="top" title="Desânimo/Tristeza/Solidão">4.1</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="18" type="checkbox"  name="adp" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="adp" class="form-check-label" data-tt="tooltip" data-placement="top" title="Ansiedade/Depressão">4.2</label>
                                </div>
                                <div class="form-check  pl-0">
                                    <input id="19" type="checkbox"  name="dqu" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="dqu" class="form-check-label" data-tt="tooltip" data-placement="top" title="Dependência química">4.3</label>
                                </div>                    
                                <div class="form-check  pl-0">
                                    <input id="20" type="checkbox"  name="est" data-toggle="toggle" data-width="70" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                    <label for="est" class="form-check-label" data-tt="tooltip" data-placement="top" title="Estresse">4.4</label>
                                </div>            
                            </div>
                        </div>        
                    </div>
                </div>
        </fieldset>
        <br>
        <div class="modal-footer">
            <div class="row">
                <div class="col" style="text-align: left;">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </div>
                <div class="col" style="text-align: left;">
                    <button type="submit" class="btn" style="background-color:#007bff; color:#fff;" data-bs-dismiss="modal">Confirmar</button>
                </div>
                </form>
            </div>
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