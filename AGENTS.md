# AGENTS.md

Instructions for any AI assistant working in this repository: Claude, ChatGPT, Gemini,
Copilot, Cursor. This file is the single source of truth. `CLAUDE.md` and `GEMINI.md`
point here and only add tool-specific notes.

Human teammates should read `docs/` instead. Those are in Dutch and explain the same
rules in plain language.

## What this project is

Draagvlak is a speculative design prototype for a CMGT future scenario at Hogeschool
Rotterdam. It shows a 2038 app that turns social support into a public number between
0 and 100. The build exists to be tested on people, not to be shipped.

Two consequences that decide most judgement calls:

- Screens must feel finished enough that a participant forgets it is a prototype.
- The uncomfortable parts of the concept are the product. Never soften a screen to be
  friendlier, never add a reassuring disclaimer, never make the opt-out work.

Read `docs/01-project-en-scenario.md` before changing copy, and
`docs/06-onderzoek-en-inzichten.md` before changing anything that came out of user
research. Those two files carry decisions that should not be re-derived.

The screens themselves are not built yet. What exists is the template: the layout, the
helpers and the design system. Building a screen means adding a file to `public/`, not
reorganising what is here.

## Stack

PHP 8.4 or newer, no framework and no router. Vanilla ES modules, no bundler, no npm
runtime dependencies. CSS with custom properties, no preprocessor. JSON files in `data/`
instead of a database. State for one test run lives in the PHP session.

Do not add a framework, a build step, a CSS library, a JS library or a database.
If a task seems to need one, say so in one sentence and solve it without.

## Repository layout

The layout is page-based: one PHP file per screen in `public/`, logic in `src/`, the
document in `views/layouts/`, shared fragments in `views/partials/`. This is not MVC and
should not be converted to MVC.

```
public/           One file per screen. The only web-reachable folder.
  assets/css/     fonts.css, tokens.css, base.css, components.css, screens.css, in that order.
  assets/js/      app.js entry point plus modules/.
  assets/fonts/   Self-hosted Outfit and Inter, as woff2.
src/              PHP helpers, loaded automatically by bootstrap.php.
  support/        Escaping and URLs, session and flashes, request and redirect, the page template.
  data/           Reading and writing the JSON files.
views/layouts/    The document printed around a screen.
views/partials/   Fragments used by the layout or by more than one page.
data/             JSON fixtures.
docs/             Dutch documentation for the team.
bootstrap.php     Required by every page. Paths, error handling, session, helper loading.
```

Every directory name is lowercase. Placement is correctness, not taste: a file in the
wrong folder is a defect.

- Logic that a second page could ever need goes in `src/`, never in a page file.
- Markup used by a second page goes in `views/partials/`, never copied.
- Never declare a function inside a partial; partials are included more than once.
- Never write a `.php` file into `public/` that is not a screen a person can open.
- A file in `src/` only declares functions and constants. It is loaded on every request,
  so code that runs on its own does not belong there.

## How a page works

There are no imports. `bootstrap.php` loads every PHP file in `src/`, so every helper is
available everywhere. There is no `use function` line, no namespace and no autoload list
to maintain: a new file in `src/` works immediately.

```php
<?php

declare(strict_types=1);

/**
 * One sentence saying what this screen is for.
 */

require __DIR__ . '/../bootstrap.php';

if (isPost()) {
    // Handle the form, then always redirect.
    redirect('contacten');
}

$contacts = loadJson('contacts');

page('Contacten');

?>
<section class="section">
    <h1>Contacten</h1>
</section>
```

`page()` captures everything the screen prints and hands it to `views/layouts/app.php`
when the script ends. That is why a screen has no header include and no closing call.
Everything above the `?>` is loading and deciding; everything below it is markup with
variables in it. A calculation below the `?>` belongs in a function in `src/`.

The helpers a screen can rely on: `e()`, `attributes()`, `asset()`, `url()`,
`currentPage()`, `isCurrentPage()`, `page()`, `partial()`, `flash()`, `redirect()`,
`isPost()`, `input()`, `inputInt()`, `csrfToken()`, `csrfField()`, `isValidCsrf()`,
`sessionGet()`, `sessionSet()`, `sessionForget()`, `sessionReset()`, `loadJson()`,
`saveJson()`. Read the file in `src/` before using one; do not guess at a signature.

## Language rule

Code is English: identifiers, comments, docblocks, commit messages, branch names,
file names, CSS class names, JSON keys, console messages.

Dutch is what the participant reads: page copy, button labels, error messages shown
on screen, flash messages, and everything in `docs/`.

Internal keys stay English even when they represent something Dutch. A function returns
`strong`, `tight`, `low`, `critical`; the Dutch sentences for those keys live in a
partial. Never translate an internal key to Dutch and never put a Dutch string in `src/`.
A label a visitor reads therefore lives in a page or a partial, which is why the menu
labels sit in `views/partials/site-nav.php` and not in a helper.

## Naming

