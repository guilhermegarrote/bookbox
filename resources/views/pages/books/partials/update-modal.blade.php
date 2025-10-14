<x-modals.modal id="bookUpdateModal" title="Atualizar Livro">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" :value="$book->isbn"
                title="Código ISBN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$book->title"
                title="Título do livro" />
            <x-modals.input-field id="author" label="Autor" width="400px" data-value="{{ $book->author }}"
                :value="$book->author" title="Autor do livro" />
            <x-modals.select-field id="genre_name" label="Gênero" width="200px" data-value="{{ $book->genre_name }}"
                :value="$book->genre_name" title="Gênero do livro" />
            <x-modals.input-field id="publisher" label="Editora" width="360px" data-value="{{ $book->publisher }}"
                :value="$book->publisher" title="Editora do livro" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-cancel-update"
            title="Cancelar atualização">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update"
            title="Salvar alterações do livro">Salvar</button>
    </x-slot name="footer">
</x-modals.modal>
