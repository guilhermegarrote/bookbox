/**
 * Modal Message Helper
 * ---------------------
 * Handles configuration and events of the message modal.
 */

/**
 * Configures modal message content.
 *
 * @param {HTMLElement} modal
 * @param {{message: string, acceptText: string, declineText: string}} options
 */
export function configureModalMessage(modal, { message, acceptText, declineText }) {
    const msgEl = modal.querySelector('#modal-message-text');
    const acceptBtn = modal.querySelector('#modal-accept');
    const declineBtn = modal.querySelector('#modal-decline');

    msgEl.textContent = message;
    acceptBtn.textContent = acceptText;
    declineBtn.textContent = declineText;
}

/**
 * Binds events to message modal buttons.
 *
 * @param {HTMLElement} modal
 * @param {Function} removeModal
 * @param {(value: boolean) => void} resolve
 */
export function bindModalMessageEvents(modal, removeModal, resolve) {
    const acceptBtn = modal.querySelector('#modal-accept');
    const declineBtn = modal.querySelector('#modal-decline');
    const overlay = modal.closest('.modal-overlay');

    acceptBtn.addEventListener(
        'click',
        async () => {
            await removeModal(modal.id);
            resolve(true);
        },
        { once: true }
    );

    declineBtn.addEventListener(
        'click',
        async () => {
            await removeModal(modal.id);
            resolve(false);
        },
        { once: true }
    );

    overlay.addEventListener(
        'click',
        async (e) => {
            if (e.target === overlay) {
                await removeModal(modal.id);
                resolve(false);
            }
        },
        { once: true }
    );
}
