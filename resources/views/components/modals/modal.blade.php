@props(['id', 'title', 'closeId'])

<div id="{{ $id }}" class="modal-window" aria-hidden="true">
    <div class="modal-header">
        <h2 class="modal-title">{{ $title }}</h2>
        <!-- <button type="button" class="modal-close-btn" id="{{ $closeId }}" aria-label="Fechar modal">
             <x-icons.icon name="close" class="close-icon"/>
        </button> -->
    </div>

    <div class="modal-content">
        <div class="form-row">
            {{ $content }}
        </div>

        @if (isset($footer))
            <div class="modal-footer">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
