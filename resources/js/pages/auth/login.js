import { login } from '@js/api/auth/login.js';
import { setAccessToken } from '@js/api/http-client';
import { showErrors, clearErrors, notifyError } from '@js/utils/formErrors';
import { validateEmailField, combineValidations } from '@js/utils/validation/auth-validation.js';
import '@css/pages/login.css';

/**
 * Handles login form submit and validation.
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');

    if (!form) {
        console.error('Formulário de login não encontrado.');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) return;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const validationErrors = validateForm(data);
        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, data: responseData, status } = await login(data);

            if (!ok) {
                if (responseData.errors) {
                    showErrors(responseData.errors);
                } else if (status === 401 || status === 429) {
                    notifyError(responseData.error || responseData.message || 'Erro desconhecido.');
                } else {
                    notifyError('Erro inesperado no login.');
                }
                return;
            }

            if (responseData.token) {
                setAccessToken(responseData.token);
            }

            if (responseData.redirect) {
                window.location.href = responseData.redirect;
            }
        } catch (error) {
            console.error(error);
            notifyError('Erro técnico ao tentar fazer login. Tente novamente.');
        }
    });

    const recoveryLink = document.querySelector('.auth-link');

    const emailInput = document.querySelector('input[name="email"]');

    if (recoveryLink) {
        recoveryLink.addEventListener('click', (e) => {
            e.preventDefault();

            const email = emailInput?.value?.trim();
            if (email) {
                localStorage.setItem('recoveryEmail', email);
            }

            window.location.href = recoveryLink.getAttribute('href');
        });
    }

    /**
     * Validates login form fields.
     *
     * @param {Object} data
     * @returns {Array}
     */
    function validateForm(data) {
        const emailErrors = validateEmailField(data.email);

        const passwordErrors = !data.password
            ? [{ field: 'password', messages: ['A senha é obrigatória.'] }]
            : [];

        return combineValidations(emailErrors, passwordErrors);
    }
});
