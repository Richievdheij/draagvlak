# AGENTS.md

Instructions for any AI assistant working in this repository. This file is the single
source of truth. `CLAUDE.md`, `GEMINI.md` and `.github/copilot-instructions.md` point
here and only add tool-specific notes.

Human teammates should read `docs/` instead. Those are in Dutch and explain the same
rules in plain language.

## What this project is

Draagvlak is a speculative design prototype for a CMGT future scenario at Hogeschool
Rotterdam. It shows a 2038 app that turns social support into a public number between
0 and 100. The build exists to be tested on people, not to be shipped.

Two consequences that decide most judgement calls:

- Screens must feel finished enough that a participant forgets it is a prototype. That
  includes never printing anything that only exists for the team: no demo credentials,
  no seeded strangers, no "test" anywhere a participant can see it.
- The uncomfortable parts of the concept are the product. Never soften a screen to be
  friendlier, never add a reassuring disclaimer, never make the opt-out work.

Read `docs/01-project-en-scenario.md` before changing copy, and
`docs/06-onderzoek-en-inzichten.md` before changing anything that came out of user
research. Those two files carry decisions that should not be re-derived.

The home screen, contacts, logging in and registering are built. Every other screen has
a page class, a template and a stylesheet, all empty and waiting for content.

## Stack

PHP 8.4 or newer, no framework and no router. MySQL through PDO, no ORM and no query
builder. Vanilla ES modules, no bundler, no npm runtime dependencies. CSS with custom
properties, no preprocessor. State for one visit lives in the PHP session; everything
that has to survive a refresh lives in the database.

Classes are found by a PSR-4 autoloader written out in `bootstrap.php`, so a checkout
without `composer install` still works. `composer.json` declares the same mapping for
editors. There is never a list of files to maintain and never a `composer dump-autoload`.

The one dependency is `laravel/pint`, and it is `require-dev`: the app does not run on
it. Pint ships as one compiled binary, so `vendor/` holds a single package. Do not add a
framework, a build step, a CSS library, a JS library or an ORM. If a task seems to need
one, say so in one sentence and solve it without.

PHP is 8.4 and `composer.json` pins `^8.4`, so 8.5 works and 9.0 cannot slip in. Node 24
is only needed for the formatters. Nothing depends on where the project sits on disk or
what the folder is called: every path is derived from the file it is written in.

## Running it

Laravel Herd serves it at `http://draagvlak.test`. Herd picks `public/` as the document
root by itself, because `BasicWithPublicValetDriver` matches any project with a `public/`
folder, so no Valet driver has to be written. The site is pinned with `herd isolate 8.4`.

Without Herd, `composer start` serves the same thing on `http://localhost:8000`.

MySQL is reached over TCP on `127.0.0.1:3306`, never over a socket: a socket path differs
per machine and per installer. That is the same server `http://phpmyadmin.test` talks to.

`.mcp.json` registers two MCP servers for this project: `draagvlak-files` (the files in
this folder, relative path so it works anywhere) and `herd` (sites and PHP versions).

## Repository layout

Feature-first. Everything that belongs to one subject sits in one folder, in `src/`, in
`views/` and in `assets/css/` alike, and those three mirror each other exactly.

```
src/
  Core/                     The foundation. You rarely open it.
    App.php                 Every service, wired together once
    Page.php  Action.php    The two base classes a screen extends
    Config.php  Paths.php
    Http/                   Request Response Session Csrf
    Data/                   Database JsonStore
    View/                   View Assets Html Url Format
  Features/<Name>/          One folder per subject
    <domain classes>        Entities, repositories, rules
    Pages/                  One class per screen of this feature
views/
  layout/                   app site-header site-nav site-footer failure
  components/               Fragments any feature may use
  features/<name>/          The templates of that feature
public/
  <screen>.php              One file per URL. Four lines each.
  assets/css/
    base/                   fonts tokens reset elements utilities
    layout/                 One file per file in views/layout/
    components/             One file per file in views/components/
    features/<name>/        The stylesheets of that feature
  assets/js/                app.js plus modules/
  assets/fonts/             Self-hosted Outfit and Inter
database/schema.sql         The structure of the database
bin/                        Command line scripts, run through composer
data/scenario.json          The Dutch starting content of the demo account
docs/                       Dutch documentation for the team
bootstrap.php               Autoloader, then App::boot(). Returns the App.
config.php                  Settings. Overridden per machine by config.local.php.
```

