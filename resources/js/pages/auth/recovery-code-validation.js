import { validateCode } from '../../api/auth/recovery-code-validation.js';
import { resendCode } from '../../api/auth/resend-recovery-code.js';
import { showErrors, clearErrors, notifyError, notifySuccess } from '@/utils/formErrors';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('recovery-code-validation-form');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    const resendLink = document.getElementById('resend-code-btn');
    const inputs = document.querySelectorAll('.verification-input');

    if (!form) {
        console.error('Formulário de validação de código não encontrado.');
        return;
    }

    if (!resendLink) {
        console.error('Elemento de reenvio não encontrado.');
        return;
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            let value = e.target.value;

            if (!/^\d$/.test(value)) {
                e.target.value = '';
                return;
            }

            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) {
            return;
        }

        const code = Array.from(inputs).map(input => input.value).join('');
        const validationErrors = validateCodeInput(code);

        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, responseData } = await validateCode(code, csrfToken);

            if (!ok) {
                if (responseData.errors) {
                    showErrors(responseData.errors);
                } else {
                    notifyError(responseData.error || responseData.message || 'Erro desconhecido.');
                }
                return;
            }

            if (responseData.redirect) {
                window.location.href = responseData.redirect;
            }
        } catch (error) {
            console.error('Erro ao validar código:', error);
            notifyError('Erro inesperado ao redefinir a senha. Tente novamente.');
        }
    });

    resendLink.addEventListener('click', async (event) => {
        event.preventDefault();

        try {
            const { ok, responseData } = await resendCode(csrfToken);

            if (!ok) {
                if (responseData.errors) {
                    showErrors(responseData.errors);
                } else {
                    notifyError(responseData.error || responseData.message || 'Erro desconhecido.');
                }
                return;
            } else {
                notifySuccess('Código reenviado com sucesso');
            }
        } catch (error) {
            console.error('Erro ao reenviar código:', error);
            notifyError('Erro inesperado ao reenviar código. Tente novamente.');
        }
    });
});

function validateCodeInput(code) {
    const errors = [];

    code = code.trim();

    if (!code || typeof code !== 'string') {
        errors.push({ field: 'code', messages: ['O código é obrigatório.'] });
    } else {
        if (code.length !== 6) {
            errors.push({ field: 'code', messages: ['O código deve ter seis dígitos.'] });
        } else if (!/^\d{6}$/.test(code)) {
            errors.push({ field: 'code', messages: ['O código deve conter apenas números.'] });
        }
    }

    return errors;
}
