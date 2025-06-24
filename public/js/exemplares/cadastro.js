document.addEventListener('click', function (e) {
    const exemplarInput = document.getElementById("exemplar");
    if (!exemplarInput) return;  // Garante que o input existe

    // Verifica se o clique foi no botão de incremento ou em algum filho dele
    if (e.target.id === "increment" || e.target.closest('#increment')) {
        exemplarInput.value = parseInt(exemplarInput.value) + 1;
    }

    // Verifica se o clique foi no botão de decremento ou em algum filho dele
    if (e.target.id === "decrement" || e.target.closest('#decrement')) {
        const quantidade = parseInt(exemplarInput.value);
        if (quantidade > 1) {
            exemplarInput.value = quantidade - 1;
        }
    }
});
