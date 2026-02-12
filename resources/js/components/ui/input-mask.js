import IMask from 'imask';

/**
 * Applies input masks to form fields (CPF, phone, ISBN, and label copies).
 */
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

    const dateInputs = document.querySelectorAll('input[id$="date"]');

    dateInputs.forEach(input => {
        const currentYear = new Date().getFullYear();

        IMask(input, {
            mask: Date,
            pattern: 'd/`m/`Y',
            lazy: false,
            autofix: true,
            blocks: {
                d: { mask: IMask.MaskedRange, from: 1, to: 31 },
                m: { mask: IMask.MaskedRange, from: 1, to: 12 },
                Y: { mask: IMask.MaskedRange, from: currentYear - 10, to: currentYear + 10 }
            },
            format: function (date) {
                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            },
            parse: function (str) {
                const parts = str.split('/');
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }
        });
    });
}
