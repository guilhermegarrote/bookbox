/**
 * Modal Form Utilities
 * ---------------------
 * Handles reading and restoring form state from modals.
 */

/**
 * Extracts form values from a modal.
 *
 * @param {HTMLElement} modal
 * @returns {object}
 */
export function getModalData(modal) {
    if (!modal) return {};

    const data = {};
    const elements = modal.querySelectorAll('input, select, textarea');

    elements.forEach((el) => {
        if (!el.id) return;
        if (el.disabled) return;
        if (el.hasAttribute('readonly')) return;

        let value;

        if (el.tagName === 'INPUT') {
            const type = (el.getAttribute('type') || '').toLowerCase();

            if (type === 'checkbox') {
                value = el.checked;
            } else if (type === 'radio') {
                if (!el.checked) return;
                value = el.value ?? '';
            } else {
                value = el.value?.trim() || '';
            }
        } else if (el.tagName === 'TEXTAREA') {
            value = el.value?.trim() || '';
        } else if (el.tagName === 'SELECT') {
            value = el.multiple
                ? Array.from(el.selectedOptions).map((o) => o.value)
                : el.value ?? '';
        } else {
            value = el.value?.trim() || '';
        }

        data[el.id] = value;
    });

    return data;
}

/**
 * Restores form values into a modal.
 *
 * @param {HTMLElement} modal
 * @param {object} data
 */
export function restoreFormData(modal, data = {}) {
    if (!modal || !data) return;

    const callNativeSetter = (el, prop, value) => {
        try {
            const proto = Object.getPrototypeOf(el);
            const desc =
                Object.getOwnPropertyDescriptor(proto, prop) ||
                Object.getOwnPropertyDescriptor(el, prop);

            const setter = desc?.set;

            if (setter) setter.call(el, value);
            else el[prop] = value;
        } catch (e) {
            el[prop] = value;
        }
    };

    const dispatch = (el, type) => {
        try {
            el.dispatchEvent(new Event(type, { bubbles: true }));
        } catch (e) {}
    };

    for (const [key, rawValue] of Object.entries(data)) {
        const el = modal.querySelector(`#${CSS.escape(key)}`);
        if (!el) continue;

        const value = rawValue;

        if (el.tagName === 'INPUT') {
            const type = (el.getAttribute('type') || '').toLowerCase();

            if (type === 'checkbox') {
                const checked = value === true || value === 'true' || value === '1' || value === 'on';
                callNativeSetter(el, 'checked', checked);
                dispatch(el, 'input');
                dispatch(el, 'change');
            } else if (type === 'radio') {
                const name = el.name;

                if (name) {
                    const radios = modal.querySelectorAll(
                        `input[type="radio"][name="${CSS.escape(name)}"]`
                    );

                    radios.forEach((r) => {
                        const should = r.value == value;
                        callNativeSetter(r, 'checked', should);
                        if (should) {
                            dispatch(r, 'input');
                            dispatch(r, 'change');
                        }
                    });
                } else {
                    callNativeSetter(el, 'value', value);
                    dispatch(el, 'input');
                    dispatch(el, 'change');
                }
            } else if (type === 'file') {
                continue;
            } else {
                callNativeSetter(el, 'value', value ?? '');
                dispatch(el, 'input');
                dispatch(el, 'change');
            }
        } else if (el.tagName === 'TEXTAREA') {
            callNativeSetter(el, 'value', value ?? '');
            dispatch(el, 'input');
            dispatch(el, 'change');
        } else if (el.tagName === 'SELECT') {
            if (el.multiple) {
                const vals = Array.isArray(value)
                    ? value.map((v) => (v ?? '').toString())
                    : typeof value === 'string'
                        ? value.split(',')
                        : [(value ?? '').toString()];

                Array.from(el.options).forEach((opt) => {
                    callNativeSetter(opt, 'selected', vals.includes(opt.value));
                });
            } else {
                callNativeSetter(el, 'value', value ?? '');
            }

            dispatch(el, 'input');
            dispatch(el, 'change');
        } else {
            callNativeSetter(el, 'value', value ?? '');
            dispatch(el, 'input');
            dispatch(el, 'change');
        }

        if (el.classList?.contains('form-input')) {
            const has =
                el.type === 'checkbox' || el.type === 'radio'
                    ? el.checked
                    : (el.value ?? '').toString().trim() !== '';

            el.classList.toggle('has-value', !!has);
        }
    }
}

/**
 * Toggles readonly/disabled fields.
 *
 * @param {HTMLElement[]} fields
 * @param {boolean} enable
 */
export function toggleFields(fields, enable) {
    fields.forEach((f) => {
        if (f.tagName === 'INPUT') {
            enable ? f.removeAttribute('readonly') : f.setAttribute('readonly', true);
        }

        if (f.tagName === 'SELECT') {
            enable ? f.removeAttribute('disabled') : f.setAttribute('disabled', true);
        }
    });
}
