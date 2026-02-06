export default class ModalManager {
    constructor() {
        this.activeModals = new Map();
        this._escListener = null;
    }

    saveModalState(key, modalId) {
        const data = this.getModalData(modalId);
        try {
            sessionStorage.setItem(key, JSON.stringify({ modalId, data }));
        } catch (e) { }
    }

    getSavedModalState(key) {
        try {
            return JSON.parse(sessionStorage.getItem(key));
        } catch {
            return null;
        }
    }

    clearSavedModalState(key) {
        try { sessionStorage.removeItem(key); } catch (e) { }
    }

    dispatchSavedModalEvent(key, eventName = 'reopenModal') {
        const saved = this.getSavedModalState(key);
        if (saved) {
            window.dispatchEvent(new CustomEvent(eventName, { detail: saved }));
            this.clearSavedModalState(key);
        }
    }

    async loadModalContent(url, modalId, { onInit, restoreKey = null, initialData = null } = {}) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Erro ao carregar modal');

            const html = await response.text();
            await this.insertModalHtml(html, modalId);
            this.bindCloseEvents(modalId);
            this.showModal(modalId);
            this.updateFloatingLabels(modalId);

            const restoredData = restoreKey ? (this.getSavedModalState(restoreKey)?.data ?? null) : null;
            let saved;
            if (initialData && restoredData) {
                saved = { ...initialData, ...restoredData };
            } else {
                saved = initialData ?? restoredData;
            }

            if (saved) {
                setTimeout(() => this.restoreFormData(modalId, saved), 0);
            }

            if (restoreKey) {
                const modal = this.activeModals.get(modalId);
                if (modal) {
                    const inputs = modal.querySelectorAll('input,textarea,select');
                    const saveHandler = () => {
                        const data = this.getModalData(modalId);
                        try { sessionStorage.setItem(restoreKey, JSON.stringify({ modalId, data })); } catch (e) { }
                    };
                    inputs.forEach(i => i.addEventListener('input', saveHandler));
                    modal.__modalManagerSaveHandler = saveHandler;
                }
            }

            if (typeof onInit === 'function') onInit(this.activeModals.get(modalId));
        } catch (err) {
            console.error(err);
            throw err;
        }
    }

    async insertModalHtml(html, modalId) {
        await this.closeAll({ remove: true });

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

    async animateClose(modalId) {
        return new Promise((resolve) => {
            const modal = this.activeModals.get(modalId);
            if (!modal) return resolve();

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

    async removeModal(modalId, { pendingKey, eventName } = {}) {
        const overlay = document.getElementById(`${modalId}-overlay`);
        if (!overlay) return;

        await this.animateClose(modalId);

        const modal = this.activeModals.get(modalId);
        if (modal && modal.__modalManagerSaveHandler) {
            modal.querySelectorAll('input,textarea,select').forEach(i =>
                i.removeEventListener('input', modal.__modalManagerSaveHandler)
            );
            delete modal.__modalManagerSaveHandler;
        }

        overlay.remove();
        this.activeModals.delete(modalId);

        if (pendingKey) {
            const saved = this.getSavedModalState(pendingKey);
            if (saved) {
                window.dispatchEvent(new CustomEvent(eventName || 'reopenModal', { detail: saved }));
                this.clearSavedModalState(pendingKey);
            }
        }
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

    async closeAll({ remove = true } = {}) {
        for (const modalId of [...this.activeModals.keys()]) {
            if (remove) {
                await this.removeModal(modalId);
            } else {
                this.hideModal(modalId);
            }
        }
    }

    getModalData(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return {};

        const data = {};
        const elements = modal.querySelectorAll('input, select, textarea');

        elements.forEach(el => {
            if (!el.id) return;
            if (el.disabled) return;
            if (el.hasAttribute('readonly')) return;

            let value;
            if (el.tagName === 'INPUT') {
                const type = (el.getAttribute('type') || '').toLowerCase();
                if (type === 'checkbox') {
                    value = el.checked;
                } else if (type === 'radio') {
                    if (!el.checked) return;
                    value = el.value ?? '';
                } else {
                    value = el.value?.trim() || '';
                }
            } else if (el.tagName === 'TEXTAREA') {
                value = el.value?.trim() || '';
            } else if (el.tagName === 'SELECT') {
                if (el.multiple) {
                    value = Array.from(el.selectedOptions).map(o => o.value);
                } else {
                    value = el.value ?? '';
                }
            } else {
                value = el.value?.trim() || '';
            }

            data[el.id] = value;
        });

        return data;
    }

    restoreFormData(modalId, data = {}) {
        const modal = this.activeModals.get(modalId);
        if (!modal || !data) return;

        const callNativeSetter = (el, prop, value) => {
            try {
                const proto = Object.getPrototypeOf(el);
                const desc = Object.getOwnPropertyDescriptor(proto, prop) || Object.getOwnPropertyDescriptor(el, prop);
                const setter = desc && desc.set;
                if (setter) {
                    setter.call(el, value);
                } else {
                    el[prop] = value;
                }
            } catch (e) { el[prop] = value; }
        };

        const dispatch = (el, type) => {
            try {
                const ev = new Event(type, { bubbles: true });
                el.dispatchEvent(ev);
            } catch (e) {}
        };

        for (const [key, rawValue] of Object.entries(data)) {
            const el = modal.querySelector(`#${CSS.escape(key)}`);
            if (!el) continue;

            const value = rawValue;

            if (el.tagName === 'INPUT') {
                const type = (el.getAttribute('type') || '').toLowerCase();
                if (type === 'checkbox') {
                    const checked = (value === true || value === 'true' || value === '1' || value === 'on');
                    callNativeSetter(el, 'checked', checked);
                    dispatch(el, 'input');
                    dispatch(el, 'change');
                } else if (type === 'radio') {
                    const name = el.name;
                    if (name) {
                        const radios = modal.querySelectorAll(`input[type="radio"][name="${CSS.escape(name)}"]`);
                        radios.forEach(r => {
                            const should = (r.value == value);
                            callNativeSetter(r, 'checked', should);
                            if (should) { dispatch(r, 'input'); dispatch(r, 'change'); }
                        });
                    } else {
                        callNativeSetter(el, 'value', value);
                        dispatch(el, 'input'); dispatch(el, 'change');
                    }
                } else if (type === 'file') {
                    continue;
                } else {
                    try { el.focus?.(); } catch {}
                    callNativeSetter(el, 'value', value ?? '');
                    dispatch(el, 'input'); dispatch(el, 'change');
                    try { el.blur?.(); } catch {}
                }
            } else if (el.tagName === 'TEXTAREA') {
                try { el.focus?.(); } catch {}
                callNativeSetter(el, 'value', value ?? '');
                dispatch(el, 'input'); dispatch(el, 'change');
                try { el.blur?.(); } catch {}
            } else if (el.tagName === 'SELECT') {
                if (el.multiple) {
                    const vals = Array.isArray(value) ? value.map(v => (v ?? '').toString()) : (typeof value === 'string' ? value.split(',') : [(value ?? '').toString()]);
                    Array.from(el.options).forEach(opt => {
                        callNativeSetter(opt, 'selected', vals.includes(opt.value));
                    });
                } else {
                    callNativeSetter(el, 'value', value ?? '');
                }
                dispatch(el, 'input'); dispatch(el, 'change');
            } else {
                try { callNativeSetter(el, 'value', value ?? ''); dispatch(el, 'input'); dispatch(el, 'change'); } catch (e) {}
            }

            if (el.classList && el.classList.contains('form-input')) {
                const has = (el.type === 'checkbox' || el.type === 'radio') ? (el.checked) : ((el.value ?? '').toString().trim() !== '');
                el.classList.toggle('has-value', !!has);
            }
        }
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

        const closeButtons = modal.querySelectorAll('#btn-close, #modal-message-decline');
        closeButtons.forEach(btn =>
            btn.addEventListener('click', () => this.removeModal(modalId), { once: true })
        );

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
            const url = route('modals.message');

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

        acceptBtn.addEventListener("click", async () => {
            await this.removeModal(modal.id);
            resolve(true);
        }, { once: true });

        declineBtn.addEventListener("click", async () => {
            await this.removeModal(modal.id);
            resolve(false);
        }, { once: true });

        overlay.addEventListener('click', async (e) => {
            if (e.target === overlay) {
                await this.removeModal(modal.id);
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
                const res = await onSubmit(data);

                let ok = true;
                if (res && typeof res === 'object') {
                    if (typeof res.ok === 'boolean') {
                        ok = res.ok;
                    } else if (typeof res.status === 'number') {
                        ok = res.status >= 200 && res.status < 300;
                    }
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
