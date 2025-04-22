@vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])

<div class="row">
    <div class="col-2">
        CPF
        <input class="form-control" id="Cpf" name="Cpf" type="text" maxlength="14"
            placeholder="123.456.789-00...">
    </div>

    <div class="col-2">
        RG
        <input class="form-control" id="RG" name="RG" type="text" maxlength="9"
            placeholder="">
    </div>
    <div class="col-3">
        Telefone
        <input class="form-control" id="Telefone" name="Telefone" type="text" maxlength="14"
            placeholder="">
    </div>

    <div class="col-5">

        E-mail
        <input class="form-control" id="Email" name="Email" type="email" maxlength="50"
        placeholder="email@host.com...">

    </div>
</div>


<script>
    $(document).ready(function() {

        let novoConteudo
        $('#Cpf').on('input', function() {

            // ---- Validação de letras ---- //
            novoConteudo = $(this).val().replace(/\D/g, '')
            $(this).val(novoConteudo)

            let numeros = ($(this).val().match(/\d/g) || [])
            .length; // Conta a quantidade de números no input

            // ---- Máscara de CPF ---- //

            if (numeros > 3) {
                novoConteudo = $(this).val().slice(0, 3) + '.' + novoConteudo.slice(
                    3, ) // Separa os números e adiciona um ponto
                $(this).val(novoConteudo)
            }
            if (numeros > 6) {
                novoConteudo = $(this).val().slice(0, 7) + '.' + novoConteudo.slice(
                    7, ) // Separa os números e adiciona um ponto
                $(this).val(novoConteudo)
            }
            if (numeros > 9) {
                novoConteudo = $(this).val().slice(0, 11) + '-' + novoConteudo.slice(
                    11, ) // Separa os números e adiciona um hífen
                $(this).val(novoConteudo)
            }


        })



    });
</script>

