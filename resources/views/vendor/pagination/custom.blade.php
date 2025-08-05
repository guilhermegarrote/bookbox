@php
    $perPageCurrent = request('perPage', 10);
@endphp

<div class="pagination-form" id="paginationForm">
    <label for="perPage">Itens por página:</label>

    <select name="perPage" id="perPage" aria-label="Selecionar quantidade de itens por página">
        @foreach ([10, 15, 20, 25, 50, 100, 250] as $size)
            <option value="{{ $size }}" {{ $perPageCurrent == $size ? 'selected' : '' }}>
                {{ $size }}
            </option>
        @endforeach
    </select>

    <div class="info-text" aria-live="polite" aria-atomic="true">
        {{ $paginator->firstItem() ?? 0 }} – {{ $paginator->lastItem() ?? 0 }} de {{ $paginator->total() }} itens
    </div>

    <nav aria-label="Navegação de páginas">
        {{-- Primeira página --}}
        <button type="button" class="page-link" data-page="1" aria-label="Primeira página"
            {{ $paginator->onFirstPage() ? 'disabled' : '' }}>
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor">
                <path d="M18 6L12 12L18 18L16.5 19.5L9 12L16.5 4.5L18 6Z" />
                <path d="M12 6L6 12L12 18L10.5 19.5L3 12L10.5 4.5L12 6Z" />
            </svg>
        </button>

        {{-- Página anterior --}}
        <button type="button" class="page-link" data-page="{{ $paginator->currentPage() - 1 }}"
            aria-label="Página anterior" {{ $paginator->onFirstPage() ? 'disabled' : '' }}>
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor">
                <path d="M15 18L9 12L15 6L13.5 4.5L6 12L13.5 19.5L15 18Z" />
            </svg>
        </button>

        {{-- Input de página --}}
        <input type="number" class="no-spin" min="1" max="{{ $paginator->lastPage() }}"
            value="{{ $paginator->currentPage() }}" id="pageInput" aria-label="Número da página atual" />

        <span aria-live="polite">de {{ $paginator->lastPage() }}</span>

        {{-- Próxima página --}}
        <button type="button" class="page-link" data-page="{{ $paginator->currentPage() + 1 }}"
            aria-label="Próxima página" {{ !$paginator->hasMorePages() ? 'disabled' : '' }}>
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor">
                <path d="M9 6L15 12L9 18L10.5 19.5L18 12L10.5 4.5L9 6Z" />
            </svg>
        </button>

        {{-- Última página --}}
        <button type="button" class="page-link" data-page="{{ $paginator->lastPage() }}" aria-label="Última página"
            {{ !$paginator->hasMorePages() ? 'disabled' : '' }}>
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor">
                <path d="M6 6L12 12L6 18L7.5 19.5L15 12L7.5 4.5L6 6Z" />
                <path d="M12 6L18 12L12 18L13.5 19.5L21 12L13.5 4.5L12 6Z" />
            </svg>
        </button>
    </nav>
</div>
