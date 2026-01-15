import IMask from 'imask';

export function applyInputMasks() {
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        IMask(cpfInput, {
            mask: '000.000.000-00'
        });
    }

    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        IMask(phoneInput, {
            mask: [
                { mask: '(00) 00000-0000' }
            ]
        });
    }

    const isbnInput = document.getElementById('isbn');
    if (isbnInput) {
        IMask(isbnInput, {
            mask: [
                { mask: '000-0-0000-0000-0' }
            ]
        });
    }

    const labelInputs = document.querySelectorAll('[data-copies-input]');
    labelInputs.forEach(element => {
        IMask(element, {
            mask: /^\d*(?:-\d*)?(?:,\d*(?:-\d*)?)*$/,
            lazy: false,
        });
    });
}
