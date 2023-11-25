<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<div class="modal fade" id="tratamento{{$assistidos->idat}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Encaminhamento para tratamento</h5>
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
                <form class="form-horizontal mt-4" method="POST" action="/tratamentos/{{$assistidos->idat}}">
                @csrf                
                <div class="row" style="text-align: left;">
                    <div class="form-check form-check-inline">                        
                        <input type="checkbox" name="pph" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="pph" class="form-check-label">Palestra/Passe de Harmonização - PPH</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="ptd" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="ptd" class="form-check-label">Passe Tratamento Desobessessivo - PTD</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="ptig" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="ptig" class="form-check-label">Passe Tratamento Integral - PTIg</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="pti" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                        <label for="pti" class="form-check-label">Passe Tratamento Intensivo - PTI</label>
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