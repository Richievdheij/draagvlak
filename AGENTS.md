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

The home screen, logging in and registering are built. Every other screen in `public/`
is an empty file with its own stylesheet already wired up, waiting for content.

## Stack

PHP 8.4 or newer, no framework and no router. MySQL through PDO, no ORM and no query
builder. Vanilla ES modules, no bundler, no npm runtime dependencies. CSS with custom
properties, no preprocessor. State for one visit lives in the PHP session; everything
that has to survive a refresh lives in the database.

Do not add a framework, a build step, a CSS library, a JS library or an ORM.
If a task seems to need one, say so in one sentence and solve it without.

## Repository layout

The layout is page-based: one PHP file per screen in `public/`, logic in `src/`, the
document in `views/layouts/`, shared fragments in `views/partials/`. This is not MVC and
should not be converted to MVC.

```
public/            One file per screen. The only web-reachable folder.
  assets/css/      base/, layouts/, partials/, components/, pages/. See below.
  assets/js/       app.js entry point plus modules/.
  assets/fonts/    Self-hosted Outfit and Inter, as woff2.
src/               PHP helpers, loaded automatically by bootstrap.php.
  support/         Config, escaping and URLs, session and flashes, request, formatting,
                   and the page template.
  data/            The database connection and queries, the JSON reader, the seed.
  auth/            Registering, logging in, and closing a screen for visitors.
  score/           The rules of the scenario and what a choice costs.
  contacts/        The list of people, and taking someone off it.
  messages/        The inbox, the response window and the outcome of a choice.
views/layouts/     The document printed around a screen.
views/partials/    Fragments used by the layout or by more than one page.
database/          schema.sql: the structure of the database.
bin/               Command line scripts, run through composer.
data/              scenario.json: the Dutch content every participant starts with.
docs/              Dutch documentation for the team.
bootstrap.php      Required by every page. Paths, error handling, session, helper loading.
config.php         Database settings. Overridden per machine by config.local.php.
```

Every directory name is lowercase. Placement is correctness, not taste: a file in the
wrong folder is a defect.

- Logic that a second page could ever need goes in `src/`, never in a page file.
- Markup used by a second page goes in `views/partials/`, never copied.
- Never declare a function inside a page or a partial; both are included more than once.
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

requireLogin();

if (isPost()) {
    // Handle the form, then always redirect.
    redirect('contacten');
}

$contacts = activeContacts(currentUserId());

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
`isPost()`, `input()`, `inputInt()`, `csrfField()`, `isValidCsrf()`, `rememberForm()`,
`takeForm()`, `sessionGet()`, `sessionSet()`, `sessionForget()`, `sessionReset()`,
`requireLogin()`, `requireGuest()`, `currentUser()`, `currentUserId()`, `db()`,
`dbAll()`, `dbFirst()`, `dbValue()`, `dbRun()`, `dbInsert()`, `loadJson()`,
`timeParts()`, `formatCountdown()`, `formatPrice()`, `initial()`. Read the file in
`src/` before using one; do not guess at a signature.

## The database

MySQL, reached through PDO. `composer db:setup` creates the database, the tables from
`database/schema.sql` and the demo content; `composer db:fresh` drops it and builds it
again, which is how a test session is reset.

Every query is a prepared statement with the values passed separately. A value never
goes into the SQL string, not even one you typed yourself:

```php
dbAll('SELECT * FROM contacts WHERE user_id = ?', [$userId]);   // right
dbAll("SELECT * FROM contacts WHERE user_id = $userId");        // wrong
```

Always look a row up together with the id of the visitor who owns it, otherwise a
changed number in the URL reaches someone else's data.

Four tables: `users`, `contacts`, `messages`, `score_events`. A change to a score is
written to `score_events` as well as to the number itself, so a researcher can read back
a whole session. Add a column by editing `database/schema.sql` and running
`composer db:fresh`; there are no migrations in this project.

Content a participant reads is not stored in `src/`: the people and their messages live
in `data/scenario.json` and are written to the database by `seedScenarioFor()` when an
account is made.

## Accounts

Passwords are hashed with `password_hash()` and never stored or logged in any other
form. `requireLogin()` on the first line of a screen closes it for visitors who are not
logged in; `requireGuest()` keeps a logged-in visitor off the login and register screens.
The session id is regenerated on a successful login, and five wrong attempts close the
form for fifteen minutes.

Every POST form prints `<?= csrfField() ?>` and the screen that handles it checks
`isValidCsrf()` before it changes anything. A screen that changes something ends in
`redirect()`, so refreshing never submits twice.

## Language rule

Code is English: identifiers, comments, docblocks, commit messages, branch names,
file names, CSS class names, JSON keys, database tables and columns, console messages.

Dutch is what the participant reads: page copy, button labels, error messages shown
on screen, flash messages, the content of `data/scenario.json`, and everything in
`docs/`.

Internal keys stay English even when they represent something Dutch. A function returns
`answered_in_time` or `below_minimum`; the Dutch sentence that belongs to that key is
written on the screen that prints it. Never translate an internal key to Dutch and never
put a Dutch string in `src/`. That is why the menu labels sit in
`views/partials/site-nav.php` and the labels of the theme switch sit in data attributes
in `views/partials/site-footer.php`.

