/**
 * The menu button that opens the navigation on a phone.
 *
 * The panel it controls is named in aria-controls, so this module works for any
 * button and panel pair without knowing their classes.
 *
 * @param {HTMLElement} button Button with aria-controls and aria-expanded.
 * @returns {void}
 */
export function init(button) {
    const panel = document.getElementById(button.getAttribute('aria-controls') ?? '');

    if (!panel) {
        return;
    }

    button.addEventListener('click', () => {
        setOpen(button, panel, !isOpen(button));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen(button)) {
            setOpen(button, panel, false);
            button.focus();
        }
    });
}

/** Whether the panel is open right now, according to the button. */
function isOpen(button) {
    return button.getAttribute('aria-expanded') === 'true';
}

/** Open or close the panel and report the state on the button. */
function setOpen(button, panel, open) {
    button.setAttribute('aria-expanded', String(open));
    panel.classList.toggle('is-open', open);
}
