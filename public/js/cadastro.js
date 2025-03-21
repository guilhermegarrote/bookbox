document.getElementById('cadastro-form').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('/bookbox/cadastro', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            window.location.href = data.redirecionar;
        } else {
            let erros = Object.values(data.erro).join("\n");
            alert(erros);
        }
    } catch (error) {
        console.error("Erro ao cadastrar usuário:", error);
    }
});
