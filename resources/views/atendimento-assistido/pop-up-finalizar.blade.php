<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<div class="modal fade" id="finalizar{{$assistidos->idat}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Finalizar o atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">Nome assistido
                        <input class="form-control" type="text" name="nome" value="{{$assistidos->nm_1}}" disabled>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col" style="color:red;">Mensagem de alerta:
                        <input class="form-control" style="background-color: #f7ccca;" type="text" value="Tem certeza que deseja finalizar este Atendimento?" disabled>
                    </div>
                </div>
                <form class="form-horizontal mt-4" method="POST" action="/finalizar/{{$assistidos->idat}}">
                @csrf                
                <div class="col-2">Nr atendimento
                    <input class="form-control" type="numeric" name="id_atend" value="{{$assistidos->idat}}" disabled>
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