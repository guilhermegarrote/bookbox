import { createBooks } from '../../api/books/create.js';
import { updateBooks } from '../../api/books/update.js';
import { deleteBooks } from '../../api/books/delete.js';
import booksTable from '../../pages/books/table.js';
import { applyInputMasks } from '../../components/inputMask.js';
import ModalManager from '../../components/modalManager.js';
import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@/utils/formErrors';

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
            onSubmit: createBooks,
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
            onSubmit: (data) => updateBooks(bookId, data),
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

        document.getElementById('submit-delete')?.addEventListener('click', async () => {
            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente excluir este livro?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                await deleteBooks(bookId);
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
    const publisherSelect = document.getElementById('publisher');;
    if (!genreNameSelect || !publisherSelect) return;

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
        const selectedPublisher = selected.publisher || publisherSelect.value || publisherSelect.dataset.value || '';


        const genre_names = uniqueBy(filterData, 'genre_name').map(c => ({ value: c.genre_name, label: c.genre_name }));
        populateSelect(genreNameSelect, genre_names, 'value', 'label', 'Gênero', selectedGenre_name);

        const filtered = selectedGenre_name ? filterData.filter(d => d.genre_name === selectedGenre_name) : filterData;

        const publishers = uniqueBy(filtered, 'publisher').map(p => ({ value: p.publisher, label: `${p.publisher}°` }));
        populateSelect(publisherSelect, publishers, 'value', 'label', 'Editora', selectedPublisher);

    }

    genreNameSelect.addEventListener('change', () => updateSelects({ genre_name: genreNameSelect.value }));
    publisherSelect.addEventListener('change', () => updateSelects({
        genre_name: genreNameSelect.value,
        publisher: publisherSelect.value
    }));

    updateSelects();
}

function bindOpenButtons() {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn) addBtn.addEventListener('click', openCreateModal);

    document.querySelectorAll('table.data-table tbody tr').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.getAttribute('data-book-id');
            if (id) openMenuModal(id);
        });
    });
}

export function initBooksModals() {
    bindOpenButtons();
    document.addEventListener('tableUpdated', bindOpenButtons);
}

export { openCreateModal, openUpdateModal, openMenuModal };
