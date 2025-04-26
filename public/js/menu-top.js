$(document).ready(function () {
    $(document).on('click', '.menu-top-btn', function () {
        var pagina = $(this).data('pagina');
        var url = new URL(window.location);
        url.searchParams.set('pagina', pagina);
        window.location.href = url.toString();
    });
});

document.addEventListener('click', function (e) {
    const targetId = e.target.id;

    if (targetId === 'abrir-configuracoes') {
        logout();
    }
});