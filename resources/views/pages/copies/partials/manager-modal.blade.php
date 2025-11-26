<style>
    :root {
        --scroll-size: 6px;
        --scroll-radius: 6px;
        --scroll-track: var(--color-base-gray-454);
        ;
        --scroll-thumb-color: #ffffffff;
        --scroll-thumb-pattern: none;
    }

    .modal-wrapper {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 8px;
        padding: 8px 12px;
        border: 1px solid #cfcfcf;
    }

    .search-box input {
        width: 100%;
        border: none;
        outline: none;
        font-size: 14px;
    }

    .generate-table-wrapper {
        overflow: auto;
        max-width: 100%;
        max-height: 240px;
        border: 1px solid #ccc;
        border-radius: 6px;
        scrollbar-width: thin;
        scrollbar-color: var(--scroll-thumb-color) var(--scroll-track);
        background: #fff;
    }

    .generate-table-wrapper::-webkit-scrollbar {
        width: var(--scroll-size);
        height: var(--scroll-size);
    }

    .generate-table-wrapper::-webkit-scrollbar-track {
        background: var(--scroll-track);
        border-radius: var(--scroll-radius);
    }

    .generate-table-wrapper::-webkit-scrollbar-thumb {
        background-color: var(--scroll-thumb-color);
        background-image: var(--scroll-thumb-pattern);
        border-radius: var(--scroll-radius);
        border: 2px solid var(--scroll-track);
    }

    .generate-table-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: var(--scroll-thumb-color);
        filter: brightness(0.9);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    thead {
        background: #2b2b2b;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 5;
    }

    th {
        padding: 10px;
        text-align: left;
    }

    thead th {
        font-size: 18px;
        font-weight: 600;
    }

    td {
        padding: 10px;
    }

    .center {
        text-align: center;
    }

    .input-exemplares {
        width: 100%;
        padding: 6px;
        border-radius: 6px;
        border: 1px solid #cfcfcf;
    }

    .footer-btn-wrapper {
        display: flex;
        justify-content: center;
        gap: 30px;
    }

    .modal-button {
        padding: 10px 20px;
        border-radius: 8px;
        border: 1px solid #2b2b2b;
        background: #fff;
        cursor: pointer;
        font-size: 14px;
    }
</style>


<x-modals.modal id="copyManagerModal" title="Gerenciar Exemplares">

    <x-slot name="content">
        <div>
            <h3 style="font-size: 1.2rem; font-weight: normal; margin-bottom: 5px; color:#5c5a5a; ">
                Título
            </h3>
            <h2 style="font-size: 1.5rem; font-weight: normal; margin-bottom: 15px; color:#5c5a5a; ">
                A jornada do conhecimento
            </h2>

            <div class="generate-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>N Exemplares</th>
                            <th>Status</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="font-size: 1.3rem; font-weight: normal; color:#5c5a5a;">
                            <td>001</td>
                            <td style="font-weight: bold; ">Disponivel</td>
                            <td>10/01/2025</td>
                            <td>
                                <button data-icon="trash" class="btn-trash" title="Excluir exemplar">
                                    <x-icons.icon name="trash" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </x-slot>

    <x-slot name="footer">
             <div style="width: 100%; text-align: right;">
                <button type="button" class="modal-button" id="open-add-modal" title="Adicionar exemplares">
                    + Adicionar
                </button>
             </div>
    </x-slot>

</x-modals.modal>
