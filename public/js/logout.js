function logout() {
    fetch("/bookbox/logout", { method: "POST" })
        .then(response => {
            if (!response.ok) {
                throw new Error("Erro ao tentar fazer logout.");
            }
            window.location.href = "/bookbox/login";
        })
        .catch(error => {
            console.error("Erro ao fazer logout:", error);
            alert("Ocorreu um erro ao tentar fazer logout. Tente novamente.");
        });
}
