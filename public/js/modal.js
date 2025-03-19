function abrirModal(modalType, id = null) {
    let url = `modals/${modalType}`; // Caminho do modal correspondente
    if (id) {
        url += `?id=${id}`;
    }

    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById("modal-container").innerHTML = html;
            document.getElementById("overlay").style.display = "flex";
        })
        .catch(error => console.error('Erro ao carregar o modal:', error));
}

function fecharModal() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("modal-container").innerHTML = '';
}

window.onclick = function (event) {
    const modal = document.getElementById("overlay");
    if (event.target === modal) {
        fecharModal();
    }
};

function realizarEmprestimo(event) {
    event.preventDefault();

    const formData = new FormData(document.querySelector("#cadastroEmprestimoModal form"));

    fetch('/bookbox/api/cadastrar_emprestimo', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            alert(data.mensagem);
            fecharModal();
        })
        .catch(error => console.error('Erro ao cadastrar:', error));
}

