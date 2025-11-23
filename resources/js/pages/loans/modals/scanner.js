import { notifyError } from '@/utils/formErrors';
import { findLoanByBarcode } from '@js/api/loans/find-by-barcode.js';
import { findBookByIsbn } from '@js/api/books/find-by-isbn.js';
import { isValidISBN } from '@js/utils/validation/app-validation.js';

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
                notifyError(err.message || 'Erro ao processar o código.');
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

