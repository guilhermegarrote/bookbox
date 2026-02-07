import modalManager from '@js/components/ui/modal-manager/modal-manager';

document.addEventListener('DOMContentLoaded', () => {
    initSettingsNavigation();
});

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
            attachPageActions(page);
        });
    });

    const defaultBtn = document.querySelector('.settings-nav-btn[data-page="users"]');
    if (defaultBtn) defaultBtn.click();
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

        input.addEventListener('input', () => {
            const value = input.value.trim();
            const entity = input.dataset.entity;

            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(async () => {
                if (!entity) return;

                if (value === '') {
                    const res = await fetch(
                        `/settings/${entity}`,
                        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                    );

                    if (res.ok) {
                        list.innerHTML = await res.text();
                    }
                    return;
                }

                const res = await fetch(
                    `/settings/${entity}?search=${encodeURIComponent(value)}`,
                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                );

                if (!res.ok) return;
                list.innerHTML = await res.text();
            }, 400);
        });
    } catch (err) {
        console.error(err);
        modalManager.showModalMessage({
            message: `Erro ao carregar a aba "${page}"`,
            acceptText: "Fechar"
        });
    }
}

function attachPageActions(page) {
    switch (page) {
        case 'users':
            attachUserActions();
            break;
        case 'classes':
            attachClassActions();
            break;
        case 'genres':
            attachGenreActions();
            break;
        case 'config':
            attachConfigActions();
            break;
    }
}

function attachUserActions() {
    document.querySelectorAll('[data-edit-user]').forEach(btn =>
        btn.addEventListener('click', () => openUserEditModal(btn.dataset.editUser))
    );
    document.querySelectorAll('[data-delete-user]').forEach(btn =>
        btn.addEventListener('click', () => deleteUser(btn.dataset.deleteUser))
    );
}

async function openUserEditModal(userId) {
    try {
        await modalManager.loadModalContent(`/user/${userId}/update-modal`, 'userEditModal');
    } catch (err) {
        console.error('Erro ao abrir modal de edição:', err);
    }
}

async function deleteUser(userId, reloadPage = true) {
    const confirmed = await modalManager.showModalMessage({
        message: "Deseja realmente excluir este usuário?",
        acceptText: "Sim",
        declineText: "Cancelar"
    });
    if (!confirmed) return;

    try {
        const res = await fetch(`/user/${userId}`, {
            method: "DELETE",
            headers: { "X-CSRF-TOKEN": window.csrf, "Accept": "application/json" }
        });
        if (!res.ok) throw new Error("Falha ao excluir usuário");
        if (reloadPage) await loadSettingsPage('users');
    } catch (err) {
        console.error(err);
        modalManager.showModalMessage({ message: "Erro ao excluir usuário", acceptText: "Fechar" });
    }
}

function openCreateUserModal() {
    modalManager.loadModalContent(`/user/create-modal`, 'userCreateModal');
}

function attachClassActions() {
    document.querySelectorAll('.card-class').forEach(card =>
        card.addEventListener('click', () => console.log('Turma clicada:', card.querySelector('strong').textContent))
    );
}

function attachGenreActions() {
    document.querySelectorAll('#submit-delete').forEach(btn =>
        btn.addEventListener('click', () => openUserEditModal(btn.dataset.editUser))
    );
    document.querySelectorAll('[data-delete-user]').forEach(btn =>
        btn.addEventListener('click', () => deleteUser(btn.dataset.deleteUser))
    );
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
