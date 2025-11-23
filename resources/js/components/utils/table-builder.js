export function getButtonHTML(icon) {
    const btn = document.querySelector(`#button-prototypes [data-icon="${icon}"]`);
    return btn ? btn.outerHTML : '';
}

