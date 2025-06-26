const btnFiltro = document.getElementById('btn-filtro');
const popup = document.getElementById('popupFiltro');

btnFiltro.addEventListener('click', function (e) {
    e.stopPropagation();

    if (popup.style.display === 'block') {
        popup.style.display = 'none';
    } else {
        const rect = btnFiltro.getBoundingClientRect();
        const offset = 0; // Espaço entre botão e popup

        // POSICIONA O POP-UP ABAIXO DO BOTÃO, ALINHADO À ESQUERDA
        // Mantém o pop-up alinhado verticalmente (mesma altura que o botão)
        /*popup.style.top = rect.top + window.scrollY + 'px';*/

        // Posiciona o pop-up à direita do botão, com um pequeno espaço (offset)
        /*popup.style.left = rect.right + window.scrollX + offset + 'px';*/
        popup.style.top = rect.bottom + window.scrollY - rect.height+ offset + 'px';
        popup.style.left = rect.left + window.scrollX + 'px';

        const isAlunosPage = window.location.search.includes('pagina=alunos');
        const rotaFiltro = isAlunosPage ? 'popups/filtro_aluno' : 'popups/filtro_emprestimo';

        fetch(rotaFiltro)
            .then(res => res.text())
            .then(html => {
                popup.innerHTML = html;
                popup.style.display = 'block';
            });
    }
});

popup.addEventListener('click', function (e) {
    e.stopPropagation();
});

// Fecha o popup se clicar fora dele
document.addEventListener('click', function () {
    popup.style.display = 'none';
});


