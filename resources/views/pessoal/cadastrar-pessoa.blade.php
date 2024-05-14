
        <div class="modal-body">
            <form>
            @csrf
            <div class="container"> 
                    <div class="row">
                        <div class="col">
                            <label for="txtAssNome">Nome:</label>
                            <input type="text" id="txtAssNome" name="nomepes" class="form-control req" value=""></input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-m">
                            <label for="txtAssCpf">CPF:</label>
                            <input type="text" id="txtAssCpf" name="txtAssCpf" class="form-control mask cpf req" value=""></input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="txtAssEmail">E-mail:</label>
                            <input type="text" id="txtAssEmail" name="txtAssEmail" class="form-control req mail" value=""></input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="txtAssFone">Telefone:</label>
                            <input type="text" id="txtAssFone" name="txtAssFone" class="form-control mask tel minlen 10" data-invalidmessage="Telefone Inválido !" value=""></input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="txtAssCid">Cidade:</label>
                            <input type="text" id="txtAssCid" name="txtAssCid" class="form-control "  value=""></input>
                        </div>
                    </div>
                   
                    </form>
                        <hr>
                        <div class="row mt-1 justify-content-center">
                            <div class="d-grid gap-1 col-4 mx-auto">
                        <button type="button" class="btn btn-secondary" id="btnCancelar" title="Cancelar" data-dismiss="modal"><i class="fa fa-ban"></i></button>
                        <button type="submit" class="btn btn-primary" id="btnSalvaNovoAssistido" title="Salvar" disabled="disabled"><i class="fa fa-save"></i></button>
                    </div>
            </div>
        </div>
                
