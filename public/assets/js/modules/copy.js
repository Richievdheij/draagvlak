/**
 * Copy a short value, like the code somebody shares to add you.
 *
 * The value is on the page either way: this only saves somebody from selecting
 * it by hand. The button is printed hidden and shown from here, so a browser
 * without JavaScript never offers something that would do nothing.
 *
 * Inside the element it looks for:
 *   [data-copy-button]  the button, printed hidden, carrying data-copied-label
 *   [data-copy-label]   the text inside it, briefly replaced after a copy
 *
 * The Dutch word it shows afterwards comes from that data attribute, so all
 * screen copy stays in the template.
 *
 * @param {HTMLElement} element Element with data-copy-value.
 * @returns {void}
 */
export function init(element) {
    const value = element.dataset.copyValue ?? '';
    const button = element.querySelector('[data-copy-button]');

    if (value === '' || !button || !navigator.clipboard) {
        return;
    }

    const label = button.querySelector('[data-copy-label]') ?? button;
    const original = label.textContent;

    button.hidden = false;

    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(value);
        } catch {
            // Copying can be refused. The code is still readable on the screen.
            return;
        }

        label.textContent = button.dataset.copiedLabel ?? original;

        setTimeout(() => {
            label.textContent = original;
        }, CONFIRMATION_MS);
    });
}

/** How long the button says it worked before it goes back to normal. */
const CONFIRMATION_MS = 2000;
