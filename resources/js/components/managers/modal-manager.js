export default class ModalManager {
    constructor() {
        this.activeModals = new Map();
        this._escListener = null;
    }

    async loadModalContent(url, modalId, { onInit } = {}) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Erro ao carregar modal');

            const html = await response.text();
            this.insertModalHtml(html, modalId);
            this.bindCloseEvents(modalId);
            this.showModal(modalId);
            this.updateFloatingLabels(modalId);

            if (typeof onInit === 'function') onInit(this.activeModals.get(modalId));
        } catch (err) {
            console.error(err);
            throw err;
        }
    }

    insertModalHtml(html, modalId) {
        this.closeAll({ remove: true });

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
            throw new Error(`Modal com ID ${modalId} não encontrado no HTML carregado`);
        }

        overlay.classList.remove('hidden');
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');

        this.activeModals.set(modalId, modalWrapper);
    }

    showModal(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

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

    hideModal(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

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

    removeModal(modalId) {
        const overlay = document.getElementById(`${modalId}-overlay`);
        if (overlay) overlay.remove();
        this.activeModals.delete(modalId);
    }

    closeAll({ remove = true } = {}) {
        for (const modalId of this.activeModals.keys()) {
            remove ? this.removeModal(modalId) : this.hideModal(modalId);
        }
    }

    getModalData(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return {};

        const data = {};
        const elements = modal.querySelectorAll('input, select, textarea');

        elements.forEach(el => {
            if (!el.id) return;
            let value = el.value?.trim() || '';

            data[el.id] = value;
        });

        return data;
    }

    toggleFields(fields, enable) {
        fields.forEach(f => {
            if (f.tagName === 'INPUT') {
                enable ? f.removeAttribute('readonly') : f.setAttribute('readonly', true);
            }
            if (f.tagName === 'SELECT') {
                enable ? f.removeAttribute('disabled') : f.setAttribute('disabled', true);
            }
        });
    }

    bindCloseEvents(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        const closeButtons = modal.querySelectorAll('.modal-button#btn-close, #modal-message-decline');
        closeButtons.forEach(btn => btn.addEventListener('click', () => this.removeModal(modalId), { once: true }));

        const overlay = modal.closest('.modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) this.removeModal(modalId);
            }, { once: true });
        }

        if (!this._escListener) {
            this._escListener = (e) => {
                if (e.key === 'Escape') {
                    const lastModalId = Array.from(this.activeModals.keys()).pop();
                    if (lastModalId) this.removeModal(lastModalId);
                }
            };
            document.addEventListener('keydown', this._escListener);
        }
    }

    updateFloatingLabels(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        modal.querySelectorAll('.form-input').forEach(input => {
            const toggleHasValue = () => input.classList.toggle("has-value", input.value.trim() !== "");
            toggleHasValue();
            input.addEventListener("input", toggleHasValue);
        });
    }

    async showModalMessage({
        message,
        acceptText = "OK",
        declineText = "Cancelar"
    } = {}) {
        return new Promise(async (resolve) => {
            const modalId = 'modal-message';
            const url = route('modals.modalMessage');

            await this.loadModalContent(url, modalId);
            const modal = this.activeModals.get(modalId);
            if (!modal) return resolve(false);

            this.configureModalMessage(modal, { message, acceptText, declineText });
            this.bindModalMessageEvents(modal, resolve);
        });
    }

    configureModalMessage(modal, { message, acceptText, declineText }) {
        const msgEl = modal.querySelector("#modal-message-text");
        const acceptBtn = modal.querySelector("#modal-accept");
        const declineBtn = modal.querySelector("#modal-decline");

        msgEl.textContent = message;
        acceptBtn.textContent = acceptText;
        declineBtn.textContent = declineText;
    }

    bindModalMessageEvents(modal, resolve) {
        const acceptBtn = modal.querySelector("#modal-accept");
        const declineBtn = modal.querySelector("#modal-decline");
        const overlay = modal.closest('.modal-overlay');

        acceptBtn.addEventListener("click", () => {
            this.remove(modal.id);
            resolve(true);
        }, { once: true });

        declineBtn.addEventListener("click", () => {
            this.remove(modal.id);
            resolve(false);
        }, { once: true });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                this.remove(modal.id);
                resolve(false);
            }
        }, { once: true });
    }

    bindFormSubmit({ modalId, buttonId, onSubmit, onSuccess, onError }) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        const btn = modal.querySelector(`#${buttonId}`);
        if (!btn) return;

        btn.addEventListener('click', async () => {
            const data = this.getModalData(modalId);
            btn.disabled = true;

            try {
                await onSubmit(data);
                if (onSuccess) onSuccess();
            } catch (err) {
                if (onError) onError(err);
            } finally {
                btn.disabled = false;
            }
        });
    }
}
