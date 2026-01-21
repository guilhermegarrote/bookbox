import { generateLabels } from "@js/api/labels/generate";
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { notifySuccess, notifyError } from '@js/utils/formErrors';

let selectedBooks = {};
let remainingLabels = null;
const labelsPerSheet = 16;

export async function openGenerateLabelModal(modalManager) {
    const url = route('labels.generate-modal');

    try {
        await modalManager.loadModalContent(url, 'labelGenerateModal', {
            onInit: () => {
                const modal = document.getElementById('labelGenerateModal');
                if (!modal) return;

                applyInputMasks();
                initSearch();
                bindCheckboxAndCopiesEvents();

                const tbody = modal.querySelector('tbody');
                if (tbody && Object.keys(selectedBooks).length > 0) {
                    restoreSelectedBooks(tbody);
                    updateLabelCounter();
                }
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'labelGenerateModal',
            buttonId: 'submit-generate-label',

            onSubmit: async () => {
                collectBooksForGenerateLabel();

                if (remainingLabels > 0) {
                    const missingLabels = labelsPerSheet - remainingLabels;

                    const message =
                        `Faltam ${missingLabels} etiquetas para completar a última folha. Deseja prosseguir mesmo assim?`;

                    const confirmed = await modalManager.showModalMessage({
                        message,
                        acceptText: 'Continuar',
                        declineText: 'Cancelar'
                    });

                    if (!confirmed) {
                        openGenerateLabelModal(modalManager);
                        throw new Error('SUBMIT_CANCELLED');
                    }
                }

                const booksArray = Object.entries(selectedBooks).map(
                    ([isbn, bookData]) => ({ isbn, copies: bookData.value })
                );

                selectedBooks = {};

                return generateLabels({ books: booksArray });
            },

            onSuccess: () => {
                modalManager.removeModal('labelGenerateModal');
                notifySuccess('Etiquetas geradas com sucesso!');
            },

            onError: (err) => {
                if (err.message === 'SUBMIT_CANCELLED') return;

                err.errors
                    ? showErrors(err.errors)
                    : notifyError(err.message || 'Erro ao gerar etiquetas');
            },
        });

    } catch (err) {
        console.error(err);
    }
}

/**
 * Handles live search with debounce, reloads table via AJAX,
 * and restores selected books.
 */
function initSearch() {
    const modal = document.getElementById('labelGenerateModal');
    if (!modal) throw new Error('Modal não encontrado');

    const input = modal.querySelector('#item-search');
    const list = modal.querySelector('tbody');
    if (!input || !list) return;

    let debounceTimer = null;

    input.addEventListener('input', () => {
        const value = input.value.trim();
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(async () => {
            collectBooksForGenerateLabel();

            const fetchUrl = value === '' ? route('labels.generate-modal') : `${route('labels.generate-modal')}?search=${encodeURIComponent(value)}`;

            try {
                const res = await fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;

                list.innerHTML = await res.text();

                restoreSelectedBooks(list);
                applyInputMasks();
                bindCheckboxAndCopiesEvents();
            } catch (err) {
                console.error('Erro ao buscar livros:', err);
            }
        }, 400);
    });
}

/**
 * Collects all selected books and their copies into memory
 */
function collectBooksForGenerateLabel() {
    const rows = document.querySelectorAll('#labelGenerateModal tbody tr');

    rows.forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        const isbn = row.querySelector('td:nth-child(4)').textContent.trim().replace(/-/g, '');
        const copiesInput = row.querySelector('[data-copies-input]');
        const value = copiesInput?.value.trim() || copiesInput?.placeholder.replace(/^Ex:\s*/i, '');

        if (checkbox?.checked) {
            selectedBooks[isbn] = {
                value: value,
                placeholder: copiesInput?.placeholder || ''
            };
        } else {
            delete selectedBooks[isbn];
        }
    });
}

/**
 * Restores checkbox and copies state, and moves selected rows to the top
 */
