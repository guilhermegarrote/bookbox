import { resetPassword } from '../../api/auth/reset-password.js';
import { showErrors, clearErrors, notifyError } from '@/utils/formErrors';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reset-password-form');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    if (!form) {
        console.error('Formulário de redefinição de senha não encontrado.');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) {
            return;
        }

        const formData = new FormData(form);
        const formValues = Object.fromEntries(formData.entries());

        const validationErrors = validateForm(formValues);
        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, responseData } = await resetPassword(formValues, csrfToken);

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
            console.error('Erro ao redefinir senha:', error);
            notifyError('Erro inesperado ao redefinir a senha. Tente novamente.');
        }
    });

    function validateForm(data) {
        const errors = [];

        const password = data.password?.trim();
        const confirmation = data.password_confirmation?.trim();

        if (!password) {
            errors.push({ field: 'password', messages: ['A senha é obrigatória.'] });
        } else {
            const messages = [];
            if (password.length < 8 || password.length > 16) {
                messages.push('A senha deve ter entre 8 e 16 caracteres.');
            }
            if (!/[A-Z]/.test(password)) {
                messages.push('A senha deve conter ao menos uma letra maiúscula.');
            }
            if (!/[a-z]/.test(password)) {
                messages.push('A senha deve conter ao menos uma letra minúscula.');
            }
            if (!/[0-9]/.test(password)) {
                messages.push('A senha deve conter ao menos um número.');
            }
            if (!/[^A-Za-z0-9]/.test(password)) {
                messages.push('A senha deve conter ao menos um caractere especial.');
            }

            if (messages.length > 0) {
                errors.push({ field: 'password', messages });
            }
        }

        if (!confirmation) {
            errors.push({ field: 'password_confirmation', messages: ['A confirmação de senha é obrigatória.'] });
        } else if (confirmation !== password) {
            errors.push({ field: 'password_confirmation', messages: ['A confirmação deve ser idêntica à senha.'] });
        }

        return errors;
    }
});
