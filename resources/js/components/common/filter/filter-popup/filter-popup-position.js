/**
 * Filter Popup Positioner
 * -----------------------
 * Positions popup relative to filter button.
 */

import { btnFilter, popupFilter } from './filter-popup-dom';

/**
 * Positions the popup element below the filter button.
 *
 * @returns {void}
 */
export const positionPopup = () => {
    if (!btnFilter || !popupFilter) return;

    const rect = btnFilter.getBoundingClientRect();
    popupFilter.style.top = `${rect.bottom + window.scrollY}px`;
    popupFilter.style.left = `${rect.left + window.scrollX}px`;
};
