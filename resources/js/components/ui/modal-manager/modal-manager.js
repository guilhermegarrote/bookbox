import {
    saveModalState,
    getSavedModalState,
    clearSavedModalState,
    dispatchSavedModalEvent,
} from './modal-storage';

import {
    createModalOverlay,
    showModalDom,
    hideModalDom,
    animateModalClose,
    removeModalOverlay,
} from './modal-dom';

import { getModalData, restoreFormData, toggleFields } from './modal-form';
import { updateFloatingLabels } from './modal-floating-labels';
import { bindCloseEvents, createEscListener } from './modal-events';
import { configureModalMessage, bindModalMessageEvents } from './modal-message';

/**
 * ModalManager
 * -------------
 * Manages modal lifecycle:
 * - loading modal HTML via fetch
 * - inserting/removing modal DOM
 * - storing/restoring form state
 * - close behavior (ESC, overlay click, buttons)
 * - message modal helper
 */
export default class ModalManager {
    constructor() {
        /**
         * Active modals registry.
         * Map key is modalId, value is the modal root element.
         *
         * @type {Map<string, HTMLElement>}
         */
        this.activeModals = new Map();

        /**
         * ESC key listener reference.
         *
         * @type {?function(KeyboardEvent): void}
         */
        this._escListener = null;
    }

    /**
     * Saves modal state to sessionStorage.
     *
     * @param {string} key
     * @param {string} modalId
     * @returns {void}
     */
    saveModalState(key, modalId) {
        const modal = this.activeModals.get(modalId);
        const data = getModalData(modal);
        saveModalState(key, modalId, data);
    }

    /**
     * Gets saved modal state from sessionStorage.
     *
     * @param {string} key
     * @returns {?{modalId: string, data: Object}}
     */
    getSavedModalState(key) {
        return getSavedModalState(key);
    }

    /**
     * Clears saved modal state.
     *
     * @param {string} key
     * @returns {void}
     */
    clearSavedModalState(key) {
        clearSavedModalState(key);
    }

    /**
     * Dispatches a saved modal event and clears state.
     *
     * @param {string} key
     * @param {string} [eventName='reopenModal']
     * @returns {void}
     */
    dispatchSavedModalEvent(key, eventName = 'reopenModal') {
        dispatchSavedModalEvent(key, eventName);
    }

    /**
     * Loads modal HTML from URL and initializes modal lifecycle.
     *
     * @param {string} url
     * @param {string} modalId
     * @param {Object} [options]
     * @param {function(HTMLElement): void} [options.onInit]
     * @param {?string} [options.restoreKey]
     * @param {?Object} [options.initialData]
     * @returns {Promise<void>}
     */
    async loadModalContent(url, modalId, { onInit, restoreKey = null, initialData = null } = {}) {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error('Error loading modal content');
        }

        const html = await response.text();

        await this.insertModalHtml(html, modalId);
        this.bindCloseEvents(modalId);
        this.showModal(modalId);
        this.updateFloatingLabels(modalId);

        const restoredData = restoreKey ? (getSavedModalState(restoreKey)?.data ?? null) : null;

        const mergedData =
            initialData && restoredData
                ? { ...initialData, ...restoredData }
                : initialData ?? restoredData;

        if (mergedData) {
            setTimeout(() => this.restoreFormData(modalId, mergedData), 0);
        }

        if (restoreKey) {
            this.bindAutoSave(modalId, restoreKey);
        }

