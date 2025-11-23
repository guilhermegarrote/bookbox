import '@css/pages/settings.css';
import { logout } from '@js/api/auth/logout.js';
import ModalManager from '@js/components/ui/modal-manager.js';
import { route } from 'ziggy-js';

export default class SettingsPanelManager {
    constructor() {
        this.modalManager = new ModalManager();
    }

    async open() {
        const modalId = "settings-panel";
        const url = route("settings.view");

        await this.modalManager.loadModalContent(url, modalId, {
            onInit: (modal) => {
                const overlay = modal.closest('.modal-overlay');
                if (overlay) overlay.classList.add('settings-overlay');

                this._bindSettingsEvents(modal);
            }
        });

        document.getElementById('submit-logout')?.addEventListener('click', () => {
            logout();
        });
    }

    _bindSettingsEvents(modal) {
        const finalizeBtn = modal.querySelector(".settings-btn-finalize");
        if (finalizeBtn) {
            finalizeBtn.addEventListener("click", () => {
                this.modalManager.removeModal(modal.id);
            });
        }
    }
}
