export default class ModalManager {
    constructor() {
        this.activeModals = new Map();
    }

    async loadModalContent(url, modalId) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Erro ao carregar modal');
            const html = await response.text();

            this.insertModalHtml(html, modalId);
            this.bindCloseEvents(modalId);
            this.showModal(modalId);
            this.updateFloatingLabels(modalId);

            return Promise.resolve();
        } catch (err) {
            console.error(err);
            return Promise.reject(err);
        }
    }

    insertModalHtml(html, modalId) {
        let modalWrapper = document.getElementById(modalId);
        if (!modalWrapper) {
            modalWrapper = document.createElement('div');
            modalWrapper.id = modalId;
            document.body.appendChild(modalWrapper);
        }
        modalWrapper.innerHTML = html;
        modalWrapper.classList.remove('hidden');
        modalWrapper.classList.add('modal-window');
        modalWrapper.setAttribute('aria-hidden', 'false');
        this.activeModals.set(modalId, modalWrapper);
        document.body.classList.add('modal-open');
    }

    showModal(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        const overlay = document.getElementById('modalOverlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('active');
            overlay.setAttribute('aria-hidden', 'false');
        }

        modal.classList.remove('hidden');
        modal.removeAttribute('inert');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }

    hideModal(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) return;

        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        modal.setAttribute('inert', '');

        const overlay = document.getElementById('modalOverlay');
        if (overlay) {
            overlay.classList.remove('active');
            overlay.classList.add('hidden');
            overlay.setAttribute('aria-hidden', 'true');
        }

        document.body.classList.remove('modal-open');
    }

    bindCloseEvents(modalId) {
        const modal = this.activeModals.get(modalId);
        if (!modal) {
            console.warn(`Modal ${modalId} não encontrado na hora de bindCloseEvents`);
            return;
        }

        const closeButtons = modal.querySelectorAll('.modal-close-btn, .modal-button#btn-close');

        if (closeButtons.length === 0) {
            console.warn(`Nenhum botão de fechar encontrado dentro de ${modalId}`);
        }

        closeButtons.forEach(button => {
            button.addEventListener('click', () => this.hideModal(modalId));
        });
    }

    updateFloatingLabels(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.querySelectorAll('.form-input').forEach(input => {
            if (input.value.trim() !== '') {
                input.classList.add('has-value');
            } else {
                input.classList.remove('has-value');
            }
        });
    }
}