function restoreSelectedBooks(list) {
    const rows = Array.from(list.querySelectorAll('tr'));
    const selectedRows = [];
    const unselectedRows = [];

    rows.forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        const isbn = row.querySelector('td:nth-child(4)').textContent.trim().replace(/-/g, '');
        const copiesInput = row.querySelector('[data-copies-input]');

        if (isbn in selectedBooks) {
            checkbox.checked = true;
            const bookData = selectedBooks[isbn];
            copiesInput.value = bookData.value || '';
            copiesInput.placeholder = bookData.placeholder || copiesInput.placeholder;
            selectedRows.push(row);
        } else {
            checkbox.checked = false;
            copiesInput.value = '';
            unselectedRows.push(row);
        }
    });

    list.innerHTML = '';
    selectedRows.concat(unselectedRows).forEach(row => list.appendChild(row));
}

/**
 * Binds events to checkboxes and copies inputs to update memory in real-time
 */
function bindCheckboxAndCopiesEvents() {
    const rows = document.querySelectorAll('#labelGenerateModal tbody tr');

    rows.forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        const copiesInput = row.querySelector('[data-copies-input]');
        const isbn = row.querySelector('td:nth-child(4)').textContent.trim().replace(/-/g, '');

        if (!checkbox) return;

        const updateSelectedBook = () => {
            if (checkbox.checked) {
                selectedBooks[isbn] = {
                    value: copiesInput?.value.trim() || '',
                    placeholder: copiesInput?.placeholder || ''
                };
            } else {
                delete selectedBooks[isbn];
            }

            updateLabelCounter();
        };

        checkbox.addEventListener('change', () => {
            updateSelectedBook();
        });

        if (copiesInput) {
            copiesInput.addEventListener('input', () => {
                if (copiesInput.value.trim() !== '') {
                    checkbox.checked = true;
                } else {
                    checkbox.checked = false;
                }
                updateSelectedBook();
            });
        }
    });
}

/**
 * Calculates the total number of labels selected based on user input,
 * determines how many full sheets are needed, and updates the label counter UI.
 */
function updateLabelCounter() {
    let totalLabels = 0;

    Object.values(selectedBooks).forEach(book => {
        let copies = parseInt(book.value, 10);

        if (isNaN(copies) || book.value.trim() === '') {
            copies = countCopiesFromInput(book.placeholder);
        } else {
            copies = countCopiesFromInput(book.value.trim());
        }

        totalLabels += copies;
    });

    const fullSheets = Math.floor(totalLabels / labelsPerSheet);
    remainingLabels = totalLabels % labelsPerSheet;

    const counter = document.getElementById('label-counter');
    if (!counter) return;

    if (totalLabels === 0) {
        counter.textContent = 'Nenhuma etiqueta selecionada';
    } else {
        counter.textContent = `Total: ${totalLabels} etiquetas | ${fullSheets} folha(s) completa(s) + ${remainingLabels} etiqueta(s) na folha seguinte`;
    }
}

/**
 * Parses a string representing label copy ranges or individual values
 * and returns the total number of copies calculated from it.
 *
 * Examples:
 * - "1,2,3" → 3
 * - "1-5" → 5
 * - "1-3,5,7-9" → 7
 * - "sem etiquetas" → 0
 *
 * @param {string} value - The input string containing copy information
 * @returns {number} The total number of calculated copies
 */
function countCopiesFromInput(value) {
    if (!value || value.toLowerCase().includes('sem')) return 0;

    const clean = value.replace(/Ex:\s*/i, '');
    const parts = clean.split(',');
    let total = 0;

    parts.forEach(part => {
        if (part.includes('-')) {
            const [start, end] = part.split('-').map(Number);
            if (!isNaN(start) && !isNaN(end)) {
                total += end - start + 1;
            }
        } else {
            const n = Number(part);
            if (!isNaN(n)) total += 1;
        }
    });

    return total;
}