The features today are `Auth`, `Contacts`, `Messages`, `Score`, `Home`, `Settings`,
`Screening`, `Reset` and `Scenario`.

Placement is correctness, not taste: a file in the wrong folder is a defect.

- Something that belongs to one subject goes in that feature, all of it.
- Something two features need goes in `components/`, or in `Core/` when it is plumbing.
- A class in `src/` declares only. Nothing there runs on its own at load time.
- A template only prints. A calculation in a template belongs in the page class.
- Never write a `.php` file into `public/` that is not a URL a person can open.

## How a screen works

Four files, and their names follow from each other.

```
public/contacts.php                        the URL
src/Features/Contacts/Pages/ContactsPage.php   what it does
views/features/contacts/contacts.php       what it looks like
public/assets/css/features/contacts/*.css  how it looks
```

The file in `public/` is the URL and nothing else:

```php
<?php

declare(strict_types=1);

use Draagvlak\Features\Contacts\Pages\ContactsPage;

/**
 * One sentence saying what this screen is for.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new ContactsPage($app))->handle();
```

The page class decides everything:

```php
final class ContactsPage extends Page
{
    protected function title(): string
    {
        return 'Contacten';
    }

    protected function submit(): void
    {
        $this->requireValidCsrf('contacts');

        // Change something, then always redirect.
        Response::redirect('contacts');
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['contacts' => $this->app->contacts->active($this->app->auth->id())];
    }
}
```

The template prints, and only prints:

```php
<?php foreach ($contacts as $contact): ?>
    <p><?= $this->e($contact->name) ?></p>
<?php endforeach; ?>
```

`Page::handle()` always runs the same four steps in the same order: `authorise()`,
`submit()` when the request is a POST, `data()`, then the template inside the layout.
That order is the reason a template never has to work anything out.

**Nothing is configured.** A page knows its feature from its namespace and its name from
its class name, so `LoginPage` in `Features\Auth\Pages` is `/login.php`, prints
`views/features/auth/login.php` and gets the stylesheets in
`assets/css/features/auth/`. `EmergencyContactsPage` becomes `emergency-contacts`.

Override only what your screen needs. `authorise()` defaults to requiring a login,
`nav()` defaults to the screen itself, `submit()` does nothing, `data()` returns nothing.
A screen that only redirects (logging out) extends `Action` instead and has no template.

In a template `$this` is the `View`: `$this->e()`, `$this->attributes()`, `$this->url()`,
`$this->asset()`, `$this->csrfField()` and `$this->partial()`. `partial()` takes the path
under `views/` in full, so you can always see which file you are looking at:

```php
$this->partial('components/notices', ['flashes' => $flashes]);
$this->partial('features/home/score-block', ['account' => $account]);
```

Read the class in `src/` before using a method; do not guess at a signature.

## Services

`bootstrap.php` returns the `App`. A page reaches everything through it.

| `$this->app->…` | What it is                                             |
| --------------- | ------------------------------------------------------ |
| `request`       | Method, submitted values, which screen this is         |
| `session`       | Per-visit state, flash messages, a form that came back |
| `csrf`          | The form token                                         |
| `view`          | Rendering a template                                   |
| `assets`        | Asset URLs and the stylesheets of a feature            |
| `config`        | Settings from config.php                               |
| `database`      | Prepared statements, nothing else                      |
| `auth`          | Who is logged in, and the guards                       |
| `accounts`      | The users table, including passwords and contact codes |
| `registration`  | Checking the registration form                         |
| `contacts`      | The list, adding by code, removing                     |
| `messages`      | The inbox and what answering costs                     |
| `scores`        | Changing a number and writing down why                 |
| `scenario`      | The starting content, for the demo account             |

Nothing constructs its own dependencies halfway down a method, and nothing reaches for a
global. Add a service by adding a property and a line in `App::__construct()`, where the
whole graph is visible in one screenful.

## The database

MySQL, reached through PDO. `composer db:setup` creates the database, the tables from
`database/schema.sql` and the demo content; `composer db:fresh` drops it and builds it
again, which is how a test session is reset.

Every query is a prepared statement with the values passed separately. A value never
goes into the SQL string, not even one you typed yourself:

