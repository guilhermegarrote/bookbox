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
}
