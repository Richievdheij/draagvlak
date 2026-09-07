/**
 * Entry point of the front-end.
 *
 * The layout loads this file once, as a module. It finds every element with a
 * data-component attribute and imports the matching file from modules/ by
 * itself, so a new component needs no import here and no line in the layout:
 * name the file after the attribute value and export init().
 *
 *   <button data-component="nav-toggle"> starts modules/nav-toggle.js
 *
 * A module is only fetched on a page that actually uses it.
 */

const started = new WeakSet();

/**
 * Start every component inside a part of the page.
 *
 * Call this again after inserting HTML, to start the components in it. Elements
 * that already run are skipped.
 *
 * @param {ParentNode} [root] Where to look. Defaults to the whole document.
 * @returns {Promise<void>} Resolves once every module has run its init().
 */
export async function startComponents(root = document) {
    const elements = [...root.querySelectorAll('[data-component]')];

    await Promise.all(elements.map((element) => startElement(element)));
}

/** Import and start the modules one element asks for. */
async function startElement(element) {
    if (started.has(element)) {
        return;
    }

    started.add(element);

    const names = element.dataset.component.split(/\s+/).filter(Boolean);

    for (const name of names) {
        try {
            const module = await import(`./modules/${name}.js`);

            await module.init?.(element);
        } catch (error) {
            console.error(`Component "${name}" could not start.`, error);
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => startComponents(), { once: true });
} else {
    startComponents();
}
