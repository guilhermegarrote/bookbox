/**
 * Filter Popup DOM Module
 * -----------------------
 * Responsible only for accessing popup-related DOM elements
 * and toggling popup visibility.
 */

/**
 * Button that triggers the filter popup.
 * @type {HTMLElement|null}
 */
export const btnFilter = document.getElementById('btn-filter');

/**
 * Popup container element where filter UI is injected.
 * @type {HTMLElement|null}
 */
export const popupFilter = document.getElementById('popup-filter');

/**
 * Makes the filter popup visible.
 *
 * @returns {void}
 */
export const showPopup = () => popupFilter?.classList.add('visible');

/**
 * Hides the filter popup and clears its content.
 *
 * @returns {void}
 */
export const hidePopup = () => {
    if (!popupFilter) return;
    popupFilter.classList.remove('visible');
    popupFilter.innerHTML = '';
};
