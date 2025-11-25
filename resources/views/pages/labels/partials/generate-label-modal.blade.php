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


<x-modals.modal id="labelGenerateModal" title="Gerar etiquetas">
    <x-slot name="content">
        <div class="modal-wrapper">

            <div class="search-box">
                <input type="text" placeholder="Pesquisa" />
            </div>

            <div class="generate-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Nome do livro</th>
                            <th>Autor</th>
                            <th>Exemplares</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="center"><input type="checkbox" /></td>
                            <td>A Jornada do Conhecimento</td>
                            <td>Marina Oliveira</td>
                            <td><input type="text" class="input-exemplares" placeholder="Ex: 1-10 ou 1,3,4-10" /></td>
                        </tr>
                        <tr>
                            <td class="center"><input type="checkbox" /></td>
                            <td>O Universo Invisível</td>
                            <td>Rafael Mendes</td>
                            <td><input type="text" class="input-exemplares" placeholder="Ex: 2-5 ou 7,9-12" /></td>
                        </tr>
                        <tr>
                            <td class="center"><input type="checkbox" /></td>
                            <td>Além das Estrelas</td>
                            <td>Marina Oliveira</td>
                            <td><input type="text" class="input-exemplares" placeholder="Ex: 1-3 ou 4,6-9" /></td>
                        </tr>
                        <tr>
                            <td class="center"><input type="checkbox" /></td>
                            <td>Caminhos da História</td>
                            <td>Eduardo Teles</td>
                            <td><input type="text" class="input-exemplares" placeholder="Ex: 5-15" /></td>
                        </tr>
                        <tr>
                            <td class="center"><input type="checkbox" /></td>
                            <td>Reflexões Modernas</td>
                            <td>Carla Nogueira</td>
                            <td><input type="text" class="input-exemplares" placeholder="Ex: 1-8 ou 10-20" /></td>
                        </tr>
                        <tr>
                            <td class="center"><input type="checkbox" /></td>
                            <td>Mundos Paralelos</td>
                            <td>Luis Fernando</td>
                            <td><input type="text" class="input-exemplares" placeholder="Ex: 3-7 ou 9-14" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="footer-btn-wrapper">
            <button type="button" class="modal-button" id="btn-close" title="Fechar o menu">Fechar</button>
            <button type="button" class="modal-button" id="submit-generate-label" title="Gerar etiquetas">
                Gerar etiquetas
            </button>
        </div>
    </x-slot>
</x-modals.modal>