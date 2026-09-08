# AI in de browser gebruiken (ChatGPT, Gemini, Claude web)

Werk je in een editor met een agent (Claude Code, Copilot, Cursor, Gemini CLI), dan hoef
je niets te doen: die lezen `AGENTS.md`, `CLAUDE.md`, `GEMINI.md` of
`.github/copilot-instructions.md` vanzelf.

Werk je in de browser, dan weet het model niets van dit project. Plak dan het blok
hieronder eenmalig bovenin je gesprek, of zet het in ChatGPT bij Project instructions,
in Gemini bij Gems, en bij Claude in de Project-instructies. Daarna hoef je alleen nog
je vraag te stellen.

Plak er altijd het bestand bij dat je wilt laten aanpassen. Zonder het echte bestand
verzint het model paden en klassenamen die hier niet bestaan.

---

```text
You are working on Draagvlak: a PHP prototype for a speculative design project at
Hogeschool Rotterdam (CMGT). It shows a fictional 2038 app that turns social support
into a public score from 0 to 100. It is built to be user-tested, not shipped.

STACK
PHP 8.4 or newer, object-oriented, no framework and no router. MySQL through PDO, no ORM
and no query builder. Vanilla ES modules, no bundler, no JS libraries. CSS custom
properties, no preprocessor. Per-visit state in the PHP session, everything else in the
database. The only dependency is Laravel Pint, and it is require-dev. Never introduce a
framework, build step, CSS library or ORM.

LAYOUT - feature-first. Everything about one subject sits in one folder, and src/,
views/ and assets/css/ mirror each other.
src/Core/                  the foundation: App, Page, Action, Config, Paths
src/Core/Http              Request Response Session Csrf
src/Core/Data              Database JsonStore
src/Core/View              View Assets Html Url Format
src/Features/Auth          Account AccountRepository Guard LoginThrottle Registration
src/Features/Contacts      Contact ContactCode ContactRepository
src/Features/Messages      Message MessageRepository
src/Features/Score         ScoreRules ScoreBoard Outcome
src/Features/Scenario      Scenario, the starting content of the demo account
src/Features/<Name>/Pages  one class per screen of that feature
views/layout               app site-header site-nav site-footer failure
views/components           fragments any feature may use: notices, message-card
views/features/<name>      the templates of that feature
public/<screen>.php        one file per URL, four lines each
public/assets/css          base/ layout/ components/ features/<name>/
database/schema.sql, data/scenario.json, bootstrap.php, config.php

AUTOLOADING
bootstrap.php registers a PSR-4 autoloader for Draagvlak\ and returns the App, so
Composer is not required to run the app. Never add an autoload entry, never run composer
dump-autoload. A class works as soon as its folder matches its namespace and its file is
named after it. One class per file, final unless something extends it.

HOW A SCREEN WORKS - four files whose names follow from each other
public/contacts.php:
    $app = require __DIR__ . '/../bootstrap.php';
    (new ContactsPage($app))->handle();

src/Features/Contacts/Pages/ContactsPage.php:
    final class ContactsPage extends Page {
        protected function title(): string { return 'Contacten'; }
        protected function submit(): void {
            $this->requireValidCsrf('contacts');
            Response::redirect('contacts');
        }
        /** @return array<string, mixed> */
        protected function data(): array {
            return ['contacts' => $this->app->contacts->active($this->app->auth->id())];
        }
    }

views/features/contacts/contacts.php:
    <?php foreach ($contacts as $contact): ?>
        <p><?= $this->e($contact->name) ?></p>
    <?php endforeach; ?>

Page::handle() runs authorise(), then submit() on a POST, then data(), then the template
inside the layout. Nothing is configured: a page knows its feature from its namespace and
its screen name from its class name, so LoginPage in Features\Auth\Pages is /login.php,
prints views/features/auth/login.php and loads assets/css/features/auth/.
EmergencyContactsPage becomes emergency-contacts. authorise() defaults to requiring a
login. A screen that only redirects extends Action and has no template.

In a template $this is the View: $this->e(), $this->attributes(), $this->url(),
$this->asset(), $this->csrfField(), $this->partial('components/notices', [...]) with the
full path under views/. A template only prints.

SERVICES, reached through $this->app in a page
request, session, csrf, view, assets, config, database, auth, accounts, registration,
contacts, messages, scores, scenario. Everything is wired in App::__construct(); nothing
builds its own dependency and nothing reaches for a global.

DATABASE
MySQL through PDO. Four tables: users, contacts, messages, score_events. Every query
lives in a repository inside its feature, is a prepared statement with the values passed
separately - $this->database->all('... WHERE user_id = ?', [$userId]) - and never a value
inside the SQL string. Always look a row up together with the id of the logged-in user. A
repository returns typed objects (Account, Contact, Message), never raw rows. Tables are
plural snake_case, columns snake_case, the same value in PHP and JSON is camelCase.
Schema changes go in database/schema.sql followed by "composer db:fresh"; no migrations.
contacts.contact_user_id points at a real account when two people exchanged a code, and
is null for the scenario contacts, so reads join users with COALESCE.

ACCOUNTS
password_hash() and password_verify(), only inside AccountRepository. Session id
regenerated on login, five wrong attempts lock the form. Guard::requireLogin() in
authorise(). Every POST form prints $this->csrfField(), is handled behind
requireValidCsrf(), and ends in Response::redirect(). Registering asks for a name, an
address and one password; there is no repeat field. A new account starts with an empty
list and its own contact code (SAM-7QK4); only the demo account gets scenario content.

LANGUAGE
Code is English: identifiers, comments, docblocks, CSS classes, JSON keys, database
tables and columns, commits, console messages, file names and URLs. Dutch is only what
the participant reads and it lives in views/, plus data/scenario.json and docs/. The
single exception in src/ is the return of title() in a page class. A class returns a key
like 'answered_in_time'; the Dutch sentence for it is written in the template that prints
it, or in views/components/notices.php, which holds every flash sentence in one list.

NAMING
Namespace is Draagvlak\ plus the folders; folders in src/ are PascalCase, every other
folder is lowercase. Methods and variables camelCase, class constants typed
UPPER_SNAKE_CASE. Files in public/ and views/features/ are lowercase English kebab-case.
CSS BEM-style: .block, .block__element, .block--modifier, state classes .is-open. JS
camelCase in kebab-case files. JSON keys camelCase. Data attributes kebab-case.

COMMENTS - short, and only where they say something the code does not
One sentence on a class. A method gets a docblock only when the name and the signature do
not already say it; isPost(): bool gets nothing. Annotations wherever an editor cannot
infer a type: an array always gets a shape (array<string, mixed>, list<Contact>), @throws
for an exception a caller must handle, @var on every template for each variable it
receives, aligned in a column. Never "@param string $name Name.", never a comment that
repeats the next line, never history, a name or a date. Inline comments only where
correct code looks wrong, which here is almost always the scenario itself.

FORMATTING
Do not hand-format. pint.json owns PHP and .prettierrc owns CSS, JS and Markdown;
both run on save. Four spaces, LF, PSR-12, sorted imports, trailing commas, a blank line
before a return and before a block. What is left to the author: docblock alignment, and
where a blank line goes inside a method to separate one thought from the next.

DESIGN
Every colour, size, font and radius comes from a custom property in
public/assets/css/base/tokens.css. A literal hex code anywhere else is a bug. Tokens are
layered: primitives build semantic tokens (--color-surface, --color-accent), and light
and dark share one definition through light-dark(). Stylesheets mirror the code: base/,
layout/, components/, features/<name>/, and a screen loads base, layout, components and
its own feature. Soft mint, white cards on pale green, rounded corners, hairlines instead
of shadows; it looks friendly on purpose. Losing points is never red, it goes quiet and
beige. Outfit for display, Inter for reading, both self-hosted; numbers that change get
class="numeric". The app is one phone-width column, centred on a laptop. WCAG AA
contrast, 44px touch targets, reduced motion respected. One focus indicator and never a
second signal next to it: :focus-visible draws a single outline that follows the corner
the element already has, a component may move it with --focus-offset, and inputs have no
hover state at all.

JAVASCRIPT
app.js starts a module for every element with data-component="name", loading
modules/name.js and calling its init(element). Never add an import to app.js or a script
tag to the layout. A screen has to work without JavaScript; a control that would do
nothing is printed hidden and shown by its module. Dutch text a module needs comes in
through a data attribute.

GIT
Work happens on develop. Branch off develop, pull request into develop. main is the
branch a demo runs from.

CONTENT
The uncomfortable parts of the concept are the product. Do not soften copy, do not add
reassuring disclaimers, do not make the opt-out work. Never print anything that only
exists for the team: no demo credentials, no seeded strangers, no "test" on a screen.

HOW TO ANSWER
Give the complete changed file or an exact find-and-replace, not a description of the
change. Never invent a file path, class, method or CSS class; if you need to know whether
something exists, ask for that file. State in one line what you assumed.
```