| Thing | Convention | Example |
|---|---|---|
| Directory | lowercase, kebab-case | `src/support` |
| PHP function | camelCase | `penaltyForDelay()` |
| PHP class | PascalCase | `ScoreCalculator` |
| PHP constant | UPPER_SNAKE_CASE | `DATA_PATH` |
| PHP variable | camelCase | `$secondsWaiting` |
| File with functions | kebab-case | `json-store.php` |
| File with a class | PascalCase | `ScoreCalculator.php` |
| Page file | lowercase Dutch, no dashes | `noodcontacten.php` |
| Layout and partial | kebab-case | `site-header.php` |
| CSS block | kebab-case | `.contact-row` |
| CSS element | double underscore | `.contact-row__name` |
| CSS modifier | double dash | `.contact-row--slow` |
| CSS state class | `is-` prefix | `.is-open` |
| CSS custom property | `--group-name` | `--color-accent` |
| JS function and variable | camelCase | `startComponents` |
| JS module file | kebab-case | `nav-toggle.js` |
| JSON key | camelCase | `responseMinutes` |
| Data attribute | kebab-case | `data-component` |

Page file names are Dutch because they become the URL a participant sees. Everything
else is English.

## Comments and documentation

Write a docblock on every exported thing: every function in `src/`, every function
exported from a JS module, every layout and every partial. The docblock says why the
thing exists and what a caller has to know, not what the next line does.

PHP: `/** */` with typed `@param` and `@return` only where the signature does not
already say it. Skip `@param int $score` when the signature reads `int $score` and the
name is obvious; keep it when the parameter needs an explanation. Every layout and
partial starts with a docblock listing its `@var` inputs, because those arrive through
`extract()` and an editor cannot infer them.

JavaScript: JSDoc on exported functions with `@param` and `@returns` including types,
since there is no TypeScript here and JSDoc is the only thing giving the editor type
information. Internal helpers get a one-line description without tags.

Inline comments only where the logic is non-obvious or where a choice looks like a
mistake and is not. In this project that is usually the scenario: a disabled toggle, a
grey escape button, a penalty that keeps running. Say why. Never comment the obvious,
never leave a commented-out block, never write history in a comment ("used to be",
"now that", "verified"); that belongs in the commit message.

Never add a comment that names a person, a date or an AI tool.

## Editor support

The setup relies on the helper files in `src/` and on docblocks for IntelliSense. Keep it
working:

- Type every parameter and return value. `mixed` only where the value genuinely is.
- Do not add a function to a page or a partial. An editor cannot offer what it cannot
  find in `src/`.
- No `@phpstan-ignore`, no `eslint-disable`, no silencing with `@`.
- Nothing has to be registered anywhere when you add a file to `src/`. If a function is
  reported as undefined, the file is not in `src/` or the name is misspelled.

## Design system

All colour, type, spacing and radius values live in `public/assets/css/tokens.css`.
A literal hex code, px value or font name anywhere else is a bug. Change the palette
by editing tokens, never by overriding a token at the point of use.

Tokens come in three layers. Primitives (`--civic-700`) are the raw palette and are only
used to build the layer above. Semantic tokens (`--color-accent`, `--color-surface`) are
what you write in a component. A component may define its own token when it needs one.
Light and dark share one set: `light-dark()` holds both values, and the theme switch only
changes `color-scheme`.

The direction is deliberate and documented in `docs/04-designsysteem.md`: the app looks
like calm institutional software, because a scoring system that looked sinister would
not be believable. Do not make it darker, more futuristic or more "dystopian". Do not
add gradients, drop shadows or glassmorphism.

Typefaces are Outfit for display (headings, the brand, a number that has to land) and
Inter for everything read as a sentence. Both are self-hosted in `public/assets/fonts/`;
do not add a font CDN and do not add a third typeface. Figures that change or line up get
`.numeric`, which turns on tabular figures.

This is a responsive site, not a phone frame: mobile first, and the layout grows into the
width it is given. Test at 390px wide and at desktop width.

Accessibility floor: WCAG AA contrast, visible focus outline, labels on inputs,
`prefers-reduced-motion` respected. Never remove a focus style.

## Front-end behaviour

`app.js` starts a module for every element with a `data-component` attribute:
`data-component="nav-toggle"` loads `modules/nav-toggle.js` and calls its `init(element)`.
Adding behaviour means adding a module and an attribute, never an import in `app.js` or a
script tag in the layout. A page without that attribute loads no module at all.

Behaviour is an enhancement: a screen has to work without JavaScript, and the menu is
built that way.

## Git

`develop` is where the work happens and `main` is what has to run during a demo. Never
commit to either directly.

```
git switch develop
git pull
git switch -c feature/contacts-screen
```

Branch names are English, with a prefix: `feature/`, `fix/` or `docs/`. One subject per
branch. Open the pull request against `develop`, never against `main`.

## Working rules

- Implement the change. Do not hand back a description of a change that was asked for.
- Read the file before you edit it. Prefer the file to your memory of it.
- Make the smallest change that does the job. Do not reformat, rename or restructure
  files you were not asked about.
- Never invent a path, a function, a CSS class or a data key. Grep for it first.
- Run `composer lint` after touching PHP. Say plainly what you could not run.
- When something you were asked to do conflicts with this file, do it the way this file
  says and mention the conflict once.

## Definition of done

A change is done when the page it affects has been opened in a browser at
`http://localhost:8000` and behaves as intended, at phone width as well, `composer lint`
is clean, and the screen still reads as Dutch to a participant and as English in the
source.