```php
$this->database->all('SELECT * FROM contacts WHERE user_id = ?', [$userId]);   // right
$this->database->all("SELECT * FROM contacts WHERE user_id = $userId");        // wrong
```

Always look a row up together with the id of the visitor who owns it, otherwise a
changed number in the URL reaches someone else's data.

SQL lives in a repository inside its feature, never in a page class and never in a
template. A repository hands back typed objects (`Account`, `Contact`, `Message`), not
raw rows.

Four tables: `users`, `contacts`, `messages`, `score_events`. A change to a score is
written to `score_events` as well as to the number itself, so a researcher can read back
a whole session. Add a column by editing `database/schema.sql` and running
`composer db:fresh`; there are no migrations in this project.

`contacts.contact_user_id` is the seam between the two kinds of contact. It points at a
real account when two people exchanged a code, and is null for the people from
`data/scenario.json`, who belong to nobody. A query reads the name and the number of a
linked contact from the account, so both sides always see the same number.

## Accounts

Passwords are hashed with `password_hash()` and never leave `AccountRepository` in any
other form. `Guard::requireLogin()` closes a screen for visitors who are not logged in;
`requireGuest()` keeps a logged-in visitor off the login and register screens. The
session id is regenerated on a successful login, and five wrong attempts close the form
for fifteen minutes.

Registering asks for a name, an address and one password. There is no second password
field: repeating a password does not catch the typo that matters and is one more thing
between somebody and their account.

A new account starts empty. It gets a contact code of its own (`SAM-7QK4`), and somebody
who has that code can add them; the link is made both ways at once. Nobody is put on a
list for them. Only the demo account is given the content of `data/scenario.json`.

That demo account is a fixture, not a person. `src/Features/Scenario/DemoSeeder.php` holds
its address, password and contact code as constants, so they survive every `db:fresh` and
the README can name them. Its password is written down there, which is why `app.demo` in
`config.php` exists: set it to `false` in `config.local.php` and `composer db:setup` stops
after the tables. Never seed it from anywhere else, and never print it on a screen.

Every POST form prints `<?= $this->csrfField() ?>` and the page that handles it calls
`requireValidCsrf()` before it changes anything. A page that changes something ends in
`Response::redirect()`, so refreshing never submits twice.

## Language rule

Code is English: identifiers, comments, docblocks, commit messages, branch names,
file names, CSS class names, JSON keys, database tables and columns, console messages.
URLs are English too, because a file in `public/` is its own URL.

Dutch is what the participant reads, and it lives in `views/`: page copy, button labels,
error messages on screen, flash messages, and the content of `data/scenario.json`.
`docs/` is Dutch too.

There is exactly one Dutch string in `src/`: the return of `title()` in a page class,
because that is the name of the screen. Everything else that a participant reads is in a
template. A class returns a key like `answered_in_time` or `below_minimum`, and the
Dutch sentence that belongs to it is written where it is printed.

That is why:

- flash messages are queued as a key with values, and every sentence they can produce
  lives in `views/components/notices.php`;
- form errors come back as keys, and the Dutch for them sits in the screen's template;
- the menu labels sit in `views/layout/site-nav.php`;
- the labels of the theme switch and the copy button sit in data attributes, so nothing
  Dutch ends up in a JavaScript module.

Never translate an internal key to Dutch, and never put a Dutch sentence in `src/`.

## Naming

