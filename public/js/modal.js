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
        })
        .catch(error => console.error('Erro ao carregar o modal:', error));
}

function fecharModal() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("modal-container").innerHTML = '';
}

window.onclick = function(event) {
    if (event.target === document.getElementById("overlay")) {
        fecharModal();
    }
};

function realizarEmprestimo(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append("nomeEstudante", document.getElementById("nomeEstudante").value);
    formData.append("cpf", document.getElementById("cpf").value);
    formData.append("serie", document.getElementById("serie").value);
    formData.append("curso", document.getElementById("curso").value);
    formData.append("nomeLivro", document.getElementById("nomeLivro").value);
    formData.append("codigoIBSN", document.getElementById("codigoIBSN").value);
    formData.append("dataDevolucao", document.getElementById("dataDevolucao").value);
    formData.append("exemplar", document.getElementById("exemplar").value);
    
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