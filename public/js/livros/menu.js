document.addEventListener('click', function (e) {
    const exemplarInput = document.getElementById("exemplar");

    if (e.target.id === "increment") {
        exemplarInput.value = parseInt(exemplarInput.value) + 1;
    }

});
