import { generateLabels } from "@js/api/labels/generate";
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { notifySuccess, notifyError } from '@js/utils/formErrors';

let selectedBooks = {};

export async function openGenerateLabelModal(modalManager) {
    const url = route('labels.generate-modal');

    try {
        await modalManager.loadModalContent(url, 'labelGenerateModal', {
            onInit: () => {
                applyInputMasks();
                initSearch();
                bindCheckboxAndCopiesEvents();
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'labelGenerateModal',
            buttonId: 'submit-generate-label',

            onSubmit: () => {
                collectBooksForGenerateLabel();

                const booksArray = Object.entries(selectedBooks).map(
                    ([isbn, copies]) => ({ isbn, copies })
                );

                generateLabels({ books: booksArray });
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
        const copies = copiesInput?.value.trim() || copiesInput.placeholder.replace(/^Ex:\s*/i, '');

        if (checkbox?.checked) {
            selectedBooks[isbn] = copies;
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
            copiesInput.value = selectedBooks[isbn] ?? '';
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

        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                selectedBooks[isbn] = copiesInput?.value.trim() || null;
            } else {
                delete selectedBooks[isbn];
            }
            updateLabelCounter();
        });

        if (copiesInput) {
            copiesInput.addEventListener('input', () => {
                if (copiesInput.value.trim() !== '') {
                    checkbox.checked = true;
                    selectedBooks[isbn] = copiesInput.value.trim();
                } else {
                    checkbox.checked = false;
                    selectedBooks[isbn] = null;
                }
                updateLabelCounter();
            });
        }
    });
}

function updateLabelCounter() {
    const rows = document.querySelectorAll('#labelGenerateModal tbody tr');
    let totalLabels = 0;

    rows.forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        const copiesInput = row.querySelector('[data-copies-input]');
        const placeholder = copiesInput?.placeholder;

        if (checkbox?.checked) {
            let copies = parseInt(copiesInput.value, 10);

            if (isNaN(copies) || copiesInput.value.trim() === '') {
                copies = countCopiesFromInput(placeholder);
            } else {
                copies = countCopiesFromInput(copiesInput.value.trim());
            }

            totalLabels += copies;
        }
    });

    const labelsPerSheet = 16;
    const fullSheets = Math.floor(totalLabels / labelsPerSheet);
    const remainingLabels = totalLabels % labelsPerSheet;

    const counter = document.getElementById('label-counter');
    if (!counter) return;

    if (totalLabels === 0) {
        counter.textContent = 'Nenhuma etiqueta selecionada';
    } else {
        counter.textContent = `Total: ${totalLabels} etiquetas | ${fullSheets} folha(s) completa(s) + ${remainingLabels} etiqueta(s) na folha seguinte`;
    }
}

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


