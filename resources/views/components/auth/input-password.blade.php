@props([
'label',
'name',
'value' => '',
'required' => true,
])

@php
$inputId = $attributes->get('id', $name);
@endphp

<div class="input-group2" style="position: relative;">
    @if($label)
    <label for="{{ $inputId }}" class="auth-label">
        {{ $label }}{{ $required ? '*' : '' }}
    </label>
    @endif

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="password"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => 'auth-input with-password-toggle ' . ($errors->has($name) ? 'input-error' : ''),
            'aria-describedby' => "erro-{$name}",
            'aria-invalid' => $errors->has($name) ? 'true' : 'false',
        ]) }}
        @if($required) required @endif
        autocomplete="current-password">

    <button
        type="button"
        aria-label="Mostrar ou ocultar senha"
        aria-pressed="false"
        onclick="togglePasswordVisibility('{{ $inputId }}', this)"
        tabindex="0"
        class="password-toggle-btn"
        onmouseenter="this.classList.add('hovered')"
        onmouseleave="this.classList.remove('hovered')"
        onfocus="this.classList.add('hovered')"
        onblur="this.classList.remove('hovered')">
        <svg
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
            role="img"
            class="icon-eye">
            <path class="eye-path" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
            <circle class="eye-pupil" cx="12" cy="12" r="3" />
        </svg>
    </button>
</div>

<style>
    .input-group2 {
        position: relative;
    }

    .password-toggle-btn {
        position: absolute;
        top: 50%;
        right: 0.5rem;
        transform: translateY(-35%);
        border: none;
        background-color: transparent;
        padding: 0.4rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        color: #888;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        opacity: 0;
        transition:
            opacity 0.3s ease,
            background-color 0.3s ease,
            color 0.3s ease;
        z-index: 10;
        outline: none;
    }

    .password-toggle-btn.visible {
        opacity: 1;
        pointer-events: auto;
        background-color: rgba(160, 160, 160, 0.12);
    }

    .password-toggle-btn.hovered {
        color: #555;
        background-color: #f1f1f1;
    }

    .password-toggle-btn:focus {
        outline: none;
        background-color: #e0e0e0;
    }

    .icon-eye {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        user-select: none;
        pointer-events: none;
    }

    .eye-path {
        stroke-dasharray: 0;
        transition: stroke-dasharray 0.3s ease;
    }

    .eye-pupil {
        fill: none;
        transition: fill 0.3s ease;
    }

    .eye-visible .eye-path {
        stroke-dasharray: 4;
    }

    .eye-visible .eye-pupil {
        fill: var(--color-base-gray-light);
    }
</style>

<script>
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const svg = button.querySelector('svg');
        if (!svg) return;

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');

        if (isPassword) {
            svg.classList.add('eye-visible');
        } else {
            svg.classList.remove('eye-visible');
        }
    }

    function setupInputFocusHandlers() {
        document.querySelectorAll('.input-group2').forEach(group => {
            const input = group.querySelector('input.auth-input');
            const button = group.querySelector('.password-toggle-btn');

            if (!input || !button) return;

            function addFocus() {
                button.classList.add('visible');
            }

            function removeFocus() {
                setTimeout(() => {
                    if (
                        document.activeElement !== input &&
                        document.activeElement !== button
                    ) {
                        button.classList.remove('visible');
                    }
                }, 100);
            }

            input.addEventListener('focus', addFocus);
            input.addEventListener('blur', removeFocus);
            button.addEventListener('focus', addFocus);
            button.addEventListener('blur', removeFocus);

            if (
                document.activeElement === input ||
                document.activeElement === button
            ) {
                button.classList.add('visible');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', setupInputFocusHandlers);
</script>
