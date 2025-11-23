import { fetchMetadata } from '@js/api/books/fetch-metadata.js';
import { isValidISBN } from '@js/utils/validation/app-validation.js';

let lastIsbn = null;
let lastData = null;

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

function normalizeIsbn(value) {
    return value.replace(/[^0-9Xx]/g, '').toUpperCase();
}

async function autofill(isbn) {
    if (isbn === lastIsbn && lastData) {
        applyMetadata(lastData);
        return;
    }

    try {
        const response = await fetchMetadata(isbn);
        const data = response?.data || {};

        lastIsbn = isbn;
        lastData = data;

        applyMetadata(data);
    } catch (e) {
        console.error(e);
    }
}

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
