/**
 * The light and dark switch in the footer.
 *
 * Three states in a row: follow the system, force light, force dark. The choice
 * is remembered, and the layout applies it before the first paint, so a page
 * never appears in the wrong theme first. This module only changes it.
 *
 * The labels come from data attributes on the button, so the Dutch wording
 * stays in the template.
 *
 * @param {HTMLElement} button Button with data-label-auto, data-label-light and
 *                             data-label-dark, holding a [data-theme-label] node.
 * @returns {void}
 */
export function init(button) {
    const label = button.querySelector('[data-theme-label]');

    apply(button, label, readMode());

    button.addEventListener('click', () => {
        const next = MODES[(MODES.indexOf(readMode()) + 1) % MODES.length];

        apply(button, label, next);
    });
}

const MODES = ['auto', 'light', 'dark'];
const STORAGE_KEY = 'theme';

/** The stored choice, or 'auto' when there is none or storage is blocked. */
function readMode() {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);

        return MODES.includes(stored) ? stored : 'auto';
    } catch {
        return 'auto';
    }
}

/** Store the mode, put it on the document and update the button label. */
function apply(button, label, mode) {
    try {
        if (mode === 'auto') {
            localStorage.removeItem(STORAGE_KEY);
        } else {
            localStorage.setItem(STORAGE_KEY, mode);
        }
    } catch {
        // Storage can be blocked. The switch then lasts for this page only.
    }

    if (mode === 'auto') {
        delete document.documentElement.dataset.theme;
    } else {
        document.documentElement.dataset.theme = mode;
    }

    if (label) {
        label.textContent = button.dataset[`label${mode[0].toUpperCase()}${mode.slice(1)}`] ?? '';
    }
}
