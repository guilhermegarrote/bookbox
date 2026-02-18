@props(['id', 'title'])

<div id="{{ $id }}" class="modal-window" aria-hidden="true" data-select="{{ $dataSelect ?? ''}}">
    <div class="modal-header">
        <h2 class="modal-title">{{ $title }}</h2>
    </div>

    <div class="modal-content">
        {{ $content }}

        @if (isset($footer))
            <div class="modal-footer">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
