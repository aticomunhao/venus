//Quando executar a pagina usa o select2

$(function () {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});


// Inicializa os tooltips Bootstrap 5
document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});


