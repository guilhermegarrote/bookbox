/**
 * FilterUI Params
 * ---------------
 * Reads current filter parameters from fields and global search input.
 */

/**
 * Reads active filter params from FilterUI instance fields.
 *
 * @param {Array<Object>} fields
 * @returns {Object<string,string>}
 */
export const getCurrentParamsFromFields = fields => {
    const params = {};

    fields.forEach(f => {
        if (!f.element) return;

        const val = f.element.value?.trim();
        if (val) params[f.key] = val;
    });

    const searchInput = document.getElementById('top-nav-search-input');
    const searchVal = searchInput?.value?.trim();
    if (searchVal) params.search = searchVal;

    return params;
};
