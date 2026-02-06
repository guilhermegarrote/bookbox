/**
 * Gets the HTML for a button prototype by icon name.
 *
 * @param {string} icon
 * @returns {string}
 */
export function getButtonHTML(icon) {
    const btn = document.querySelector(`#button-prototypes [data-icon="${icon}"]`);
    return btn ? btn.outerHTML : '';
}
