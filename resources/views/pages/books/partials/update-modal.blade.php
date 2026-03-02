<x-modals.modal id="bookUpdateModal" title="Atualizar Livro" :data-select="json_encode($selectData)">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" :value="$book->isbn"
                title="Código ISBN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$book->title"
                title="Título do livro" />
            <x-modals.input-field id="author" label="Autor" width="390px" :value="$book->author"
                title="Autor do livro" />

            <div class="form-group" style="display: flex; align-items: center;">
                <select id="genre_name" name="genre_name" class="form-input"
                    style="width: 170px; border-radius: 6px 0 0 6px; border-right: none; padding-right: 2px;"
                    title="Selecione o gênero do livro" data-value="{{ $book->genre_name }}" required>

                    <option value="{{ $book->genre_name }}" selected>{{ $book->genre_name }}</option>
                </select>
                <label class="form-label" for="genre_name"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Gênero do livro</span>
                </label>
                <div class="btn-input-side-wrapper">
                    <button id="open-create-genre-modal" class="btn-dark btn-input-side" style="border-radius: 0;"
                        title="Clique para adicionar gênero" aria-label="Adicionar gênero">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </div>
            </div>

            <x-modals.input-field id="publisher" label="Editora" width="360px" :value="$book->publisher"
                title="Editora do livro" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar atualização" aria-label="Cancelar atualização">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update"
            title="Salvar alterações do livro" aria-label="Salvar alterações do livro">Salvar</button>
    </x-slot name="footer">
</x-modals.modal>
