/**
 * Global form validators for authentication forms.
 * Each function returns an array of objects in the format:
 * [{ field: 'field_name', messages: ['Error message 1', 'Error message 2'] }]
 */

/**
 * Validate an email field for presence, length, and format.
 * @param {string} email
 * @returns {Array} Array of error objects
 */
export function validateEmailField(email) {
    const errors = [];
    const value = (email || '').trim();

    if (!value) {
        errors.push('O e-mail é obrigatório.');
    } else {
        if (value.length > 320) errors.push('O e-mail não pode ter mais que 320 caracteres.');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) errors.push('Formato do e-mail inválido.');
    }

    return errors.length ? [{ field: 'email', messages: errors }] : [];
}

/**
 * Validate a name field: must be 3–100 characters, at least two words, letters only.
 * @param {string} name
 * @returns {Array} Array of error objects
 */
export function validateNameField(name) {
    const errors = [];
    const value = (name || '').trim();

    if (!value) {
        errors.push('O nome é obrigatório.');
    } else {
        if (value.length < 3 || value.length > 100) errors.push('O nome deve conter entre 3 e 100 caracteres.');
        if (value.split(/\s+/).length < 2) errors.push('O nome deve conter pelo menos nome e sobrenome.');
        if (!/^[A-Za-zÀ-ú\s'-]+$/.test(value)) errors.push('Apenas letras, espaços, hífens e apóstrofos são permitidos no nome.');
    }

    return errors.length ? [{ field: 'name', messages: errors }] : [];
}

/**
 * Validate password complexity: length 8–16, uppercase, lowercase, number, special character.
 * @param {string} password
 * @returns {Array} Array of error objects
 */
export function validatePasswordField(password) {
    const errors = [];
    const value = (password || '').trim();

    if (!value) {
        errors.push('A senha é obrigatória.');
    } else {
        if (value.length < 8 || value.length > 16) errors.push('A senha deve ter entre 8 e 16 caracteres.');
        if (!/[A-Z]/.test(value)) errors.push('A senha deve conter pelo menos uma letra maiúscula.');
        if (!/[a-z]/.test(value)) errors.push('A senha deve conter pelo menos uma letra minúscula.');
        if (!/[0-9]/.test(value)) errors.push('A senha deve conter pelo menos um número.');
        if (!/[^A-Za-z0-9]/.test(value)) errors.push('A senha deve conter pelo menos um caractere especial.');
    }

    return errors.length ? [{ field: 'password', messages: errors }] : [];
}

/**
 * Validate password confirmation: must match password.
 * @param {string} password
 * @param {string} confirmation
 * @returns {Array} Array of error objects
 */
export function validatePasswordConfirmation(password, confirmation) {
    const errors = [];
    const confirmValue = (confirmation || '').trim();
    const passwordValue = (password || '').trim();

    if (!confirmValue) {
        errors.push('A confirmação de senha é obrigatória.');
    } else if (confirmValue !== passwordValue) {
        errors.push('A confirmação de senha deve ser idêntica à senha informada.');
    }

    return errors.length ? [{ field: 'password_confirmation', messages: errors }] : [];
}

/**
 * Validate acceptance of terms checkbox.
 * @param {boolean} [termsChecked=document.getElementById('terms')?.checked]
 * @returns {Array} Array of error objects
 */
export function validateTermsField(termsChecked = document.getElementById('terms')?.checked) {
    if (!termsChecked) {
        return [{
            field: 'terms',
            messages: ['Você deve aceitar os Termos de Condição e a Política de Privacidade.']
        }];
    }
    return [];
}

/**
 * Validate 6-digit numeric recovery code.
 * @param {string} code
 * @returns {Array} Array of error objects
 */
export function validateRecoveryCode(code) {
    const errors = [];
    const value = (code || '').trim();

    if (!value) {
        errors.push('O código é obrigatório.');
    } else if (!/^\d{6}$/.test(value)) {
        errors.push('O código deve ter exatamente seis dígitos numéricos.');
    }

    return errors.length ? [{ field: 'code', messages: errors }] : [];
}

/**
 * Combine multiple validator results into a single array.
 * @param {...Array} validators
 * @returns {Array} Flattened array of errors
 */
export function combineValidations(...validators) {
    return validators.flat();
}
