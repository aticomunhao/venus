<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<div class="modal fade" id="pessoa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Opcionais da sala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            
            <div class="modal-body">
                    <form class="form-horizontal" method="POST" action="/gerenciar-salas">
                        @csrf
                </div>
                <div class="form-group">
                    <div class="row justify-content-center m">
                        <div class="col-1">Ar-cond
                            <input type="checkbox" name="ar_condicionado" value="{{$sala->ar_condicionado}}" data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                              
                        </div>

                        <div class="col-1">Armários
                            <input type="checkbox" name="armarios"  value="{{$sala->armarios}}"data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                         </div>

                         <div class="col-1">Bebedouro
                            <input type="checkbox" name="bebedouro"  value="{{$sala->bebedouro}}"data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                         </div>

                        <div class="col-1">Controle_proj
                            <input type="checkbox" name= "controle"  value="{{$sala->controle}}" data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                        </div>
                        <div class="col-1">PC
                            <input type="checkbox" name= "computador"  value="{{$sala->computador}}" data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                        </div>
                        <div class="col-1">Projetor
                            <input type="checkbox"name="projetor"  value="{{$sala->projetor}}" data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                        </div>
                        <div class="col-1">Tela_proj
                            <input type="checkbox" name="tela_projetor" value="{{$sala->tela_projetor}}"data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                        </div>
                        <div class="col-1">Quadro
                            <input type="checkbox" name= "quadro" value="{{$sala->quadro}}" data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                        </div>
                        <div class="col-1">Som
                            <input type="checkbox" name="som"  value="{{$sala->som}}"data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                            </div>
                                <div class="col-1">Luz azul
                                    <input type="checkbox" name="luz_azul" value="{{$sala->luz_azul}}"data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                                    </div>
                                       
                                                                
                        <div class="col-1">Ventilador
                            <input type="checkbox" name= "ventilador"  value="{{$sala->ventilador}}" data-toggle="toggle" data-onlabel="Sim"
                                data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
                        </div>
                        
                        <br>
                    </div>
                </div>
                <br>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col" style="text-align: left;">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col" style="text-align: left;">
                            <button type="submit" class="btn" style="background-color:#007bff; color:#fff;"
                                data-bs-dismiss="modal">Confirmar</button>
                        </div>
                        </form>
                    </div>
                </div>
                

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
