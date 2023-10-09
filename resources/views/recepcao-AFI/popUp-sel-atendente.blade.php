
<div class="modal fade" id="atendimento{{$listas->ida}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Selecionar Atendente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal mt-4" method="POST" action="atendente-atualizar/{{$listas->ida}}">
                @method('PUT')
                @csrf                
                <div class="row">
                    <div class="col-3" style="text-align:left;">Nr
                        <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="numeric" name="numero" id="" value="{{$listas->ida}}">    
                    </div>
                    <div class="col" style="text-align:left;">Assistido
                        <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="text" name="atendido" id="" value="{{$listas->nm_1}}" >                            
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col" style="text-align:left;">Atendente fraterno
                        <select class="form-select" aria-label=".form-select-lg" id="1" name="atendente" type="text">                            
                        @foreach($atende as $atendes)
                        <option value="{{$atendes->idatt}}">{{$atendes->nm_1}}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col" style="text-align: left;">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                    <div class="col" style="text-align: left;">
                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Confirmar</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>