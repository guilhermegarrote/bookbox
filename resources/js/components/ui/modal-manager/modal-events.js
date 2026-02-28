/**
 * Modal Events Utilities
 * -----------------------
 * Handles ESC key behavior and close button bindings.
 */

/**
 * Binds close events to modal buttons and overlay click.
 *
 * @param {HTMLElement} modal
 * @param {Function} onClose
 */
export function bindCloseEvents(modal, onClose) {
    if (!modal) return;

    const closeButtons = modal.querySelectorAll('#btn-close, #modal-message-decline');

    closeButtons.forEach((btn) => {
        btn.addEventListener('click', () => onClose(), { once: true });
    });

    const overlay = modal.closest('.modal-overlay');

    if (overlay) {
        overlay.addEventListener(
            'click',
            (e) => {
                if (e.target === overlay) onClose();
            },
            { once: true }
        );
    }
}

/**
 * Creates a global ESC key listener.
 *
 * @param {Function} getLastModalId
 * @param {Function} onCloseModal
 * @returns {function(KeyboardEvent): void}
 */
export function createEscListener(getLastModalId, onCloseModal) {
    return (e) => {
        if (e.key !== 'Escape') return;

        const lastModalId = getLastModalId();
        if (lastModalId) onCloseModal(lastModalId);
    };
}
