<div class="settings-container" id="settings-panel">
    <aside class="settings-sidebar">
        <nav>
            <button class="settings-nav-btn active" data-page="config">
                <x-icons.icon name="gear" /> Configurações
            </button>
            <button class="settings-nav-btn" data-page="genres">
                <x-icons.icon name="genre" /> Gerenciar Gêneros
            </button>
            <button class="settings-nav-btn" data-page="school-classes">
                <x-icons.icon name="chalkboard-teacher" /> Gerenciar Turmas
            </button>
            <button class="settings-nav-btn" data-page="users">
                <x-icons.icon name="users" /> Gerenciar Usuários
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
        <section id="config" class="settings-page active"></section>
        <section id="genres" class="settings-page"></section>
        <section id="school-classes" class="settings-page"></section>
        <section id="users" class="settings-page"></section>
    </main>
</div>
