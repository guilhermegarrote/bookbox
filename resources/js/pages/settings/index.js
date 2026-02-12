import ModalManager from '@js/components/ui/modal-manager/modal-manager';
import { handleDeleteGenre } from '../genres/delete';
import { openUpdateModal as openUpdateGenreModal } from '../genres/update';
import { openCreateModal as openCreateGenreModal } from '../genres/create';
import { openUpdateModal as openUpdateSchoolClassModal } from '../school-classes/update';
import { openCreateModal as openCreateSchoolClassModal } from '../school-classes/create';

const modalManager = new ModalManager();
let globalActionsAttached = false;

export function initSettingsNavigation() {
    const navButtons = document.querySelectorAll('.settings-nav-btn');

    navButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const page = btn.dataset.page;

            document
                .querySelectorAll('.settings-page')
                .forEach(sec => sec.classList.remove('active'));

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

async function loadSettingsPage(page) {
    try {
        const res = await fetch(`/settings/${page}`);
        if (!res.ok) throw new Error(`Erro ao carregar página: ${res.status}`);

        const html = await res.text();
        const section = document.getElementById(page);
        if (!section) return;

        const allSections = document.querySelectorAll('.settings-page');

        allSections.forEach(sec => {
            if (sec.id === page) {
                sec.classList.add('active');
            } else {
                sec.classList.remove('active');
                sec.innerHTML = '';
            }
        });

        section.innerHTML = html;

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

                    const res = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!res.ok) return;

                    list.innerHTML = await res.text();
                }, 400);
            });
        }
    } catch (err) {
        console.error(err);
    }
}

function attachConfigActions() {
    const section = document.getElementById('config');
    if (!section) return;

    section.querySelectorAll('input').forEach(input => {
        input.addEventListener('change', () => {
            console.log(`Config alterada: ${input.name} = ${input.value}`);
        });
    });
}

function attachGlobalActions() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-list-config');
        if (btn) {
            const action = btn.dataset.action;
            const card = btn.closest('.settings-card');
            if (!card) return;

            const id = card.dataset.id;

            if (btn.closest('#genres')) {
                if (action === 'edit') {
                    openUpdateGenreModal(id, modalManager, () => loadSettingsPage('genres'));
                }

                if (action === 'delete') {
                    handleDeleteGenre(id, modalManager, () => loadSettingsPage('genres'));
                }
            }

            if (btn.closest('#school-classes')) {
                if (action === 'edit') {
                    openUpdateSchoolClassModal(id, modalManager, () => loadSettingsPage('school-classes'));
                }
            }
        }

        const createBtn = e.target.closest('#open-create-modal');
        if (createBtn) {
            if (createBtn.closest('#genres')) {
                openCreateGenreModal(
                    modalManager,
                    () => loadSettingsPage('genres')
                );
            }
            if (createBtn.closest('#school-classes')) {
                openCreateSchoolClassModal(
                    modalManager,
                    () => loadSettingsPage('school-classes')
                );
            }
        }
    });
}
