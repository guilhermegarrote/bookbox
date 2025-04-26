document.addEventListener('input', function (e) {
    if (e.target.id === 'cpf') {
        mascararCPF(e.target);
    } else if (e.target.id === 'isbn') {
        mascararISBN(e.target);
    } else if (e.target.id === 'telefone') {
        mascararTelefone(e.target);
    } else if (e.target.id === 'exemplar' || e.target.id === 'periodo') {
        permitirSomenteNumeros(e.target);
    }
});

function mascararCPF(input) {
    let value = input.value.replace(/\D/g, "").slice(0, 11);

    if (value.length > 9) {
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
    } else if (value.length > 6) {
        value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
    } else if (value.length > 3) {
        value = value.replace(/(\d{3})(\d{1,3})/, "$1.$2");
    }

    input.value = value;
}

function mascararISBN(input) {
    let valor = input.value.replace(/\D/g, "").slice(0, 13);

    if (valor.length > 12) {
        valor = valor.replace(/(\d{3})(\d{1})(\d{2})(\d{6})(\d{1})/, "$1-$2-$3-$4-$5");
    } else if (valor.length > 9) {
        valor = valor.replace(/(\d{3})(\d{1})(\d{2})(\d{1,6})/, "$1-$2-$3-$4");
    } else if (valor.length > 5) {
        valor = valor.replace(/(\d{3})(\d{1})(\d{1,2})/, "$1-$2-$3");
    } else if (valor.length > 3) {
        valor = valor.replace(/(\d{3})(\d{1,2})/, "$1-$2");
    }

    input.value = valor;
}

function mascararTelefone(input) {
    let telefone = input.value.replace(/\D/g, '').slice(0, 11);

    if (telefone.length > 10) {
        telefone = telefone.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
    } else if (telefone.length > 6) {
        telefone = telefone.replace(/^(\d{2})(\d{5})(\d{0,4})$/, "($1) $2-$3");
    } else if (telefone.length > 2) {
        telefone = telefone.replace(/^(\d{2})(\d{1,5})$/, "($1) $2");
    }

    input.value = telefone;
}

function permitirSomenteNumeros(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
}