## Naming

| Thing | Convention | Example |
|---|---|---|
| Directory | lowercase, kebab-case | `src/support` |
| PHP function | camelCase | `scoreForAnswer()` |
| PHP class | PascalCase | `ScoreCalculator` |
| PHP constant | UPPER_SNAKE_CASE | `REWARD_THRESHOLD` |
| PHP variable | camelCase | `$secondsWaiting` |
| File with functions | kebab-case | `score-rules.php` |
| File with a class | PascalCase | `ScoreCalculator.php` |
| Page file | lowercase Dutch, no dashes | `noodcontacten.php` |
| Layout and partial | kebab-case | `contact-card.php` |
| Database table | plural, snake_case | `score_events` |
| Database column | snake_case | `respond_within_seconds` |
| CSS file | named after what it styles | `partials/contact-card.css` |
| CSS block | kebab-case | `.contact-card` |
| CSS element | double underscore | `.contact-card__name` |
| CSS modifier | double dash | `.contact-card--urgent` |
| CSS state class | `is-` prefix | `.is-open` |
| CSS custom property | `--group-name` | `--color-accent` |
| JS function and variable | camelCase | `startComponents` |
| JS module file | kebab-case | `nav-toggle.js` |
| JSON key | camelCase | `respondWithinSeconds` |
| Data attribute | kebab-case | `data-component` |

Page file names are Dutch because they become the URL a participant sees. Everything
else is English. Note the one seam: a column is `respond_within_seconds` and the same
value in JSON or PHP is `respondWithinSeconds`.

## Comments and documentation

Write a docblock on every exported thing: every function in `src/`, every function
exported from a JS module, every layout and every partial. The docblock says why the
thing exists and what a caller has to know, not what the next line does.

PHP: `/** */` with typed `@param` and `@return` only where the signature does not
already say it. Every layout and partial starts with a docblock listing its `@var`
inputs, because those arrive through `extract()` and an editor cannot infer them.

JavaScript: JSDoc on exported functions with `@param` and `@returns` including types,
since there is no TypeScript here. Internal helpers get a one-line description without
tags.

Every CSS file opens with a comment saying what it styles and which file it belongs to.

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
- Nothing has to be registered when you add a file to `src/` or a stylesheet to
  `assets/css/`. If a function is reported as undefined, the file is not in `src/` or the
  name is misspelled.

## Design system

All colour, type, spacing and radius values live in
`public/assets/css/base/tokens.css`. A literal hex code, px value or font name anywhere
else is a bug. Change the palette by editing tokens, never by overriding a token at the
point of use. A one-off size may become a token on the component itself, like
`--avatar-size` in `components/avatar.css`.

Tokens come in three layers. Primitives (`--mint-600`) are the raw palette and are only
used to build the layer above. Semantic tokens (`--color-accent`, `--color-surface`) are
what you write in a component. Light and dark share one set through `light-dark()`, so a
new colour needs both values in one line.

Stylesheets mirror the code, and every place has its own file:

```
base/        fonts, tokens, reset, elements, utilities. Loaded first, in that order.
layouts/     one file per layout in views/layouts/
partials/    one file per partial in views/partials/
components/  one file per component that more than one screen uses
pages/       one file per screen in public/, named after it, loaded only there
```

Nothing has to be registered: `stylesheets()` finds the files and prints them in that
order, and `pages/contacten.css` is loaded on `contacten.php` and nowhere else.

The direction is documented in `docs/04-designsysteem.md`: soft mint, white cards on a
pale green background, generous rounding, a hairline instead of a shadow. It looks
friendly on purpose, because a scoring app that looked threatening would not be
believable. Losing points is never red; it goes quiet and beige. Do not add gradients or
glassmorphism, and keep drop shadows out; a ring (`--ring-accent`) is the one exception.

Typefaces are Outfit for display (the wordmark, headings, the number) and Inter for
everything read as a sentence. Both are self-hosted in `public/assets/fonts/`; do not add
a font CDN and do not add a third typeface. Figures that change or line up get
`.numeric`.

The whole app is one column of `--width-app`, phone width, centred on a laptop. It reads
as an app; it is not a website that stretches. Test at 390px wide and at desktop width.

Accessibility floor: WCAG AA contrast, visible focus outline, labels on inputs, touch
targets of at least 44px, `prefers-reduced-motion` respected. Never remove a focus style.

## Front-end behaviour

`app.js` starts a module for every element with a `data-component` attribute:
`data-component="countdown"` loads `modules/countdown.js` and calls its `init(element)`.
Adding behaviour means adding a module and an attribute, never an import in `app.js` or a
script tag in the layout. A page without that attribute loads no module at all.

Behaviour is an enhancement. The server prints a correct page first: the countdown shows
the right value without JavaScript, and the menu is simply open.

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
- Never invent a path, a function, a CSS class or a column. Grep for it first.
- Run `composer lint` after touching PHP. Say plainly what you could not run.
- When something you were asked to do conflicts with this file, do it the way this file
  says and mention the conflict once.

## Definition of done

A change is done when the page it affects has been opened in a browser at
`http://localhost:8000` and behaves as intended, at phone width as well, `composer lint`
is clean, and the screen still reads as Dutch to a participant and as English in the
source.
