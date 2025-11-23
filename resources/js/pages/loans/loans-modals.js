import { openCreateModal, openCreateModalWithIsbn } from './modals/create';
import { openMenuModal } from './modals/menu';
import { initBarcodeScannerListener } from './modals/scanner';
import { bindOpenButtons } from '@js/components/loans/handlers/bind-buttons';
import { renderSidebar } from './render/sidebar';

export function initLoansModals({ modalManager, table }) {
    const handlers = {
        openMenuModal: (loanId) => openMenuModal(modalManager, loanId, table),
        openCreateModal: () => openCreateModal(modalManager, table),
        openCreateModalWithIsbn: (isbn) => openCreateModalWithIsbn(modalManager, table, isbn),
    };

    bindOpenButtons(handlers);

    initBarcodeScannerListener(
        handlers.openMenuModal,
        handlers.openCreateModalWithIsbn
    );

    document.addEventListener('tableUpdated', () => {
        bindOpenButtons(handlers);

        const sidebarData = table?.lastResponse?.data?.data_sidebar;
        if (sidebarData) {
            renderSidebar(sidebarData);
        }
    });
}

export {
    openCreateModal,
    openMenuModal,
};
