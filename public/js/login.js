document.getElementById('login-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const formData = new FormData(this);
    const formObject = Object.fromEntries(formData);

    try {
        const response = await fetch('/bookbox/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formObject)
        });

        if (!response.ok) {
            const errorData = await response.json();
            const erros = Object.values(errorData.erro).join("\n");
            console.log(erros);
            return;
        }

        const data = await response.json();

        if (data.redirecionar) {
            window.location.href = data.redirecionar;
        }

    } catch (error) {
        console.error("Erro ao fazer login:", error);
    }
});
