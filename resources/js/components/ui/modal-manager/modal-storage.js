/**
 * Modal Storage Utilities
 * ------------------------
 * Handles persistence of modal state inside sessionStorage.
 */

/**
 * Saves modal state (modalId + data) into sessionStorage.
 *
 * @param {string} key
 * @param {string} modalId
 * @param {object} data
 */
export function saveModalState(key, modalId, data) {
    try {
        sessionStorage.setItem(key, JSON.stringify({ modalId, data }));
    } catch (e) {}
}

/**
 * Retrieves saved modal state from sessionStorage.
 *
 * @param {string} key
 * @returns {{modalId: string, data: object}|null}
 */
export function getSavedModalState(key) {
    try {
        return JSON.parse(sessionStorage.getItem(key));
    } catch {
        return null;
    }
}

/**
 * Clears saved modal state.
 *
 * @param {string} key
 */
export function clearSavedModalState(key) {
    try {
        sessionStorage.removeItem(key);
    } catch (e) {}
}

/**
 * Dispatches a custom event with saved modal state (if exists).
 *
 * @param {string} key
 * @param {string} [eventName='reopenModal']
 */
export function dispatchSavedModalEvent(key, eventName = 'reopenModal') {
    const saved = getSavedModalState(key);

    if (!saved) return;

    window.dispatchEvent(new CustomEvent(eventName, { detail: saved }));
    clearSavedModalState(key);
}
