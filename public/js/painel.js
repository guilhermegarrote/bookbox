$(document).ready(function() {
    $('.menu-top-btn').click(function() {
        var pagina = $(this).data('pagina');
        
        window.location.href = '/bookbox/painel?pagina=' + pagina;
    });
});

function selectAll() {
    const checkboxes = document.querySelectorAll('#select-row');

    checkboxes.forEach(cb => cb.checked = document.getElementById('select-all').checked);
}

function toggleSelectAllButton() {
    const checkboxes = document.querySelectorAll('#select-row');

    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

    document.getElementById('select-all').checked = allChecked;
}


