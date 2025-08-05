import { sendCode } from '../../api/auth/send-recovery-code.js';
import { showErrors, clearErrors, notifyError } from '@/utils/formErrors';
import '../../../css/pages/auth.css';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('send-recovery-code-form');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    if (!form) {
        console.error('Formulário de envio de código não encontrado.');
        return;
    }

    const emailInput = form.querySelector('input[name="email"]');
    const savedEmail = localStorage.getItem('recoveryEmail');

    if (savedEmail && emailInput) {
        emailInput.value = savedEmail;
        localStorage.removeItem('recoveryEmail');
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) {
            return;
        }

        const email = emailInput.value;
        const validationErrors = validateEmail(email);

        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, responseData } = await sendCode(email, csrfToken);

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
            console.error('Erro ao enviar código:', error);
            notifyError('Erro inesperado ao redefinir a senha. Tente novamente.');
        }
    });
});

function validateEmail(email) {
    const errors = [];

    if (!email || typeof email !== 'string' || email.trim() === '') {
        errors.push({ field: 'email', messages: ['O e-mail é obrigatório.'] });
    } else {
        const cleanEmail = email.trim();

        if (cleanEmail.length > 320) {
            errors.push({ field: 'email', messages: ['O e-mail não pode ter mais que 320 caracteres.'] });
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(cleanEmail)) {
            errors.push({ field: 'email', messages: ['Formato do e-mail inválido.'] });
        }
    }

    return errors;
}