        if (typeof onInit === 'function') {
            onInit(this.activeModals.get(modalId));
        }
    }

    /**
     * Inserts modal HTML into the DOM.
     *
     * @param {string} html
     * @param {string} modalId
     * @returns {Promise<void>}
     */
    async insertModalHtml(html, modalId) {
        await this.closeAll({ remove: true });

        const { modalWrapper } = createModalOverlay(html, modalId);

        this.activeModals.set(modalId, modalWrapper);
    }

    /**
     * Shows a modal.
     *
     * @param {string} modalId
     * @returns {void}
     */
    showModal(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        showModalDom(modal);
    }

    /**
     * Hides a modal.
     *
     * @param {string} modalId
     * @returns {void}
     */
    hideModal(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        hideModalDom(modal);
    }

    /**
     * Removes a modal (with animation).
     *
     * @param {string} modalId
     * @param {Object} [options]
     * @param {string} [options.pendingKey]
     * @param {string} [options.eventName]
     * @returns {Promise<void>}
     */
    async removeModal(modalId, { pendingKey, eventName } = {}) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        await animateModalClose(modal);

        this.unbindAutoSave(modalId);
        removeModalOverlay(modalId);

        this.activeModals.delete(modalId);

        if (pendingKey) {
            dispatchSavedModalEvent(pendingKey, eventName || 'reopenModal');
        }
    }

    /**
     * Closes all active modals.
     *
     * @param {Object} [options]
     * @param {boolean} [options.remove=true]
     * @returns {Promise<void>}
     */
    async closeAll({ remove = true } = {}) {
        for (const modalId of [...this.activeModals.keys()]) {
            if (remove) await this.removeModal(modalId);
            else this.hideModal(modalId);
        }
    }

    /**
     * Gets modal form data.
     *
     * @param {string} modalId
     * @returns {Object}
     */
    getModalData(modalId) {
        const modal = this.activeModals.get(modalId);
        return getModalData(modal);
    }

    /**
     * Restores modal form data.
     *
     * @param {string} modalId
     * @param {Object} data
     * @returns {void}
     */
    restoreFormData(modalId, data = {}) {
        const modal = this.activeModals.get(modalId);
        restoreFormData(modal, data);
    }

    /**
     * Enables/disables modal fields.
     *
     * @param {HTMLElement[]} fields
     * @param {boolean} enable
     * @returns {void}
     */
    toggleFields(fields, enable) {
        toggleFields(fields, enable);
    }

    /**
     * Binds close button and overlay click behavior.
     *
     * @param {string} modalId
     * @returns {void}
     */
    bindCloseEvents(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        bindCloseEvents(modal, () => this.removeModal(modalId));

        if (!this._escListener) {
            this._escListener = createEscListener(
                () => Array.from(this.activeModals.keys()).pop(),
                (id) => this.removeModal(id)
            );

            document.addEventListener('keydown', this._escListener);
        }
    }

    /**
     * Updates floating labels for modal fields.
     *
     * @param {string} modalId
     * @returns {void}
     */
    updateFloatingLabels(modalId) {
        const modal = this.activeModals.get(modalId);
        updateFloatingLabels(modal);
    }

    /**
     * Automatically saves modal form state into sessionStorage.
     *
     * @param {string} modalId
     * @param {string} restoreKey
     * @returns {void}
     */
    bindAutoSave(modalId, restoreKey) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        const inputs = modal.querySelectorAll('input,textarea,select');

        const saveHandler = () => {
            const data = this.getModalData(modalId);
            saveModalState(restoreKey, modalId, data);
        };

        inputs.forEach((i) => i.addEventListener('input', saveHandler));

        modal.__modalManagerSaveHandler = saveHandler;
    }

    /**
     * Removes autosave handler.
     *
     * @param {string} modalId
     * @returns {void}
     */
    unbindAutoSave(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal || !modal.__modalManagerSaveHandler) return;

        modal.querySelectorAll('input,textarea,select').forEach((i) =>
            i.removeEventListener('input', modal.__modalManagerSaveHandler)
        );

        delete modal.__modalManagerSaveHandler;
    }

    /**
     * Displays a confirm modal message.
     *
     * @param {Object} options
     * @param {string} options.message
     * @param {string} [options.acceptText="OK"]
     * @param {string} [options.declineText="Cancelar"]
     * @returns {Promise<boolean>}
     */
    async showModalMessage({ message, acceptText = 'OK', declineText = 'Cancelar' } = {}) {
        return new Promise(async (resolve) => {
            const modalId = 'modal-message';
            const url = route('modals.message');

            await this.loadModalContent(url, modalId);

            const modal = this.activeModals.get(modalId);
            if (!modal) return resolve(false);

            configureModalMessage(modal, { message, acceptText, declineText });
            this.bindModalMessageEvents(modalId, resolve);
        });
    }

    /**
     * Binds message modal events.
     *
     * @param {string} modalId
     * @param {function(boolean): void} resolve
     * @returns {void}
     */
    bindModalMessageEvents(modalId, resolve) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        bindModalMessageEvents(
            modal,
            (id) => this.removeModal(id),
            resolve
        );
    }

    /**
     * Binds a submit handler to a modal button.
     *
     * @param {Object} options
     * @param {string} options.modalId
     * @param {string} options.buttonId
     * @param {function(Object): Promise<*>} options.onSubmit
     * @param {function(*): void} [options.onSuccess]
     * @param {function(*): void} [options.onError]
     * @returns {void}
     */
    bindFormSubmit({ modalId, buttonId, onSubmit, onSuccess, onError }) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        const btn = modal.querySelector(`#${buttonId}`);
        if (!btn) return;

        btn.addEventListener('click', async () => {
            const data = this.getModalData(modalId);
            btn.disabled = true;

            try {
                const res = await onSubmit(data);

                let ok = true;

                if (res && typeof res === 'object') {
                    if (typeof res.ok === 'boolean') ok = res.ok;
                    else if (typeof res.status === 'number') ok = res.status >= 200 && res.status < 300;
                }

                if (!ok) {
                    const payload = res?.data ?? res?.error ?? res?.message ?? res;
                    if (onError) onError(payload);
                    return;
                }

                if (onSuccess) onSuccess(res);
            } catch (err) {
                if (onError) onError(err);
            } finally {
                btn.disabled = false;
            }
        });
    }
}
