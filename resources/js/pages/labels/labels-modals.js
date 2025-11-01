import { generateLabels } from "../../api/labels/generate";

export async function openGeneraLabelModal(modalManager) {
    const url = route('labels.generateModal');

    try {
        await modalManager.loadModalContent(url, 'labelGenerateModal');

        modalManager.bindFormSubmit({
            modalId: 'labelGenerateModal',
            buttonId: 'submit-generate-label',
            onSubmit: generateLabels(data),
            onSuccess: () => {},
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao gerar etiquetas');
            }
        });
    } catch (err) {
        console.error(err);
    }
}


