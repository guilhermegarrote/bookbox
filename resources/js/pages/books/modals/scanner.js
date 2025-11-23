import { notifyError } from '@/utils/formErrors';
import { findBookByIsbn } from '@js/api/books/find-by-isbn.js';
import { findLoanByBarcode } from '@js/api/loans/find-by-barcode.js';
import { isValidISBN } from '@js/utils/validation/app-validation.js';

export function initScannerListener(openMenuModal, openCreateModalWithIsbn) {
    let buffer = '';
    let timer = null;

    document.addEventListener('keydown', async (e) => {
        if (shouldIgnoreKeyPress(e)) return;

        if (e.key === 'Enter' && buffer) {
            clearTimeout(timer);

            const code = buffer.trim();
            buffer = '';

            try {
                await handleScannedCode(code, { openMenuModal, openCreateModalWithIsbn });
            } catch (err) {
                console.error(err);
                notifyError(err.message || 'Erro ao processar o código.');
            }

            return;
        }

        if (e.key.length === 1) {
            buffer += e.key;
            clearTimeout(timer);
            timer = setTimeout(() => (buffer = ''), 250);
        }
    });
}

function shouldIgnoreKeyPress(e) {
    const active = document.activeElement;
    const modalOpen = document.querySelector('.modal.show') !== null;

    const isTyping =
        active &&
        ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName);

    return modalOpen || isTyping;
}

async function handleScannedCode(code, { openMenuModal, openCreateModalWithIsbn }) {
    if (isValidISBN(code)) {
        const result = await findBookByIsbn(code);

        if (result?.ok && result.data?.book) {
            const bookId = result.data.book.id;
            return openMenuModal(bookId);
        }

        return openCreateModalWithIsbn(code);
    }

    const result = await findLoanByBarcode(code);

    if (result?.ok && result.data?.bookId) {
        return openMenuModal(result.data.bookId);
    }

    notifyError('Nenhuma informação encontrada para o código lido.');
}
