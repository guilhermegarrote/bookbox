<x-modals.modal id="bookMenuModal" title="Menu de Livros">
    <x-slot:content>
        <div class="form-row">
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" :value="$book->isbn" readonly
                title="Código IBSN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$book->title" readonly
                title="Título do livro" />
            <x-modals.input-field id="publisher" label="Editora" width="300px" :value="$book->publisher" readonly
                title="Editora do livro" />
            <x-modals.input-field id="author" label="Autor" width="300px" :value="$book->author" readonly
                title="Autor do livro" />
            <x-modals.input-field id="genre_name" label="Gênero" width="140px" :value="$book->genre_name" readonly
                title="Gênero do livro" />
            <x-modals.input-field id="total_copies" label="Exemplares disponíveis" width="225px" :value="$book->available_copies . '/' . $book->total_copies"
                readonly title="Exemplares disponíveis" />
        </div>
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do livro">Fechar</button>
        <button type="button" class="modal-button" id="submit-delete" title="Excluir este livro">Excluir</button>
        <button type="button" class="modal-button" id="open-edit-modal" title="Abrir modal de edição">Editar</button>
        <button type="button" class="modal-button" id="" title="Gerar etiqueta do livro">Gerar
            Etiqueta</button>
    </x-slot:footer>
</x-modals.modal>
