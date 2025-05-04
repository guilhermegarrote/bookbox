const btnFiltro = document.getElementById('btn-filtro');
const popup = document.getElementById('popupFiltro');

document.addEventListener('click', function (e) {
    btnFiltro.addEventListener('click', function (e) {
        e.stopPropagation();

        if (popup.style.display === 'block') {
            popup.style.display = 'none';
            return;
        }

        const rect = btnFiltro.getBoundingClientRect();
        popup.style.top = rect.bottom + window.scrollY + 'px';
        popup.style.left = rect.left + window.scrollX + 'px';

        fetch('popups/filtro')
            .then(res => res.text())
            .then(html => {
                popup.innerHTML = html;
                popup.style.display = 'block';
            });
    });

    document.addEventListener('click', function () {
        popup.style.display = 'none';
    });
});