| Thing                         | Convention                                    | Example                               |
| ----------------------------- | --------------------------------------------- | ------------------------------------- |
| Namespace                     | `Draagvlak\` plus the folders                 | `Draagvlak\Features\Contacts`         |
| Folder in `src/`              | PascalCase, matching the namespace            | `src/Features/Auth/Pages`             |
| Any other folder              | lowercase, kebab-case                         | `views/features/contacts`             |
| Class file                    | PascalCase, one class per file                | `ContactRepository.php`               |
| PHP class                     | PascalCase, `final` unless it is a base class | `ScoreBoard`                          |
| PHP method and variable       | camelCase                                     | `activeContacts()`, `$secondsWaiting` |
| PHP class constant            | UPPER_SNAKE_CASE, typed                       | `public const int REWARD_THRESHOLD`   |
| Page class                    | what the screen is, plus `Page`               | `EmergencyContactsPage`               |
| URL, entry file, template     | lowercase English, kebab-case                 | `emergency-contacts.php`              |
| Layout and component fragment | kebab-case                                    | `message-card.php`                    |
| Database table                | plural, snake_case                            | `score_events`                        |
| Database column               | snake_case                                    | `respond_within_seconds`              |
| CSS file                      | named after what it styles                    | `components/message-card.css`         |
| CSS block                     | kebab-case                                    | `.message-card`                       |
| CSS element                   | double underscore                             | `.message-card__name`                 |
| CSS modifier                  | double dash                                   | `.message-card--urgent`               |
| CSS state class               | `is-` prefix                                  | `.is-open`                            |
| CSS custom property           | `--group-name`                                | `--color-accent`                      |
| JS function and variable      | camelCase                                     | `startComponents`                     |
| JS module file                | kebab-case                                    | `nav-toggle.js`                       |
| JSON key                      | camelCase                                     | `respondWithinSeconds`                |
| Data attribute                | kebab-case                                    | `data-component`                      |

Note the one seam: a column is `respond_within_seconds` and the same value in JSON or PHP
is `respondWithinSeconds`.

A class is `final` unless something is meant to extend it; today only `Page` and `Action`
are not. Properties are `private readonly` unless a template needs them, and a value
object that never changes is a `final readonly class`.

`static` is for something that depends on nothing and therefore always gives the same
answer: escaping, building a URL, formatting a price, a rule of the scenario. Anything
holding a connection, a session or a visitor is a normal object built in `App`.

## Comments and documentation

Short. A comment earns its place by saying something the code cannot.

**A class** gets one sentence saying why it exists. A second only when a rule of the
scenario would otherwise be lost.

**A method** gets a docblock when the name and the signature do not already say it, or
when an annotation is needed. `isPost(): bool` needs nothing.

**Annotations**, and this is the part that matters, because there is no static analysis
here and an editor has nothing else to go on:

- `@param` and `@return` only where the type does not already say it. An `array` always
  gets a shape (`array<string, mixed>`, `list<Contact>`, `array{values: ...}`), or you
  return an object instead.
- `@throws` for an exception a caller has to handle.
- `@var` on a property whose type is an array, and on every template, listing its inputs.
  Those arrive through `extract()` and cannot be inferred.

**A template** starts with one sentence and its `@var` list, aligned in a column.

**An inline comment** only where correct code looks wrong. In this project that is almost
always the scenario: a disabled toggle, a grey escape button, a penalty that keeps
running. Two lines at most.

**Never**: a comment that repeats the next line, a history note ("used to be", "now
that", "verified"), a name, a date, an AI tool, a commented-out block, or a banner made
of dashes. `@param string $name Name.` is worse than no annotation at all.

Every CSS file opens with one or two lines saying what it styles and which template it
belongs to. Every exported JavaScript function gets JSDoc with types, because there is no
TypeScript; internal helpers get a single line without tags.

## Formatting

Do not hand-format. `composer format` runs Pint with `pint.json`, and `npm run format`
runs Prettier with `.prettierrc` for CSS, JavaScript and Markdown. Both run on save in VS
Code through `.vscode/settings.json`. Pint reads every `.php` file in the project, so a
new folder needs no registration either.

`pint.json` is Pint's `psr12` preset plus the rules a team argues about otherwise. It is
JSON, so it cannot explain itself; the sentence under it has to stay true instead.

What they enforce: four spaces, LF, a final newline, no trailing whitespace, PSR-12,
sorted imports with none unused, a blank line before `return` and before a block, one
blank line between class members, trailing commas in multi-line calls, and a leading `\`
on the global functions PHP can optimise.

What they leave to you: how you align a docblock, and where you put a blank line inside a
method to separate one thought from the next. Use them. A method that reads as three
short paragraphs is easier than one wall of statements.

Before you push, `composer check` runs the syntax check and the format check for PHP, and
`npm run check` does the same for CSS, JavaScript and Markdown.

## Editor support

- Type every parameter, return value and property. `mixed` only where the value
  genuinely is.
- Give an `array` a shape in the docblock, or return an object instead.
- Do not declare a function or a class in a template.
- No `@phpstan-ignore`, no `eslint-disable`, no silencing with `@`.
- Nothing has to be registered when you add a class or a stylesheet. If a class is
  reported as undefined, its folder does not match its namespace or the file is not named
  after the class.

## Design system

All colour, type, spacing and radius values live in
`public/assets/css/base/tokens.css`. A literal hex code, px value or font name anywhere
else is a bug. Change the palette by editing tokens, never by overriding a token at the
point of use. A one-off size may become a token on the component itself, like
`--avatar-size` or `--field-height`.

Tokens come in three layers. Primitives (`--mint-600`) are the raw palette and are only
used to build the layer above. Semantic tokens (`--color-accent`, `--color-surface`) are
what you write in a component. Light and dark share one set through `light-dark()`, so a
new colour needs both values in one line.

Stylesheets mirror the code:

```
base/        Loaded first, in the order fonts, tokens, reset, elements, utilities
layout/      One file per file in views/layout/
components/  One file per file in views/components/
features/<name>/  Everything for one feature, loaded on that feature's screens only
```

Nothing has to be registered: `Assets::stylesheets()` finds the files and prints them in
that order. Used on one screen means it belongs to that feature; used by two features
means it is a component.

The direction is documented in `docs/04-designsysteem.md`: soft mint, white cards on a
pale green background, generous rounding, a hairline instead of a shadow. It looks
friendly on purpose, because a scoring app that looked threatening would not be
believable. Losing points is never red; it goes quiet and beige. Do not add gradients or
glassmorphism, and keep drop shadows out; a ring (`--ring-accent`) is the one exception.

Typefaces are Outfit for display and Inter for everything read as a sentence. Both are
self-hosted; do not add a font CDN and do not add a third typeface. Figures that change
or line up get `.numeric`.

The whole app is one column of `--width-app`, phone width, centred on a laptop. Test at
390px wide and at desktop width.

Accessibility floor: WCAG AA contrast, labels on inputs, touch targets of at least 44px,
`prefers-reduced-motion` respected, and a focus outline that is always visible.

One focus indicator and no more. `:focus-visible` in `base/elements.css` draws a single
outline that follows the corner the element already has; a component may move it with
`--focus-offset` but never remove it, and never add a second signal such as a border that
also changes colour. An input does not change on hover either: a field that reacts to the
mouse passing over it reads as a bug.

## Front-end behaviour

`app.js` starts a module for every element with a `data-component` attribute:
`data-component="countdown"` loads `modules/countdown.js` and calls its `init(element)`.
Adding behaviour means adding a module and an attribute, never an import in `app.js` or a
script tag in the layout.

Behaviour is an enhancement. The server prints a correct page first: the countdown shows
the right value without JavaScript, the menu is simply open, and a control that would do
nothing without a module is printed `hidden` and shown by that module. Dutch text a
module needs comes in through a data attribute.

## Which file your tool reads

Every one of these points at this file and adds only what is specific to the tool. Keep
them true: a rule that is written down twice will drift.

| Tool                                   | Reads                                                             |
| -------------------------------------- | ----------------------------------------------------------------- |
| Claude Code                            | `CLAUDE.md` and `AGENTS.md`, plus the skills in `.claude/skills/` |
| GitHub Copilot                         | `.github/copilot-instructions.md`                                 |
| Gemini CLI                             | `GEMINI.md`                                                       |
| Cursor, Codex and other agents         | `AGENTS.md`                                                       |
| ChatGPT, Gemini or Claude in a browser | nothing; paste `docs/ai/chatgpt-en-gemini-paste.md`               |
| The commit message button in VS Code   | `.github/commit-instructions.md`                                  |

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

A commit message is `type(scope): short title`, a blank line, then a body that says what
changed and why. The types, the scopes and what never belongs in a message are in
`.github/commit-instructions.md`, which is also what the commit message button in VS Code
is handed. Nothing enforces it; a message that ignores it is not rejected.

## Working rules

- Implement the change. Do not hand back a description of a change that was asked for.
- Read the file before you edit it. Prefer the file to your memory of it.
- Make the smallest change that does the job. Do not reformat, rename or restructure
  files you were not asked about.
- Never invent a path, a class, a method, a CSS class or a column. Grep for it first.
- Run `composer check` after touching PHP, and `npm run check` after touching CSS,
  JavaScript or Markdown. Say plainly what you could not run.
- When something you were asked to do conflicts with this file, do it the way this file
  says and mention the conflict once.

## Definition of done

A change is done when the screen it affects has been opened in a browser at
`http://localhost:8000` and behaves as intended, at phone width as well, `composer check`
is clean, and the screen still reads as Dutch to a participant and as English in the
source.
