<div class="form-group" style="width: {{ $width ?? '100%' }};">
    <input type="{{ $type ?? 'text' }}" id="{{ $id }}" name="{{ $name ?? $id }}" class="form-input"
        value="{{ old($name ?? $id, $value ?? '') }}" autocomplete="off" aria-label="{{ $label }}"
        @if (!empty($required)) required @endif @if (!empty($readonly)) readonly @endif>
    <label class="form-label" for="{{ $id }}">{{ $label }}</label>
</div>
