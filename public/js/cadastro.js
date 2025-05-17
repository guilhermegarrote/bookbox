document.getElementById('cadastro-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    // Limpa todas as mensagens de erro antes de validar
    document.querySelectorAll('.cadastro-erro').forEach(el => el.innerText = '');

    const nome = document.querySelector('[name="nome"]');
    const email = document.querySelector('[name="email"]');
    const senha = document.querySelector('[name="senha"]');
    const senhaConfirmada = document.querySelector('[name="senhaConfirmada"]');

    let temErro = false;

    // Validação nome e sobrenome
    const nomePartes = nome.value.trim().split(" ");
    if (nomePartes.length < 2) {
        document.getElementById('erro-nome').innerText = "Informe nome e sobrenome.";
        temErro = true;
    }

    // Validação email
    if (!email.value.includes("@") || email.value.length < 5) {
        document.getElementById('erro-email').innerText = "Digite um email válido.";
        temErro = true;
    }

    // Validação senha
    if (senha.value.length < 6) {
        document.getElementById('erro-senha').innerText = "A senha deve ter pelo menos 6 caracteres contando com as especiais e com os numeros.";
        temErro = true;
    }

    // Validação confirmação senha
    if (senha.value !== senhaConfirmada.value) {
        document.getElementById('erro-senhaConfirmada').innerText = "As senhas não coincidem.";
        temErro = true;
    }

    // Se teve algum erro, para aqui
    if (temErro) return;

    // Se passou das validações, envia para o backend
    const formData = new FormData(this);
    const formObject = Object.fromEntries(formData);

    const erroMsgElement = document.getElementById('mens-erro');

    try {
        const response = await fetch('/bookbox/api/usuarios/cadastrar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formObject)
        });

        let data;
        try {
            data = await response.json();
        } catch (e) {
            erroMsgElement.innerText = "Erro inesperado. Tente novamente.";
            return;
        }

        if (!response.ok) {
            // Se backend enviou array de mensagens, exibe todas
            if (Array.isArray(data.mensagem)) {
                erroMsgElement.innerHTML = data.mensagem.map(msg => `<div>${msg}</div>`).join("");
            } else {
                erroMsgElement.innerText = data?.mensagem || "Erro ao cadastrar. Verifique os dados.";
            }
            return;
        }

        if (data.redirecionar) {
            window.location.href = data.redirecionar;
        }

    } catch (error) {
        erroMsgElement.innerText = "Erro ao fazer o cadastro. Tente novamente.";
        console.error("Erro ao cadastrar usuário:", error);
    }
});

// Limpa o erro do campo enquanto o usuário digita nele (sem apagar os outros erros)
document.querySelectorAll('.cadastro-input').forEach(input => {
    input.addEventListener('input', () => {
        const erroId = 'erro-' + input.name;
        const erroDiv = document.getElementById(erroId);
        if (erroDiv) erroDiv.innerText = "";

        // Também limpa mensagem geral (de backend)
        document.getElementById('mens-erro').innerText = "";
    });
});
