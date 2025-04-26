document.addEventListener('click', function (e) {
    if (e.target && (e.target.id === 'botao-fechar-modal' || e.target.closest('#botao-fechar-modal'))) {
        fecharModal();
    } else if (e.target && e.target.id === 'botao-abrir-modal') {
        abrirModal(e.target.dataset.modal);
    }
});

window.onclick = function (event) {
    if (event.target === document.getElementById("overlay")) {
        fecharModal();
    }
};

function fecharModal() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("modal-container").innerHTML = '';
}

function abrirModal(modalType, id = null) {
    let url = `modals/${modalType}`;
    if (id) {
        url += `?id=${id}`;
    }

    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById("modal-container").innerHTML = html;
            document.getElementById("overlay").style.display = "flex";

            if (modalType === 'cadastro_emprestimo') {
                preencherDataDevolucao('data-devolucao');
            }
        })
        .catch(error => console.error('Erro ao carregar o modal:', error));
}