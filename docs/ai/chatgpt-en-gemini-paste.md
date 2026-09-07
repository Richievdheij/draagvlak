# AI in de browser gebruiken (ChatGPT, Gemini, Claude web)

Werk je in een editor met een agent (Claude Code, Copilot, Cursor, Gemini CLI), dan hoef
je niets te doen: die lezen `AGENTS.md`, `CLAUDE.md` of `GEMINI.md` vanzelf.

Werk je in de browser, dan weet het model niets van dit project. Plak dan het blok
hieronder eenmalig bovenin je gesprek, of zet het in ChatGPT bij Project instructions,
in Gemini bij Gems, en bij Claude in de Project-instructies. Daarna hoef je alleen nog
je vraag te stellen.

Plak er altijd het bestand bij dat je wilt laten aanpassen. Zonder het echte bestand
verzint het model paden en functienamen die hier niet bestaan.

---

```text
You are working on Draagvlak: a PHP prototype for a speculative design project at
Hogeschool Rotterdam (CMGT). It shows a fictional 2038 app that turns social support
into a public score from 0 to 100. It is built to be user-tested, not shipped.

STACK
PHP 8.4 or newer, no framework, no router. MySQL through PDO, no ORM and no query
builder. Vanilla ES modules, no bundler, no JS libraries. CSS custom properties, no
preprocessor. Per-visit state in the PHP session, everything else in the database.
Never introduce a framework, build step, CSS library or ORM.

LAYOUT (page-based, deliberately not MVC, every folder name lowercase)
public/            one PHP file per screen, the only web-reachable folder
public/assets/css  base/, layouts/, partials/, components/, pages/ - one file per place
public/assets/js   app.js entry plus modules/
public/assets/fonts self-hosted Outfit and Inter
src/support        config, escaping and URLs, session and flashes, request, formatting, the page template
src/data           the database connection and queries, the JSON reader, the seed
src/auth           registering, logging in, closing a screen
src/score          the rules of the scenario
src/contacts       the list of people
src/messages       the inbox and the response window
views/layouts      the document printed around a screen
views/partials     fragments used by the layout or by more than one page
database/          schema.sql
data/              scenario.json, the Dutch starting content
bootstrap.php      required by every page: paths, session, and loading everything in src/
config.php         database settings, overridden per machine by config.local.php

NO IMPORTS
bootstrap.php loads every PHP file in src/ automatically, in the global namespace.
Never write "use function", never add a namespace, never add an autoload entry, never
run composer dump-autoload. A new file in src/ works immediately, and a file in src/
only declares functions and constants.

HOW A PAGE LOOKS
<?php
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
requireLogin();
if (isPost()) { /* handle, then */ redirect('contacten'); }
$contacts = activeContacts(currentUserId());
page('Contacten');
?>
<section class="section">...</section>

page() captures the screen's output and views/layouts/app.php prints the document
around it when the script ends. There is no header include and no closing call.
Available helpers: e(), attributes(), asset(), url(), currentPage(), isCurrentPage(),
page(), partial(), flash(), redirect(), isPost(), input(), inputInt(), csrfField(),
isValidCsrf(), rememberForm(), takeForm(), sessionGet/Set/Forget/Reset(),
requireLogin(), requireGuest(), currentUser(), currentUserId(), dbAll(), dbFirst(),
dbValue(), dbRun(), dbInsert(), loadJson(), timeParts(), formatCountdown(),
formatPrice(), initial().

DATABASE
MySQL through PDO. Four tables: users, contacts, messages, score_events. Every query is
a prepared statement with the values passed separately - dbAll('... WHERE user_id = ?',
[$userId]) - and never a value inside the SQL string. Always look a row up together
with the id of the logged-in user. Tables are plural snake_case, columns snake_case,
while the same value in PHP and JSON is camelCase. Schema changes go in
database/schema.sql followed by "composer db:fresh"; there are no migrations.

ACCOUNTS
password_hash() and password_verify(), session_regenerate_id() on login, five wrong
attempts lock the form. requireLogin() on the first line of a screen. Every POST form
prints csrfField() and is handled behind isValidCsrf(), and ends in redirect().

LANGUAGE
Code is English: identifiers, comments, docblocks, CSS classes, JSON keys, database
tables and columns, commits, console messages. Dutch is only what the participant reads
on screen, plus data/scenario.json. Internal keys stay English even when they represent
Dutch concepts; a function returns 'answered_in_time' and the page writes the Dutch
sentence for it. Never put a Dutch string in src/.

NAMING
Folders lowercase kebab-case. PHP functions and variables camelCase, classes
PascalCase, constants UPPER_SNAKE_CASE. Function files kebab-case.php, class files
PascalCase.php, page files lowercase Dutch. CSS BEM-style: .block, .block__element,
.block--modifier, state classes .is-open. JS camelCase in kebab-case files. JSON keys
camelCase. Data attributes kebab-case. Tables plural snake_case, columns snake_case.

COMMENTS
Docblock on every function in src/, every exported JS function, every layout and every
partial, saying why it exists, not what the next line does. PHP uses /** */ with types
where the signature does not already say it; JS uses JSDoc with types because there is
no TypeScript. Every layout and partial opens with a docblock listing its @var inputs.
Inline comments only where a choice looks like a mistake and is not. No commented-out
code, no history in comments, no names or dates.

DESIGN
Every colour, size, font and radius comes from a custom property in
public/assets/css/base/tokens.css. A literal hex code anywhere else is a bug. Tokens are
layered: primitives build semantic tokens (--color-surface, --color-accent), and light
and dark share one definition through light-dark(). Stylesheets mirror the code, one
file per place: base/, layouts/, partials/, components/, pages/<screen>.css, all loaded
automatically. Soft mint, white cards on pale green, rounded corners, hairlines instead
of shadows; it looks friendly on purpose. Losing points is never red, it goes quiet and
beige. Outfit for display, Inter for reading, both self-hosted; numbers that change get
class="numeric". The app is one phone-width column, centred on a laptop. WCAG AA
contrast, visible focus, 44px touch targets, reduced motion respected.

JAVASCRIPT
app.js starts a module for every element with data-component="name", loading
modules/name.js and calling its init(element). Never add an import to app.js or a
script tag to the layout. A screen has to work without JavaScript.

GIT
Work happens on develop. Branch off develop, pull request into develop. main is the
branch a demo runs from.

CONTENT
The uncomfortable parts of the concept are the product. Do not soften copy, do not add
reassuring disclaimers, do not make the opt-out work.

HOW TO ANSWER
Give the complete changed file or an exact find-and-replace, not a description of the
change. Never invent a file path, function or CSS class; if you need to know whether
something exists, ask for that file. State in one line what you assumed.
```

---

## Wat je erbij plakt

| Vraag gaat over | Plak dit erbij |
| --- | --- |
| Een scherm | het bestand uit `public/` |
| Een helper | het bestand uit de juiste map in `src/` |
| Styling | `public/assets/css/base/tokens.css` en het bestand van dat onderdeel |
| Een herhaald blokje | het bestand uit `views/partials/` |
| De pagina-opbouw | `bootstrap.php` en `views/layouts/app.php` |
| De database | `database/schema.sql` en `src/data/database.php` |
| Inloggen | `src/auth/auth.php` |
| De startinhoud | `data/scenario.json` |

## Waar je op moet letten

Een model dat dit blok niet heeft gehad, stelt bijna altijd MVC, Laravel of Tailwind
voor, of zet een rij `use function`-regels bovenin je pagina. Dat is niet fout in het
algemeen, maar het is wel fout hier. Krijg je zo'n antwoord, dan is het blok niet
meegestuurd.

Controleer altijd zelf of een voorgestelde functie echt bestaat voordat je hem
overneemt. Zoek de naam in de repo. Bestaat hij niet, dan is hij verzonnen.
