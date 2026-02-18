import ModalManager from '@js/components/ui/modal-manager/modal-manager';
import { handleDeleteGenre } from '../genres/modals/delete';
import { openUpdateModal as openUpdateGenreModal } from '../genres/modals/update';
import { openCreateModal as openCreateGenreModal } from '../genres/modals/create';
import { handleDeleteSchoolClass } from '../school-classes/modals/delete';
import { openUpdateModal as openUpdateSchoolClassModal } from '../school-classes/modals/update';
import { openCreateModal as openCreateSchoolClassModal } from '../school-classes/modals/create';
import { openUpdateModal as openUpdateUserModal } from '../users/modals/update';
import { openCreateModal as openCreateUserModal } from '../users/modals/create';
import { openDeleteModal as openDeleteUserModal } from '../users/modals/delete';
import { updateSettings } from '@js/api/settings/update';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';

const modalManager = new ModalManager();
let globalActionsAttached = false;

/**
 * Initializes the settings navigation buttons.
 * Handles switching between different settings pages
 * and attaches global actions if not already attached.
 */
export function initSettingsNavigation() {
    const navButtons = document.querySelectorAll('.settings-nav-btn');

    navButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const page = btn.dataset.page;

            document.querySelectorAll('.settings-page').forEach(sec => sec.classList.remove('active'));
            navButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const section = document.getElementById(page);
            if (!section) return;

            section.classList.add('active');
            await loadSettingsPage(page);
        });
    });

    const activeBtn = document.querySelector('.settings-nav-btn.active');
    if (activeBtn) {
        loadSettingsPage(activeBtn.dataset.page);
    }

    if (!globalActionsAttached) {
        attachGlobalActions();
        globalActionsAttached = true;
    }
}

/**
 * Loads a specific settings page via AJAX and injects its HTML.
 * @param {string} page - The page identifier to load.
 */
async function loadSettingsPage(page) {
    try {
        const res = await fetch(`/settings/${page}`);
        if (!res.ok) throw new Error(`Erro ao carregar página: ${res.status}`);

        const html = await res.text();
        const section = document.getElementById(page);
        if (!section) return;

        document.querySelectorAll('.settings-page').forEach(sec => {
            sec.id === page ? sec.classList.add('active') : (sec.classList.remove('active'), sec.innerHTML = '');
        });

        section.innerHTML = html;

        if (page === 'config') {
            initConfigSection();
        }

        const input = section.querySelector('#item-search');
        const list = section.querySelector('.settings-card-list');

        if (input && list) {
            let debounceTimer;

            input.addEventListener('input', () => {
                const value = input.value.trim();
                const entity = input.dataset.entity;
                if (!entity) return;

                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(async () => {
                    const url = value === ''
                        ? `/settings/${entity}`
                        : `/settings/${entity}?search=${encodeURIComponent(value)}`;

                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) return;

                    list.innerHTML = await res.text();
                }, 400);
            });
        }
    } catch (err) {
        console.error(err);
    }
}

/**
 * Attaches global click listeners for edit/create/delete actions
 * across genres, school classes, and users sections.
 */
function attachGlobalActions() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-list-config');
        if (btn) handleCardAction(btn);

        const createBtn = e.target.closest('#open-create-modal');
        if (createBtn) handleCreateAction(createBtn);
    });
}

/**
 * Handles edit/delete actions for cards in different sections.
 * @param {HTMLElement} btn - The button that triggered the action.
 */
function handleCardAction(btn) {
    const action = btn.dataset.action;
    const card = btn.closest('.settings-card');
    if (!card) return;
    const id = card.dataset.id;

    if (btn.closest('#genres')) {
        if (action === 'edit') openUpdateGenreModal(id, modalManager, () => loadSettingsPage('genres'));
        if (action === 'delete') handleDeleteGenre(id, modalManager, () => loadSettingsPage('genres'));
    }

    if (btn.closest('#school-classes')) {
        if (action === 'edit') openUpdateSchoolClassModal(id, modalManager, () => loadSettingsPage('school-classes'));
        if (action === 'delete') handleDeleteSchoolClass(id, modalManager, () => loadSettingsPage('school-classes'));
    }

    if (btn.closest('#users')) {
        if (action === 'edit') openUpdateUserModal(id, modalManager, () => loadSettingsPage('users'));
        if (action === 'delete') openDeleteUserModal(id, modalManager, () => loadSettingsPage('users'));
    }
}

/**
 * Handles create actions for different sections.
 * @param {HTMLElement} createBtn - The create button that was clicked.
 */
function handleCreateAction(createBtn) {
    if (createBtn.closest('#genres')) openCreateGenreModal(modalManager, () => loadSettingsPage('genres'));
    if (createBtn.closest('#school-classes')) openCreateSchoolClassModal(modalManager, () => loadSettingsPage('school-classes'));
    if (createBtn.closest('#users')) openCreateUserModal(modalManager, () => loadSettingsPage('users'));
}

/**
 * Initializes the configuration section, tracks changes,
 * and handles bulk saving via API.
 */
function initConfigSection() {
    const section = document.getElementById('config');
    if (!section) return;

    const inputs = section.querySelectorAll('input');
    const actions = document.getElementById('config-actions');
    const cancelBtn = document.getElementById('cancel-config');
    const saveBtn = document.getElementById('save-config');

    /** @type {Object.<string, string|number>} */
    const originalValues = {};

    inputs.forEach(input => {
        originalValues[input.name] = input.value;

        input.addEventListener('input', () => {
            const changed = [...inputs].some(i => i.value !== originalValues[i.name]);
            actions.classList.toggle('hidden', !changed);
        });
    });

    cancelBtn.addEventListener('click', () => {
        inputs.forEach(input => input.value = originalValues[input.name]);
        actions.classList.add('hidden');
    });

    saveBtn.addEventListener('click', async () => {
        const settings = {};
        inputs.forEach(input => {
            settings[input.name] = input.type === 'number' ? parseInt(input.value, 10) : input.value;
        });

        saveBtn.disabled = true;

        try {
            const { ok, data: responseData } = await updateSettings(settings);

            if (ok) {
                Object.keys(settings).forEach(key => (originalValues[key] = settings[key]));
                notifySuccess('Configurações salvas com sucesso!');
                actions.classList.add('hidden');
            } else {
                responseData.errors ? showErrors(responseData.errors) : notifyError(responseData || 'Erro ao salvar configurações');
            }
        } catch (err) {
            console.error(err);
            notifyError('Erro técnico ao tentar atualizar configurações. Tente novamente.');
        } finally {
            saveBtn.disabled = false;
        }
    });
}
