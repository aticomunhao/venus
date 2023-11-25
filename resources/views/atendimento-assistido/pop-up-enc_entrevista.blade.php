<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<div class="modal fade" id="entrevista{{$assistidos->idat}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Encaminhamento para Entrevista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-2">Nr Atendimento
                        <input class="form-control" type="numeric" name="id" value="{{$assistidos->idat}}" disabled>
                    </div>
                    <div class="col">Nome assistido
                        <input class="form-control" type="text" name="nome" value="{{$assistidos->nm_1}}" disabled>
                    </div>
                </div>
                <form class="form-horizontal mt-4" method="POST" action="/entrevistas/{{$assistidos->idat}}">
                @csrf
                <div class="row" style="text-align: left;">                    
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="ame" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="ame" class="form-check-label">Assessoria de Estudos e Aplicação da Medicina Espiritual - AME</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="afe" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="afe" class="form-check-label">Atendentende Fraterno Específico - AFE</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="diamo" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="diamo" class="form-check-label">Divisão de Apoio ao Médium Ostensivo em Eclosão da Mediunidade - DIAMO</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="getrat" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="getrat" class="form-check-label">Grupo de Estudo, Trabalho e Terapia  - GETRAT</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="nutres" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="nutres" class="form-check-label">Núcleo de tratamento Espiritual - NUTRES</label>
                    </div>
                </div>
            </div>                    
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
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>