import { openCreateModal } from './modals/create';
import { openUpdateModal } from './modals/update';
import { openMenuModal } from './modals/menu';
import { initStudentsSelects } from './modals/selects';
import { initBarcodeScannerListener } from './modals/scanner';
import { bindOpenButtons } from '@js/components/students/handlers/bind-buttons';

export function initStudentsModals({ modalManager, table }) {
    const handlers = {
        openMenuModal: (id) => openMenuModal(modalManager, id, table),
        openCreateModal: () => openCreateModal(modalManager, table),
    };

    bindOpenButtons(handlers);

    initBarcodeScannerListener(handlers.openMenuModal);

    document.addEventListener('tableUpdated', () => {
        bindOpenButtons(handlers);
    });
}

export {
    openCreateModal,
    openUpdateModal,
    openMenuModal,
    initStudentsSelects
};