---

## Wat je erbij plakt

| Vraag gaat over             | Plak dit erbij                                                |
| --------------------------- | ------------------------------------------------------------- |
| Een scherm                  | de vier bestanden van dat scherm                               |
| Hoe een scherm werkt        | `src/Core/Page.php` en `views/layout/app.php`                  |
| Wat een pagina kan bereiken | `src/Core/App.php`                                             |
| Styling                     | `base/tokens.css` en het bestand van dat onderdeel             |
| Een herhaald blokje         | het bestand uit `views/components/`                            |
| Een melding                 | `views/components/notices.php`                                 |
| De database                 | `database/schema.sql` en `src/Core/Data/Database.php`          |
| Contacten en codes          | `src/Features/Contacts/`                                       |
| Inloggen                    | `src/Features/Auth/`                                           |
| De regels van het scenario  | `src/Features/Score/ScoreRules.php`                            |
| De startinhoud              | `data/scenario.json`                                           |

## Waar je op moet letten

Een model dat dit blok niet heeft gehad, stelt bijna altijd Laravel, Eloquent of Tailwind
voor, of bouwt er een router en een controllerlaag bij. Dat is niet fout in het algemeen,
maar het is wel fout hier. Krijg je zo'n antwoord, dan is het blok niet meegestuurd.

Controleer altijd zelf of een voorgestelde klasse of methode echt bestaat voordat je hem
overneemt. Zoek de naam in de repo. Bestaat hij niet, dan is hij verzonnen.
