<h2 class="config-title">Configurações</h2>

<div class="configs-card">
    <div class="config-item">
        <div class="config-text">
            <span class="config-label">Dias de empréstimo</span>
            <small class="config-description">
                Prazo padrão, em dias, antes do empréstimo vencer.
            </small>
        </div>
        <input type="number" name="default_due_days"
            value="{{ $config['default_due_days'] ?? config('settings.default_due_days.default') }}"
            min="{{ config('settings.default_due_days.min') }}" max="{{ config('settings.default_due_days.max') }}">
    </div>

    <div class="config-item">
        <div class="config-text">
            <span class="config-label">Dias de prolongamento</span>
            <small class="config-description">
                Quantidade de dias adicionados ao prazo quando o empréstimo é renovado.
            </small>
        </div>
        <input type="number" name="extension_days"
            value="{{ $config['extension_days'] ?? config('settings.extension_days.default') }}"
            min="{{ config('settings.extension_days.min') }}" max="{{ config('settings.extension_days.max') }}">
    </div>

    <div class="config-item">
        <div class="config-text">
            <span class="config-label">Empréstimos simultâneos permitidos</span>
            <small class="config-description">
                Número máximo de itens que um usuário pode emprestar ao mesmo tempo.
            </small>
        </div>
        <input type="number" name="max_book_loans"
            value="{{ $config['max_book_loans'] ?? config('settings.max_book_loans.default') }}"
            min="{{ config('settings.max_book_loans.min') }}" max="{{ config('settings.max_book_loans.max') }}">
    </div>
</div>

<div class="config-actions hidden" id="config-actions">
    <button type="button" class="btn-secondary" id="cancel-config">Cancelar</button>
    <button type="button" class="btn-primary" id="save-config">Salvar Configurações</button>
</div>
