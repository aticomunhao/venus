<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<div class="modal fade" id="inativar{{$listas->ide}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Inativar Encaminhamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-2">Nr:
                        <input class="form-control" type="text" name="nome" value="{{$listas->ide}}" disabled>
                    </div>
                    <div class="col">Nome assistido
                        <input class="form-control" type="text" name="nome" value="{{$listas->nm_1}}" disabled>
                    </div>
                    <div class="col">Tratamento:
                        <input class="form-control" type="text" name="nome" value="{{$listas->sigla}}" disabled>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col" style="color:red;">Mensagem de alerta:
                        <input class="form-control" style="background-color: #f7ccca;" type="text" value="Você vai inativar o encaminhamento Nr {{$listas->ide}}, datado de {{date ('d/m/Y', strtotime($listas->dh_enc))}}, tem certeza? Caso positivo, selecione um motivo!" disabled>
                    </div>
                </div>
                <form class="form-horizontal mt-4" method="POST" action="/inativar/{{$listas->ide}}">
                @csrf 
                <div class="row">
                    <center>               
                    <div class="col-5" style="text-align: left;">Motivo:
                        <select class="form-select" id="" name="motivo" type="number" required>
                            @foreach ($motivo as $motivos)
                            <option value="{{$motivos->id}}">{{$motivos->tipo}}</option>
                            @endforeach               
                        </select>                                    
                    </div>  
                    </center>             
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