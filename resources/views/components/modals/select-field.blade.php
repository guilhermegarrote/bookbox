<div class="form-group" style="width: {{ $width ?? '100%' }};">
    <select id="{{ $id }}" name="{{ $name ?? $id }}" class="form-input"
        @if (!empty($required)) required @endif @if (!empty($readonly)) disabled @endif
        aria-label="{{ $label }}">

        @if (!empty($value))
            <option value="{{ $value }}" selected>{{ $value }}</option>
        @endif

        @foreach ($options ?? [] as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @if (($value ?? '') == $optionValue) selected @endif>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    <label class="form-label" for="{{ $id }}">{{ $label }}</label>
</div>
