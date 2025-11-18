<x-modals.modal id="bookCreateModal" title="Cadastrar Livro">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" required
                title="Informe o código ISBN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" required
                title="Informe o título do livro" />
            <x-modals.input-field id="author" label="Autor" width="400px" required
                title="infrome o autor do livro" />
            <x-modals.select-field id="genre_name" label="Gênero" width="200px" required
                title="Selecione o gênero do livro" />
            <x-modals.input-field id="publisher" label="Editora" width="360px" required
                title="Informe a editora do livro" />
            <x-modals.input-field id="number_copies" label="Quantidade de exemplares" width="240px" required
                title="Informe a quantidate de exemplares" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Cancelar o cadastro do livro">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo livro">Cadastrar</button>
    </x-slot name="footer">
</x-modals.modal>
