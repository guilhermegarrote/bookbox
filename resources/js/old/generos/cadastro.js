    document.addEventListener('input', function (e) {
        const colorPicker = document.getElementById('cor-genero-picker');
        const colorInput = document.getElementById('cor-genero');
        const preview = document.getElementById('cor-preview');

        if (e.target.id === 'cor-genero-picker') {
            const cor = e.target.value;
            colorInput.value = cor;
            colorPicker.classList.add('preenchido');
            preview.style.backgroundColor = cor;
        } else if (e.target.id === 'cor-genero') {
            const cor = e.target.value;
            if (/^#[0-9A-Fa-f]{6}$/.test(cor)) {
                preview.style.backgroundColor = cor;
                colorPicker.value = cor;
            }
        }
    });

function rgbParaHex(rgb) {
    const match = rgb.match(/\d+/g);
    if (!match || match.length < 3) return '#000000';
    return `#${match.slice(0, 3).map(x => (+x).toString(16).padStart(2, '0')).join('').toUpperCase()}`;
}