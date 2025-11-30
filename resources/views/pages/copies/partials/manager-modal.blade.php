<style>
    :root {
        --scroll-size: 6px;
        --scroll-radius: 6px;
        --scroll-track: var(--color-base-gray-454);
        --scroll-thumb-color: #ffffffff;
        --scroll-thumb-pattern: none;
    }

    .manager-copies-wrapper {
        overflow: auto;
        max-width: 100%;
        max-height: 240px;
        border: 1px solid #ccc;
        border-radius: 6px;
        scrollbar-width: thin;
        scrollbar-color: var(--scroll-thumb-color) var(--scroll-track);
    }

    .manager-copies-wrapper::-webkit-scrollbar {
        width: var(--scroll-size);
        height: var(--scroll-size);
    }

    .manager-copies-wrapper::-webkit-scrollbar-track {
        background: var(--scroll-track);
        border-radius: var(--scroll-radius);
    }

    .manager-copies-wrapper::-webkit-scrollbar-thumb {
        background-color: var(--scroll-thumb-color);
        border-radius: var(--scroll-radius);
        border: 2px solid var(--scroll-track);
    }

    .manager-copies-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: var(--scroll-thumb-color);
        filter: brightness(0.9);
    }

    .btn-trash {
        background-color: transparent;
        width: 30px;
        height: 30px;
        padding: 0;
    }

    .btn-trash svg {
        width: 25px;
        height: 25px;
        display: block;
    }

    .btn-trash svg path {
        fill: var(--color-base-gray-dark);
        transition: fill 0.3s ease;
    }

    .btn-trash:hover svg path {
        fill: #500d0d;
    }

    .status-active,
    .status-blocked {
        transition: color 0.3s ease;
    }

    .data-table tbody tr:hover .status-active {
        color: #28a745;
    }

    .data-table tbody tr:hover .status-blocked {
        color: #dc3545;
    }
</style>

<x-modals.modal id="copyManagerModal" title="Gerenciar Exemplares">
    <x-slot name="content">
        <div>
            <h3 style="font-size: 1.4rem; font-weight: normal; margin-bottom: 5px; color:#4d4b4b; ">
                {{ $book->title }}
            </h3>
            <h2 style="font-size: 1rem; font-weight: normal; margin-bottom: 15px; color:#4d4b4b; ">
                ISBN: {{ $book->isbn }}
            </h2>

            <div class="manager-copies-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Exemplar</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            <tr>
                                <td>{{ $copy->number }}</td>
                                <td class="{{ $copy->available === 1 ? 'status-active' : 'status-blocked' }}">
                                    {{ $copy->available === 1 ? 'Disponível' : 'Indisponível' }}
                                </td>
                                <td>
                                    <button data-id="{{ $copy->id }}" class="btn-trash" title="Excluir exemplar">
                                        <x-icons.icon name="trash" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu de exemplares">Fechar</button>
        <button type="button" class="modal-button" id="open-add-modal" title="Adicionar exemplares">Adicionar</button>
    </x-slot name="footer">
</x-modals.modal>
