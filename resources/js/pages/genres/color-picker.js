export function initColorPicker({
    pickerId = 'genre-color-picker',
    inputId = 'color_hex',
    previewId = 'color-preview'
} = {}) {
    const colorPicker = document.getElementById(pickerId);
    const colorInput = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (!colorPicker || !colorInput || !preview) return;

    document.addEventListener('input', function (e) {
        if (e.target === colorPicker) {
            const color = e.target.value;
            colorInput.value = color.toUpperCase();
            preview.style.backgroundColor = color;
        }

        if (e.target === colorInput) {
            const color = e.target.value;

            if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
                preview.style.backgroundColor = color;
                colorPicker.value = color.toUpperCase();
            }
        }
    });
}
