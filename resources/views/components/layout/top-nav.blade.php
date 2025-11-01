<header>
    <nav>
        <div class="top-nav">
            <div class="nav-wrapper">
                <div class="logo" title="Página inicial">
                    {!! file_get_contents(public_path('images/logo/logotype.svg')) !!}
                </div>

                <div class="nav-container">
                    <div class="search-group">
                        <input type="text" class="search-input" placeholder="Pesquisar" id="top-nav-search-input"
                            title="Pesquisar" @if (empty($filterUrl)) style="border-radius: 10px;" @endif>

                        <button class="btn-dark filter-button" id="btn-filter" data-filter-url="{{ $filterUrl ?? '' }}"
                            @if (empty($filterUrl)) style="display:none;" @endif aria-label="Filtro"
                            title="Abrir filtros">
                            <x-icons.icon name="filter" />
                        </button>

                        <div id="popup-filter" class="filter-popup"></div>
                    </div>

                    <div class="action-buttons">
                        <a class="nav-button" href="{{ route('loans.view') }}"
                            title="Gerenciar empréstimos"><span>Empréstimos</span></a>
                        <a class="nav-button" href="{{ route('books.view') }}"
                            title="Gerenciar livros"><span>Livros</span></a>
                        <a class="nav-button" href="{{ route('students.view') }}"
                            title="Gerenciar alunos"><span>Alunos</span></a>
                    </div>

                    <button class="btn-dark settings-button" id="open-settings" aria-label="Configurações"
                        title="Abrir configurações">&#x22EE;</button>
                </div>
            </div>
        </div>
    </nav>
</header>

@push('scripts')
    @vite('resources/js/components/ui/top-nav.js')
@endpush
