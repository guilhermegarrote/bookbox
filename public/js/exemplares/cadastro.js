document.addEventListener('click', function (e) {
    const exemplarInput = document.getElementById("exemplar");

    if (e.target.id === "increment") {
        exemplarInput.value = parseInt(exemplarInput.value) + 1;
    }

    else if (e.target.id === "decrement") {
        const quantidade = parseInt(exemplarInput.value);
        if (quantidade > 1) {
            exemplarInput.value = quantidade - 1;
        }
    }
});
