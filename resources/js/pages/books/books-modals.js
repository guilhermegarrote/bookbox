import { openCreateModal, openCreateModalWithIsbn } from './modals/create';
import { openUpdateModal } from './modals/update';
import { openMenuModal } from './modals/menu';
import { initBooksSelects } from './modals/selects';
import { initScannerListener } from './modals/scanner';
import { bindOpenButtons } from '@js/components/books/handlers/bind-buttons';
import { openGeneraLabelModal } from '@js/pages/labels/labels-modals.js';

export function initBooksModals({ modalManager, table }) {
    const handlers = {
        openMenuModal: (id) => openMenuModal(modalManager, id, table),
        openCreateModal: () => openCreateModal(modalManager, table),
        openLabelModal: (id) => openGeneraLabelModal(modalManager, id, table),
    };

    bindOpenButtons(handlers);

    initScannerListener(
        handlers.openMenuModal,
        (isbn) => openCreateModalWithIsbn(modalManager, table, isbn)
    );

    document.addEventListener('tableUpdated', () => {
        bindOpenButtons(handlers);
    });
}

export {
    openCreateModal,
    openUpdateModal,
    openMenuModal,
    initBooksSelects
};
