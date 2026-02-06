import '@css/pages/settings.css';
import { logout } from '@js/api/auth/logout.js';
import ModalManager from '@js/components/ui/modal-manager.js';
import { initSettingsNavigation } from "@js/pages/settings/index.js";
import { route } from 'ziggy-js';

/**
 * Manages the settings panel modal.
 */
export default class SettingsPanelManager {
    constructor() {
        this.modalManager = new ModalManager();
    }

    /**
     * Opens the settings modal and binds events.
     *
     * @returns {Promise<void>}
     */
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

        initSettingsNavigation();
    }

    /**
     * Binds settings modal internal events.
     *
     * @param {HTMLElement} modal
     */
    _bindSettingsEvents(modal) {
        const finalizeBtn = modal.querySelector(".settings-btn-finalize");

        if (finalizeBtn) {
            finalizeBtn.addEventListener("click", () => {
                this.modalManager.removeModal(modal.id);
            });
        }
    }
}
