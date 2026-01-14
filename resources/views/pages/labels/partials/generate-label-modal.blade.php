<style>
    :root {
        --scroll-size: 6px;
        --scroll-radius: 6px;
        --scroll-track: var(--color-base-gray-454);
        ;
        --scroll-thumb-color: #ffffffff;
        --scroll-thumb-pattern: none;
    }

    .modal-window {
        width: 750px;
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
        font-family: var(--font-primary);
    }

    .generate-table-wrapper {
        max-height: 240px;
    }

    .center {
        text-align: center;
    }

    .input-copies {
        width: 100%;
        padding: 6px;
        border-radius: 6px;
        border: 1px solid #cfcfcf;
        font-family: var(--font-primary);
    }

    .footer-btn-wrapper {
        display: flex;
        justify-content: center;
        gap: 30px;
    }
</style>

<x-modals.modal id="labelGenerateModal" title="Gerar etiquetas">
    <x-slot name="content">
        <div class="modal-wrapper">

            <div class="search-box">
                <input type="text" id="item-search" placeholder="Pesquisa" />
            </div>

            <div class="table-wrapper generate-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Exemplares</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('pages.labels.partials.generate-label-modal-list', ['books' => $books])
                    </tbody>
                </table>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="footer-btn-wrapper">
            <button type="button" class="modal-button" id="btn-close" title="Fechar o menu">Fechar</button>
            <button type="button" class="modal-button" id="submit-generate-label" title="Gerar etiquetas">Gerar
                etiquetas</button>
        </div>
    </x-slot>
</x-modals.modal>
