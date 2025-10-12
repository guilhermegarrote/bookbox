<x-modals.modal id="bookCreateModal" title="Cadastro de Livros">
    <x-slot:content>
        <div class="form-row">
            <x-modals.input-field id="ibsn" label="Código ISBN" width="160px" required
                title="Informe o código ISBN do livro" />
            <x-modals.input-field id="titulo" label="Título" width="440px" required
                title="Informe o título do livro" />
            <x-modals.input-field id="editora" label="Editora" width="300px" required
                title="Informe a editora do livro" />
            <x-modals.input-field id="autor" label="Autor" width="300px" required
                title="infrome o autor do livro" />
            <x-modals.input-field id="genero" label="Gênero" width="140px" required
                title="Informe o gênero do livro" />
            <x-modals.input-field id="qde" label="Quantidade de exemplares" width="225px" required
                title="Informe a quantidate de livros" />
        </div>
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar o cadastro do livro">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo livro">Cadastrar</button>
    </x-slot:footer>
</x-modals.modal>