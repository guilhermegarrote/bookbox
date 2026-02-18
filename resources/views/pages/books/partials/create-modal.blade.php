<x-modals.modal id="bookCreateModal" title="Cadastrar Livro" :data-select="json_encode($selectData)">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" required
                title="Informe o código ISBN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" required
                title="Informe o título do livro" />
            <x-modals.input-field id="author" label="Autor" width="375px" required
                title="Informe o autor do livro" />

            <div class="form-group" style="display: flex; align-items: center;">
                <select id="genre_name" name="genre_name" class="form-input"
                    style="width: 190px; border-radius: 6px 0 0 6px; border-right: none; padding-right: 2px;"
                    title="Selecione o gênero do livro" required>
                </select>
                <label class="form-label" for="genre_name"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Gênero do livro</span>
                </label>
                <div class="btn-input-side-wrapper">
                    <button id="open-create-genres-modal" class="btn-dark btn-input-side" style="border-radius: 0;"
                        title="Clique para adicionar gênero" aria-label="Adicionar gênero">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </div>
            </div>

            <x-modals.input-field id="publisher" label="Editora" width="360px" required
                title="Informe a editora do livro" />
            <x-modals.input-field id="number_copies" label="Quantidade de exemplares" width="240px" required
                title="Informe a quantidate de exemplares" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar o cadastro do livro" aria-label="Cancelar cadastro do livro">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo livro" aria-label="Cadastrar novo livro">Cadastrar</button>
    </x-slot name="footer">
</x-modals.modal>
