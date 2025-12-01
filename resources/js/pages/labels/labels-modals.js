import { generateLabels } from "@js/api/labels/generate";
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { notifySuccess, notifyError } from '@js/utils/formErrors';

export async function openGenerateLabelModal(modalManager) {
    const url = route('labels.generate-modal');

    try {
        await modalManager.loadModalContent(url, 'labelGenerateModal', {
            onInit: () => applyInputMasks()
        });

        modalManager.bindFormSubmit({
            modalId: 'labelGenerateModal',
            buttonId: 'submit-generate-label',

            onSubmit: () => {
                const books = collectBooksForGenerateLabel();
                generateLabels(books);
            },
            onSuccess: () => {
                modalManager.removeModal('labelGenerateModal');

                notifySuccess('Etiquetas geradas com sucesso!');
            },
            onError: (err) => {
                err.errors
                    ? showErrors(err.errors)
                    : notifyError(err.message || 'Erro ao gerar etiquetas');
            },
        });

    } catch (err) {
        console.error(err);
    }
}

function collectBooksForGenerateLabel() {
    const rows = document.querySelectorAll('#labelGenerateModal tbody tr');
    const selected = [];

    rows.forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');

        if (checkbox && checkbox.checked) {

            const isbn = row
                .querySelector('td:nth-child(4)')
                .textContent
                .trim()
                .replace(/-/g, '');

            const copiesInput = row.querySelector('[data-copies-input]');
            const copies = copiesInput?.value.trim() || '0';

            selected.push({
                isbn,
                copies,
            });
        }
    });

    return { books: selected };
}
