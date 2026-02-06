/**
 * FilterUI Utilities
 * ------------------
 * Helper functions used across the FilterUI class.
 */

/**
 * Returns unique objects by a given key.
 *
 * @param {Array<Object>} array
 * @param {string} key
 * @returns {Array<Object>}
 */
export const uniqueBy = (array, key) => {
    return [...new Map(array.map(item => [item[key], item])).values()];
};
