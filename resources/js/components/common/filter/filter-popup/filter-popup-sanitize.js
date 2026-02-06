/**
 * Filter Popup Sanitizer
 * ----------------------
 * Removes dangerous tags and inline event handlers from HTML.
 */

/**
 * Sanitizes HTML content before injecting into the DOM.
 *
 * @param {string} html - Raw HTML string fetched from the server.
 * @returns {DocumentFragment} Sanitized fragment safe to append.
 */
export const sanitizeHTML = html => {
    const template = document.createElement('template');
    template.innerHTML = html;

    template.content
        .querySelectorAll(
            'script, iframe, object, [onload], [onclick], [onerror]'
        )
        .forEach(el => el.remove());

    return template.content;
};
