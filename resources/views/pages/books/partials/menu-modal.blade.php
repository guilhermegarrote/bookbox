<style>
    .btn-input-side svg {
        width: 30px;
        height: 30px;
        display: block;
    }
</style>

<x-modals.modal id="bookMenuModal" title="Menu do Livro">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" :value="$book->isbn" readonly
                title="Código ISBN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$book->title" readonly
                title="Título do livro" />
            <x-modals.input-field id="author" label="Autor" width="400px" :value="$book->author" readonly
                title="Autor do livro" />

            <div class="form-group" style="width: 200px; display: flex; align-items: center;">
                <input type="text" id="genre_name" name="genre_name" class="form-input"
                    value="{{ $book->genre_name }}" readonly
                    style="width: 165px; border-radius: 6px 0 0 6px;" title="Gênero do livro">
                <label class="form-label" for="genre_name"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Gênero do livro</span>
                </label>
                <div id="genre-color-preview"
                    style="width: 35px; height: 35px; background-color: {{ '#' . ($book->genre_color_hex ?? 'ccc') }}; border: 1px solid #000; border-left: none; border-radius: 0 6px 6px 0;"
                    title="Cor do gênero"></div>
            </div>

            <x-modals.input-field id="publisher" label="Editora" width="360px" :value="$book->publisher" readonly
                title="Editora do livro" />

            <div class="form-group" style="width: 240px; display: flex; align-items: center;">
                <input type="text" id="available_copies" name="available_copies" class="form-input"
                    value="{{ $book->available_copies . '/' . $book->total_copies }}" readonly
                    style="width: 165px; border-radius: 6px 0 0 6px; border-right: none;"
                    title="Número de exemplares disponíveis">
                <label class="form-label" for="available_copies"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Exemplares disponíveis</span>
                </label>
                <div class="btn-input-side-wrapper">
                    <button id="open-manager-copies-modal" class="btn-dark btn-input-side" style="border-radius: 0"
                        title="Clique para gerenciar os exemplares" aria-label="Gerenciar exemplares">
                        <x-icons.icon name="menu" class="" />
                    </button>
                </div>
            </div>
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do livro" aria-label="Fechar o menu do livro">Fechar</button>
        <button type="button" class="modal-button" id="submit-delete" title="Excluir este livro" aria-label="Excluir este livro">Excluir</button>
        <button type="button" class="modal-button" id="open-edit-modal" title="Abrir modal de edição" aria-label="Abrir modal de edição">Editar</button>
        <button type="button" class="modal-button" id="open-generate-label-modal" title="Abrir modal de geração de etiqueta" aria-label="Gerar etiqueta">Gerar Etiqueta</button>
    </x-slot name="footer">
</x-modals.modal>
