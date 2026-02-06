/**
 * FilterUI Storage
 * ----------------
 * Handles saving/loading filter state using localStorage.
 */

const STORAGE_KEY = 'lastFilters';

/**
 * Loads saved filters from localStorage.
 *
 * @returns {Object<string,string>}
 */
export const loadSavedFilters = () => {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    } catch {
        return {};
    }
};

/**
 * Saves filters into localStorage.
 *
 * @param {Object<string,string>} params
 * @returns {void}
 */
export const saveFilters = params => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(params));
    } catch (e) {
        console.warn('Failed to save filter state', e);
    }
};

/**
 * Clears stored filter state.
 *
 * @returns {void}
 */
export const clearSavedFilters = () => {
    localStorage.removeItem(STORAGE_KEY);
};
