document.getElementById('login-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const formData = new FormData(this);
    const formObject = Object.fromEntries(formData);

    const erroMsgElement = document.getElementById('men-erro');
    erroMsgElement.innerText = ""; // Limpa mensagens anteriores

    try {
        const response = await fetch('/bookbox/api/login', {
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
            // Se o JSON não estiver válido, mostrar este erro genérico paia
            erroMsgElement.innerText = "Erro inesperado. Tente novamente.";
            return;
        }

        if (!response.ok) {
            // TENTA exibir a mensagem de erro retornada pela api
            const erros = typeof data.erro === 'string'
                ? data.erro
                : Object.values(data.erro || {}).join("\n");

            erroMsgElement.innerText = erros || "Email ou senha incorretos.";
            return;
        }

        if (data.redirecionar) {
            window.location.href = data.redirecionar;
        }

    } catch (error) {
        erroMsgElement.innerText = "Erro ao fazer login. Verifique se tudo está correto.";
        console.error("Erro ao fazer login:", error);
    }
});
