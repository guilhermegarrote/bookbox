/**
 * Floating Labels
 * ----------------
 * Adds "has-value" class to inputs based on their current value.
 */

/**
 * Updates floating label behavior for modal inputs.
 *
 * @param {HTMLElement} modal
 */
export function updateFloatingLabels(modal) {
    if (!modal) return;

    modal.querySelectorAll('.form-input').forEach((input) => {
        const toggleHasValue = () =>
            input.classList.toggle('has-value', input.value.trim() !== '');

        toggleHasValue();
        input.addEventListener('input', toggleHasValue);
    });
}
