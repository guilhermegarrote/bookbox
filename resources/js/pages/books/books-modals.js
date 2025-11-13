import { route } from 'ziggy-js';

import { showErrors, notifySuccess, notifyError } from '@/utils/formErrors';

import { applyInputMasks } from '../../components/ui/input-mask.js';
import ModalManager from '../../components/managers/modal-manager.js';

import { createBook } from '../../api/books/create.js';
import { deleteBook } from '../../api/books/delete.js';
import { findBookByIsbn } from '../../api/books/find-by-isbn.js';
import { updateBook } from '../../api/books/update.js';
import { findLoanByBarcode } from '../../api/loans/find-by-barcode.js';

import booksTable from './table.js';
import { openGeneraLabelModal } from '../labels/labels-modals.js';
import { openManagerCopiesModal } from '../copies/copies-modals.js';

const modalManager = new ModalManager();

async function openCreateModal() {
    const url = route('books.createModal');

    try {
        await modalManager.loadModalContent(url, 'bookCreateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initBooksSelects(window.App.filterData);
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'bookCreateModal',
            buttonId: 'submit-create',
            onSubmit: createBook,
            onSuccess: () => {
                modalManager.removeModal('bookCreateModal');
                booksTable.updateTable();
                notifySuccess('Livro cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao cadastrar livro');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

async function openUpdateModal(bookId) {
    const url = route('books.updateModal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'bookUpdateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initBooksSelects(window.App.filterData);
            }
        });

        document.getElementById('btn-cancel-update')?.addEventListener('click', () => {
            openMenuModal(bookId);
        });

        modalManager.bindFormSubmit({
            modalId: 'bookUpdateModal',
            buttonId: 'submit-update',
            onSubmit: (data) => updateBook(bookId, data),
            onSuccess: () => {
                openMenuModal(bookId);
                booksTable.updateTable();
                notifySuccess('Livro atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao atualizar o livro');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

async function openMenuModal(bookId) {
    const url = route('books.menuModal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'bookMenuModal');

        document.getElementById('open-edit-modal')?.addEventListener('click', () => {
            openUpdateModal(bookId);
        });

        document.getElementById('open-manager-copies-modal')?.addEventListener('click', () => {
            openManagerCopiesModal(bookId, modalManager);
        });

        document.getElementById('open-generate-label-modal')?.addEventListener('click', () => {
            openGeneraLabelModal(modalManager);
        });

        document.getElementById('submit-delete')?.addEventListener('click', async () => {
            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente excluir este livro?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                await deleteBook(bookId);
                modalManager.removeModal('bookMenuModal');
                booksTable.updateTable();
                notifySuccess('Livro excluído com sucesso!');
            } catch (err) {
                notifyError(err.message || 'Erro ao excluir o livro');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

export function initBooksSelects(filterData) {
    const genreNameSelect = document.getElementById('genre_name');
    if (!genreNameSelect) return;

    const uniqueBy = (array, key) =>
        [...new Map(array.map(item => [item[key], item])).values()];

    const populateSelect = (select, items, valueKey, textKey, placeholder, presetValue) => {
        select.innerHTML = '';
        select.appendChild(new Option(placeholder, ''));
        items.forEach(item => select.appendChild(new Option(item[textKey], item[valueKey])));

        const initial = presetValue || select.dataset.value || '';
        if (initial && items.some(i => i[valueKey] == initial)) {
            select.value = initial;
        } else {
            select.selectedIndex = 0;
        }
        select.disabled = false;
    };

    function updateSelects(selected = {}) {
        const selectedGenre_name = selected.genre_name || genreNameSelect.value || genreNameSelect.dataset.value || '';

        const genre_names = uniqueBy(filterData, 'genre_name').map(c => ({ value: c.genre_name, label: c.genre_name }));
        populateSelect(genreNameSelect, genre_names, 'value', 'label', 'Gênero', selectedGenre_name);
    }

    genreNameSelect.addEventListener('change', () => updateSelects({ genre_name: genreNameSelect.value }));

    updateSelects();
}

function bindOpenButtons() {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn) addBtn.addEventListener('click', openCreateModal);
    const labelBtn = document.querySelector('.btn-label');
    if (labelBtn) labelBtn.addEventListener('click', () => openGeneraLabelModal(modalManager));

    document.querySelectorAll('table.data-table tbody tr').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.getAttribute('data-book-id');
            if (id) openMenuModal(id);
        });
    });
}

function initScannerListener() {
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
                const isIsbn = (code) => {
                    const isbn10 = /^\d{9}[\dX]$/i;
                    const isbn13 = /^\d{13}$/;
                    return isbn10.test(code) || isbn13.test(code);
                };

                let bookId = null;

                if (isIsbn(barcodeBuffer)) {
                    const response = await findBookByIsbn(barcodeBuffer);
                    bookId = response?.data?.book?.id ?? null;
                } else {
                    const response = await findLoanByBarcode(barcodeBuffer);
                    bookId = response?.data?.bookId ?? null;
                }

                barcodeBuffer = '';

                if (bookId) {
                    openMenuModal(bookId);
                } else {
                    notifyError('Nenhuma informação encontrada para o código lido.');
                }
            } catch (err) {
                console.error(err);
                notifyError(err.message || 'Erro ao processar o código.');
            }
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(() => (barcodeBuffer = ''), 300);
        }
    });
}

export function initBooksModals() {
    bindOpenButtons();
    initScannerListener();
    document.addEventListener('tableUpdated', bindOpenButtons);
}

export { openCreateModal, openUpdateModal, openMenuModal };
