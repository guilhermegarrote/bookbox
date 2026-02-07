import { fetchMetadata } from '@js/api/books/fetch-metadata';
import { isValidISBN } from '@js/utils/validation/app-validation';
import { notifyError } from '@js/utils/formErrors';

let lastIsbn = null;
let lastData = null;

/**
 * Initializes ISBN autofill by listening to user input and fetching metadata.
 */
export function initIsbnAutoFill() {
    const isbnInput = document.getElementById('isbn');
    if (!isbnInput) return;

    let timeout;

    isbnInput.addEventListener('keyup', () => {
        clearTimeout(timeout);

        timeout = setTimeout(async () => {
            const isbn = normalizeIsbn(isbnInput.value);
            if (!isValidISBN(isbn)) return;

            await autofill(isbn);
        }, 300);
    });
}

/**
 * Normalizes ISBN input by removing special characters and forcing uppercase.
 *
 * @param {string} value
 * @returns {string}
 */
function normalizeIsbn(value) {
    return value.replace(/[^0-9Xx]/g, '').toUpperCase();
}

/**
 * Fetches metadata for the given ISBN and applies it to the form fields.
 * Uses cached data if the ISBN was already fetched.
 *
 * @param {string} isbn
 */
async function autofill(isbn) {
    if (isbn === lastIsbn && lastData) {
        applyMetadata(lastData);
        return;
    }

    try {
        const response = await fetchMetadata(isbn);

        if (response?.ok) {
            const data = response?.data || {};

            lastIsbn = isbn;
            lastData = data;

            applyMetadata(data);
        } else {
            notifyError(response?.data?.message);
        }
    } catch (e) {
        console.error(e);
    }
}

/**
 * Fills form fields using metadata keys as element IDs.
 *
 * @param {Object} data
 */
function applyMetadata(data) {
    Object.entries(data).forEach(([key, value]) => {
        const field = document.getElementById(key);
        if (!field) return;

        field.value = value ?? '';
        field.classList.toggle('has-value', Boolean(field.value.trim()));

        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
    });
}
