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

function preencherDataDevolucao(inputId) {
    const input = document.getElementById(inputId);
    if (!input || input.value) return;

    const hoje = new Date();
    hoje.setDate(hoje.getDate() + 7);

    const dia = String(hoje.getDate()).padStart(2, '0');
    const mes = String(hoje.getMonth() + 1).padStart(2, '0');
    const ano = hoje.getFullYear();

    input.value = `${dia}/${mes}/${ano}`;

    input.classList.add('preenchido');
}

function permitirSomenteNumeros(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
}