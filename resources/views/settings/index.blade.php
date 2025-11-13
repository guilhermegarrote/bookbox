<div class="settings-container" id="settings-panel">
    <aside class="settings-sidebar">
        <nav>
            <button class="settings-nav-btn" data-page="genres">
                <x-icons.icon name="genre" /> Gerenciar Gêneros
            </button>
            <button class="settings-nav-btn" data-page="classes">
                <x-icons.icon name="chalkboard-teacher" />  Gerenciar Turmas
            </button>
            <button class="settings-nav-btn" data-page="users">
                <x-icons.icon name="users" /> Gerenciar Usuários
            </button>
            <button class="settings-nav-btn" data-page="config">
                <x-icons.icon name="gear" /> Configurações
            </button>
        </nav>

        <div class="settings-logout">
            <button class="settings-btn-close" id="btn-close">
                <x-icons.icon name="close" /> Fechar painel
            </button>
            <button class="settings-btn-logout" id="submit-logout">
                <x-icons.icon name="logout" /> Sair da conta
            </button>
        </div>
    </aside>

    <main class="settings-content">
        <div class="settings-header">
            <input type="text" placeholder="Pesquisar..." class="settings-search">
            <button class="settings-btn-add">
                <i class="fa-solid fa-plus"></i> Adicionar
            </button>
        </div>

        <section id="genres" class="settings-page active"></section>
        <section id="classes" class="settings-page"></section>
        <section id="users" class="settings-page"></section>
        <section id="config" class="settings-page"></section>

        <div class="settings-footer">
            <button class="settings-btn-finalize">
                <i class="fa-solid fa-check"></i> Finalizar
            </button>
        </div>
    </main>
</div>
