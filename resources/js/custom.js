//Quando executar a pagina usa o select2

$(function () {
    $('.select2').select2({
        theme: 'bootstrap-5',
    });
});

// Inicializa os tooltips Bootstrap 5
document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

document.getElementById('cnpj').addEventListener('input', function (e) {
    let value = e.target.value;

    // Remove tudo que não for número
    value = value.replace(/\D/g, '');

    // Limita a 14 dígitos (CNPJ)
    value = value.substring(0, 14);

    // Aplica a máscara: 00.000.000/0000-00
    if (value.length > 12) {
        value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
    } else if (value.length > 8) {
        value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*/, '$1.$2.$3/$4');
    } else if (value.length > 5) {
        value = value.replace(/^(\d{2})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
    } else if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d{0,3}).*/, '$1.$2');
    }

    e.target.value = value;
});

const cepInput = document.getElementById('cep');

// Máscara ao digitar
cepInput.addEventListener('input', function (event) {
    let value = event.target.value.replace(/\D/g, '');
    if (value.length > 8) value = value.slice(0, 8);
    value = value.replace(/^(\d{5})(\d)/, '$1-$2');
    event.target.value = value;
});
