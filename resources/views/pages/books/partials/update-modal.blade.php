<x-modals.modal id="bookUpdateModal" title="Atualizar Livro">
    <x-slot:content>
        <div class="form-row">
            <x-modals.input-field id="ibsn" label="Código ISBN" width="150px" :value="$book->isbn"
                title="Informe o código IBSN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$book->title"
                title="Informe o titulo do livro" />
            <x-modals.select-field id="publisher" label="Editora" width="300px" data-value="{{ $book->publisher }}"
                :value="$book->publisher" title="Informe a editora do livro" />
            <x-modals.select-field id="author" label="Autor" width="300px" data-value="{{ $book->author }}"
                :value="$book->author" title="Informe o autor do livro" />
            <x-modals.select-field id="genre_name" label="Gênero" width="140px" data-value="{{ $book->genre_name }}"
                :value="$book->genre_name " title="Informe o gênero do livro" />
            <x-modals.input-field id="total_copies" label="Exemplares disponíveis" width="225px" :value="$book->available_copies . '/' . $book->total_copies"
                            readonly title="Exemplares disponíveis" />
            
        </div>
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-cancel-update"
            title="Cancelar atualização">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update"
            title="Salvar alterações do livro">Salvar</button>
    </x-slot:footer>
</x-modals.modal>



         