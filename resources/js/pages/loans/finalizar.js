document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'botao-cadastrar' && e.target.dataset.entidade === 'emprestimo') {
        fetch('popups/confirmacao_finalizacao_emprestimo')
        .then(response => response.text())
        .then(html => {
            document.getElementById("modal-container").innerHTML = html;
            document.getElementById("overlay").style.display = "flex";
        })
        .catch(error => console.error('Erro ao carregar o modal:', error));
    }
});