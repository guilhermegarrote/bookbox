import { notifyError } from '@js/utils/formErrors';
import { findLoanByBarcode } from '@js/api/loans/find-by-barcode';

/**
 * Initializes a global barcode scanner listener for student loans.
 *
 * @param {Function} openMenuModal - Callback to open the student menu modal (expects studentId)
 */
export function initBarcodeScannerListener(openMenuModal) {
    let barcodeBuffer = '';
    let barcodeTimer = null;

    document.addEventListener('keydown', async (e) => {
        const activeElement = document.activeElement;
        const modalOpen = document.querySelector('.modal.show') !== null;

        if (activeElement && ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeElement.tagName)) return;
        if (modalOpen) return;

        if (e.key === 'Enter' && barcodeBuffer) {
            clearTimeout(barcodeTimer);

            try {
                const data = await findLoanByBarcode(barcodeBuffer);
                barcodeBuffer = '';

                if (data?.data?.studentId) {
                    openMenuModal(data.data.studentId);
                } else {
                    notifyError('Nenhuma informação encontrada para o código lido');
                }
            } catch (err) {
                console.error(err);
                notifyError(err || 'Erro ao processar o código.');
            }
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(() => (barcodeBuffer = ''), 300);
        }
    });
}
