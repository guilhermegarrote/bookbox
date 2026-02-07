/**
 * Modal DOM Utilities
 * --------------------
 * Responsible for inserting, showing, hiding and removing modal DOM elements.
 */

/**
 * Creates and inserts the modal overlay and HTML into the DOM.
 *
 * @param {string} html
 * @param {string} modalId
 * @returns {{overlay: HTMLDivElement, modalWrapper: HTMLElement}}
 * @throws {Error} If modal wrapper is not found inside HTML
 */
export function createModalOverlay(html, modalId) {
    const overlay = document.createElement('div');

    overlay.id = `${modalId}-overlay`;
    overlay.classList.add('modal-overlay', 'hidden');
    overlay.setAttribute('aria-hidden', 'true');
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.innerHTML = html;

    document.body.appendChild(overlay);

    const modalWrapper = overlay.querySelector(`#${modalId}`);

    if (!modalWrapper) {
        overlay.remove();
        throw new Error(`Modal with ID "${modalId}" not found in loaded HTML`);
    }

    return { overlay, modalWrapper };
}

/**
 * Shows a modal and its overlay.
 *
 * @param {HTMLElement} modal
 */
export function showModalDom(modal) {
    const overlay = modal.closest('.modal-overlay');

    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
    }

    modal.classList.remove('hidden');
    modal.removeAttribute('inert');
    modal.setAttribute('aria-hidden', 'false');
}

/**
 * Hides a modal and its overlay.
 *
 * @param {HTMLElement} modal
 */
export function hideModalDom(modal) {
    const overlay = modal.closest('.modal-overlay');

    if (overlay) {
        overlay.classList.remove('active');
        overlay.classList.add('hidden');
        overlay.setAttribute('aria-hidden', 'true');
    }

    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    modal.setAttribute('inert', '');
}

/**
 * Runs closing animation for modal and overlay.
 *
 * @param {HTMLElement} modal
 * @returns {Promise<void>}
 */
export function animateModalClose(modal) {
    return new Promise((resolve) => {
        const overlay = modal.closest('.modal-overlay');
        if (!overlay) return resolve();

        modal.classList.add('closing');
        overlay.classList.add('closing');

        const handleAnimationEnd = () => {
            modal.classList.remove('closing');
            overlay.classList.remove('closing');
            resolve();
        };

        overlay.addEventListener('animationend', handleAnimationEnd, { once: true });
    });
}

/**
 * Removes modal overlay from DOM.
 *
 * @param {string} modalId
 */
export function removeModalOverlay(modalId) {
    const overlay = document.getElementById(`${modalId}-overlay`);
    if (overlay) overlay.remove();
}
