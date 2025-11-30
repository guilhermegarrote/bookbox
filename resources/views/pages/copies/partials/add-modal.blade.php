<style>
    .rec-inputs {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0;
        margin-top: 10px;
        margin-bottom: 15px;
    }

    .quantity-btn {
        background-color: #000;
        color: white;
        font-size: 20px;
        width: 40px;
        height: 40px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .quantity-btn:hover {
        background-color: #444;
    }

    .quantity-btn.left {
        border-radius: 6px 0px 0px 6px;
    }

    .quantity-btn.right {
        border-radius: 0px 6px 6px 0px;
    }

    .modal-input {
        font-family: var(--font-primary);
        width: 150px;
        height: 40px;
        font-size: 16px;
        text-align: center;
        border: 1px solid #ccc;
        padding: 5px;
        background-color: #f8f9fa;
        color: #333;
        cursor: text;
        border-left: none;
        border-right: none;
    }

    .modal-input::-webkit-outer-spin-button,
    .modal-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .modal-input[type=number] {
        -moz-appearance: textfield;
    }

    .modal-input:focus {
        outline: none;
        box-shadow: none;
    }
</style>

<x-modals.modal id="copyAddModal" title="Cadastro de exemplares">
    <x-slot name="content">
        <div class="rec-inputs">
            <div class="quantity-container">
                <button id="decrement" class="quantity-btn left" type="button">–</button>
                <input type="number" id="amount" class="modal-input" value="1" min="1" max="32766">
                <button id="increment" class="quantity-btn right" type="button">+</button>
            </div>
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do aluno">Fechar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar exemplares">Cadastrar</button>
    </x-slot name="footer">
</x-modals.modal>
