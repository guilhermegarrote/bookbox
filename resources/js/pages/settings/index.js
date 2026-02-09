import ModalManager from '@js/components/ui/modal-manager/modal-manager';
import { handleDeleteGenre } from '../genres/delete';
import { openUpdateModal as openUpdateGenreModal } from '../genres/update';
import { openCreateModal as openCreateGenreModal } from '../genres/create';

const modalManager = new ModalManager();

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
        const page = activeBtn.dataset.page;
        loadSettingsPage(page);
    }
}

async function loadSettingsPage(page) {
    try {
        const res = await fetch(`/settings/${page}`);
        if (!res.ok) throw new Error(`Erro ao carregar página: ${res.status}`);

        const html = await res.text();
        const section = document.getElementById(page);
        if (!section) throw new Error(`Seção ${page} não encontrada`);

        section.innerHTML = html;

        const input = section.querySelector('#item-search');
        const list = section.querySelector('.settings-card-list');

        if (!input || !list) return;

        let debounceTimer = null;

        attachPageActions(page);

        input.addEventListener('input', () => {
            const value = input.value.trim();
            const entity = input.dataset.entity;

            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(async () => {
                if (!entity) return;

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
    } catch (err) {
        console.error(err);
    }
}

function attachPageActions(page) {
    switch (page) {
        case 'config':
            attachConfigActions();
            break;
        case 'genres':
            attachGenreActions();
            break;
        /* case 'classes':
             attachClassActions();
             break;
         case 'users':
             attachUserActions();
             break;*/
    }
}

function attachConfigActions() {
    const section = document.getElementById('config');
    if (!section) return;
    section.querySelectorAll('input').forEach(input =>
        input.addEventListener('change', () =>
            console.log(`Config alterada: ${input.name} = ${input.value}`)
        )
    );
}

function attachGenreActions() {
    document.querySelectorAll(".btn-list-config").forEach(btn => {
        btn.addEventListener("click", () => {
            const action = btn.dataset.action;
            const genreId = btn.closest(".settings-card").dataset.id;

            if (action === "edit") {
                openUpdateGenreModal(genreId, modalManager, () => loadSettingsPage('genres'));
            }

            if (action === "delete") {
                handleDeleteGenre(genreId, modalManager, () => loadSettingsPage('genres'));
            }
        });
    });

    document.getElementById('open-create-modal')?.addEventListener('click', () => {
        openCreateGenreModal(
            modalManager,
            () => loadSettingsPage('genres')
        );
    });
}

