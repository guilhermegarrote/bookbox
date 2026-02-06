import SettingsPanelManager from "@js/components/managers/settings-panel-manager";
import { openFilterPopup, hidePopup, btnFilter } from "../filter/filter-popup/index";
import { initSearch } from "./search";

document.addEventListener('DOMContentLoaded', () => {
    if (!window.App.filterUIInstance) {
        window.App.filterUIInstance = window.App.initFilterUI?.();
    }

    const filterUI = window.App.filterUIInstance;
    const popup = document.getElementById('popup-filter');

    initSearch(filterUI);

    if (btnFilter) {
        btnFilter.addEventListener('click', () => {
            const popupVisible = popup?.classList.contains('visible');
            if (popupVisible) {
                hidePopup();
            } else {
                openFilterPopup(filterUI);
            }
        });

        document.addEventListener('click', (event) => {
            if (!event.isTrusted) return;

            if (!popup.contains(event.target) && event.target !== btnFilter) {
                hidePopup();
            }
        });
    }

    const settingsBtn = document.getElementById('open-settings');
    if (settingsBtn) {
        const settingsManager = new SettingsPanelManager();
        settingsBtn.addEventListener('click', async () => {
            settingsManager.open();
        });
    }
});

