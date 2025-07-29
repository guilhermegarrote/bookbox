@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'classe' => 'auth-input',
    'required' => true,
])

<div class="input-group" style="position: relative;">
    @if($label)
        <label for="{{ $name }}" class="auth-label">
            {{ $label }}{{ $required ? '*' : '' }}
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => "$classe " . ($errors->has($name) ? 'input-error' : ''),
            'aria-describedby' => "erro-{$name}",
            'aria-invalid' => $errors->has($name) ? 'true' : 'false',
        ]) }}
        @if($required) required @endif
    >
</div>
