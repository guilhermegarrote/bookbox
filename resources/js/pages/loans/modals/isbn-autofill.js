import { findBookByIsbn } from '@js/api/books/find-by-isbn';
import { isValidISBN } from '@js/utils/validation/app-validation.js';
import { notifyError } from '@/utils/formErrors';

/**
 * Initializes the ISBN auto-fill behavior.
 *
 * Required DOM elements:
 * - #isbn
 * - #title
 * - #copy_number
 *
 * @returns {void}
 */
export function initIsbnAutoFill() {
    const isbnInput = document.getElementById('isbn');
    if (!isbnInput) return;

    let timeout = null;
    let lastIsbn = null;
    let lastResult = null;

    isbnInput.addEventListener('keyup', () => {
        clearTimeout(timeout);

        timeout = setTimeout(async () => {
            const raw = isbnInput.value;
            const isbn = raw.replace(/\D/g, '');

            if (!isValidISBN(isbn)) return;

            await autofillFromIsbn(isbn);
        }, 300);
    });

    isbnInput.addEventListener('blur', async () => {
        const raw = isbnInput.value;
        const isbn = raw.replace(/\D/g, '');

        if (!isValidISBN(isbn)) return;

        await autofillFromIsbn(isbn);
    });

    /**
     * Fetches book data from API and applies it to the form.
     * Uses caching to avoid duplicated requests.
     *
     * @async
     * @param {string} isbn - Valid ISBN (digits only).
     * @returns {Promise<void>}
     */
    async function autofillFromIsbn(isbn) {
        if (isbn === lastIsbn && lastResult) {
            applyBookData(lastResult);
            return;
        }

        try {
            const response = await findBookByIsbn(isbn);
            const data = response?.data;

            lastIsbn = isbn;
            lastResult = data
                ? { book: data.book, copies: data.available_copies ?? [] }
                : null;

            if (!data) {
                clearBookFields();
                notifyError('Livro não encontrado.');
                return;
            }

            applyBookData(lastResult);
        } catch {
            notifyError('Não foi possível carregar as informações do livro.');
        }
    }
}

/**
 * Applies book data into the form fields.
 * Populates the available copies dropdown.
 *
 * @param {Object} payload
 * @param {Object} payload.book - Book information object.
 * @param {Array} payload.copies - List of available copies.
 * @returns {void}
 */
function applyBookData({ book, copies }) {
    const titleInput = document.getElementById('title');
    const copySelect = document.getElementById('copy_number');

    if (titleInput) {
        titleInput.value = book?.title ?? '';
        titleInput.classList.toggle('has-value', !!titleInput.value);
    }

    if (copySelect) {
        copySelect.innerHTML = '';

        copies.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.number;
            opt.textContent = c.number;
            copySelect.appendChild(opt);
        });

        const hasCopies = copies.length > 0;

        copySelect.disabled = !hasCopies;

        if (hasCopies) {
            copySelect.value = copies[0].number;
            copySelect.classList.add('has-value');
            copySelect.setAttribute('required', 'required');
        } else {
            copySelect.classList.remove('has-value');
            copySelect.removeAttribute('required');
            notifyError('Não há exemplares disponíveis deste livro.');
        }
    }
}

/**
 * Clears book-related fields and resets the copies dropdown.
 *
 * @returns {void}
 */
function clearBookFields() {
    const titleInput = document.getElementById('title');
    const copySelect = document.getElementById('copy_number');

    if (titleInput) {
        titleInput.value = '';
        titleInput.classList.remove('has-value');
    }

    if (copySelect) {
        copySelect.innerHTML = '';
        copySelect.disabled = true;
        copySelect.removeAttribute('required');
        copySelect.classList.remove('has-value');
    }
}
