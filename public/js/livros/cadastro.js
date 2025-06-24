document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'botao-cadastar' && e.target.dataset.entidade === 'livro') {
        fetch('modals/cadastro_exemplar')
        .then(response => response.text())
        .then(html => {
            document.getElementById("modal-container").innerHTML = html;
            document.getElementById("overlay").style.display = "flex";
        })
        .catch(error => console.error('Erro ao carregar o modal:', error));
    }
});