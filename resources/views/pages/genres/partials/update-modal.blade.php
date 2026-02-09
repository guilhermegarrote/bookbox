<x-modals.modal id="genreUpdateModal" title="Atualizar Gênero">
    <x-slot name="content">
        <div class="form-row" style="display: flex; gap: 20px; justify-content: center; align-items: center;">
            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                <div id="color-preview" title="Cor do gênero"
                    style="
                        width: 35px;
                        height: 35px;
                        background-color: {{ '#' . "$genre->color_hex" }};
                        border: 1px solid #000;
                        border-right: none;
                        border-radius: 6px 0 0 6px;
                    ">
                </div>

                <input type="color" id="color_hex" name="color_hex"
                    style="position: absolute; inset: 0; opacity: 0; cursor: pointer;"
                    :value={{ '#' . "$genre->color_hex" }}>

                <input type="text" id="genre-color-picker" class="form-input" value={{ '#' . "$genre->color_hex" }} readonly
                    style="width: 100px; border-radius: 0 6px 6px 0;">
            </div>

            <x-modals.input-field id="name" label="Nome do gênero" width="240px" required
                title="Informe o nome do gênero" :value="$genre->name" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar a atualização do gênero">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update" title="Atualizar gênero">Atualizar</button>
    </x-slot name="footer">
</x-modals.modal>
