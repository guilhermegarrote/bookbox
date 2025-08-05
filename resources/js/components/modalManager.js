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
        } catch (err) {
            console.error(err);
            throw err;
        }
    }

    insertModalHtml(html, modalId) {
        document.querySelectorAll('.modal-overlay').forEach(el => el.remove());

        const overlay = document.createElement('div');
        overlay.id = 'modalOverlay';
        overlay.classList.add('modal-overlay', 'hidden');
        overlay.setAttribute('aria-hidden', 'true');
        overlay.innerHTML = html;

        document.body.appendChild(overlay);

        const modalWrapper = overlay.querySelector(`#${modalId}`);
        if (!modalWrapper) {
            console.warn(`Modal com ID ${modalId} não encontrado no HTML carregado`);
            return;
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
            button.addEventListener('click', () => this.hideModal(modalId), { once: true });
        });

        const overlay = modal.closest('.modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) this.hideModal(modalId);
            }, { once: true });
        }

        const escListener = (e) => {
            if (e.key === 'Escape') {
                this.hideModal(modalId);
                document.removeEventListener('keydown', escListener);
            }
        };
        document.addEventListener('keydown', escListener);
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
