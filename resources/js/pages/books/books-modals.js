import { openCreateModal, openCreateModalWithIsbn } from './modals/create';
import { openUpdateModal } from './modals/update';
import { openMenuModal } from './modals/menu';
import { initBooksSelects } from './modals/selects';
import { initScannerListener } from './modals/scanner';
import { bindOpenButtons } from '@js/components/books/handlers/bind-buttons';
import { openGenerateLabelModal } from '@js/pages/labels/labels-modals.js';

/**
 * Initializes all book-related modals and event bindings.
 * Sets up button handlers, scanner listener, and rebinds events after table updates.
 *
 * @param {Object} params
 * @param {Object} params.modalManager Modal manager instance used to open and control modals.
 * @param {Object} params.table Table instance used to refresh data after modal actions.
 */
export function initBooksModals({ modalManager, table }) {
    const handlers = {
        openMenuModal: (id) => openMenuModal(modalManager, id, table),
        openCreateModal: () => openCreateModal(modalManager, table),
        openGenerateLabelModal: (id) => openGenerateLabelModal(modalManager, id, table),
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
