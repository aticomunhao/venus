@vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])



<div class="row justify-content-around" id="masterRow">


    <div class="col drag-column">
        <button class="btn add"><i class="bi bi-plus-lg"></i></button>
        <button class="btn remove"><i class="bi bi-dash"></i></button>
        <div class="card item mt-3 mb-3" draggable="true">
            <div class="card-body">
                <h5 class="card-title">Idade</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">Maior que 5</h6>
            </div>
        </div>



        <div class="card item mt-3 mb-3" draggable="true">
            <div class="card-body">
                <h5 class="card-title">Membro</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">Aluno ESDE 3</h6>
                <h6 class="card-subtitle mb-2 text-body-secondary">Últimos 5 anos</h6>
            </div>
        </div>
        <div class="card item mt-3 mb-3" draggable="true">
            <div class="card-body">
                <h5 class="card-title">Membro</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">Aluno Mocidade 3</h6>
                <h6 class="card-subtitle mb-2 text-body-secondary">Últimos 5 anos</h6>
            </div>
        </div>



    </div>
    <div class="col drag-column">
        <button class="btn add"><i class="bi bi-plus-lg"></i></button>
        <button class="btn remove"><i class="bi bi-dash"></i></button>
    </div>
    <div class="col drag-column">
        <button class="btn add"><i class="bi bi-plus-lg"></i></button>
        <button class="btn remove"><i class="bi bi-dash"></i></button>
        <div class="card item mt-3 mb-3" draggable="true">
            <div class="card-body">
                <h5 class="card-title">Função</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">Atendente Fraterno</h6>
            </div>
        </div>

    </div>
</div>


</div>






<style>
    .item {
        cursor: move;
        transition: 0.3s;
        -webkit-touch-callout: none;
        /* iOS Safari */
        -webkit-user-select: none;
        /* Safari */
        -khtml-user-select: none;
        /* Konqueror HTML */
        -moz-user-select: none;
        /* Old versions of Firefox */
        -ms-user-select: none;
        /* Internet Explorer/Edge */
        user-select: none;

    }

    /* Fundo do item arrastado*/
    .item.dragging {
        filter: brightness(85%);
        opacity: 0.5;
        cursor: default
    }

    /* Some com o que tem dentro do item */
    .item.dragging * {
        visibility: hidden
    }

    /* Efeito hover enquanto não é arrastado */
    .item:not(.dragging):hover {
        filter: brightness(97%);
    }

    .drag-column {
        background-color: rgb(236, 236, 236);
        border-radius: 1vh;
        margin-left: 10px;
        margin-right: 10px;
    }
</style>

<script>
    $(document).ready(function() {
        $('.drag-column').css('height', '70vh')
    });
</script>

<script>
    item = document.getElementsByClassName('item') // Pega o item selecionado
    column = document.getElementsByClassName('drag-column') // Pega a coluna dos itens


    // Aciona o CSS que gera o fundo cinza
    function addDrag(event) {
        event.dataTransfer.setData('text/plain', '')
        requestAnimationFrame(() => event.target.classList.add('dragging'))
    }

    // Volta o CSS para o original
    function removeDrag(event) {

        event.target.classList.remove('dragging')
    }

    function overDrag(event) {
        event.preventDefault();

        const dragTask = document.querySelector('.dragging')
        const target = event.target.closest('.item, .drag-column')

        if (!target || target == dragTask) return

        if (target.classList.contains('drag-column')) {
            const lastItem = target.lasElementChild
            if (!lastItem) {
                target.appendChild(dragTask)
            } else {
                const bottom = lastItem.getBoundingClientRect()
                event.clientY > bottom && target.appendChild(dragTask)
            }
            target.children.length === 0 && target.appendChild(dragTask)
        } else {
            const {
                top,
                height
            } = target.getBoundingClientRect()
            const distance = top + height / 2

            if (event.clientY < distance) {
                target.before(dragTask)
            } else {
                target.after(dragTask)
            }
        }


    }

    function dropDrag(event) {
        event.preventDefault();
    }

    $('.add').click(function(e) {
        e.preventDefault();
        $('#masterRow').after($(this).parent()).append(
            '<div class="col drag-column">' +
            '<button class="btn add"><i class="bi bi-plus-lg"></i></button>' +
            '<button class="btn remove"><i class="bi bi-dash"></i></button>' +
            '</div>'
        )
    })
    $('.remove').click(function(e) {
        e.preventDefault();
        $(this).parent().eq(0).remove()
    })

    // Adiona uma classe para cada um
    for (const [key, value] of Object.entries(item)) {
        this.addEventListener('dragstart', addDrag)
        this.addEventListener('dragend', removeDrag)
        this.addEventListener('dragover', overDrag)
        this.addEventListener('drop', dropDrag)
    }
</script>
