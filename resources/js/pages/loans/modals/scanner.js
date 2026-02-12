import { notifyError } from '@/utils/formErrors';
import { findLoanByBarcode } from '@js/api/loans/find-by-barcode';
import { findBookByIsbn } from '@js/api/books/find-by-isbn';
import { isValidISBN } from '@js/utils/validation/app-validation';

/**
 * Initializes the global barcode scanner listener.
 *
 * @param {Function} openMenuModal - Callback to open loan menu modal (expects loanId).
 * @param {Function} openCreateModalWithIsbn - Callback to open loan create modal (expects isbn).
 * @returns {void}
 */
export function initBarcodeScannerListener(openMenuModal, openCreateModalWithIsbn) {
    let barcodeBuffer = '';
    let timer = null;

    document.addEventListener('keydown', async (e) => {
        const active = document.activeElement;
        const modalOpen = document.querySelector('.modal.show');

        if (modalOpen) return;
        if (active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName)) return;

        if (e.key === 'Enter' && barcodeBuffer) {
            clearTimeout(timer);

            const code = barcodeBuffer.trim();
            barcodeBuffer = '';

            try {
                await processBarcode(openMenuModal, openCreateModalWithIsbn, code);
            } catch (err) {
                console.error(err);
                notifyError(err || 'Erro ao processar o código.');
            }

            return;
        }

        if (e.key.length === 1) {
            barcodeBuffer += e.key;

            clearTimeout(timer);
            timer = setTimeout(() => (barcodeBuffer = ''), 250);
        }
    });
}

/**
 * Processes scanned barcode/ISBN and triggers the appropriate modal.
 *
 * @async
 * @param {Function} openMenuModal
 * @param {Function} openCreateModalWithIsbn
 * @param {string} code - Raw scanned barcode string.
 * @returns {Promise<void>}
 */
async function processBarcode(openMenuModal, openCreateModalWithIsbn, code) {
    if (isValidISBN(code)) {
        const response = await findBookByIsbn(code);

        if (response.ok && response.data?.book) {
            return openCreateModalWithIsbn(code);
        }

        return notifyError('Livro não encontrado para o código lido.');
    }

    const response = await findLoanByBarcode(code);

    if (response.ok && response.data?.loanId) {
        return openMenuModal(response.data.loanId);
    }

    return notifyError('Empréstimo não encontrado para o código.');
}
