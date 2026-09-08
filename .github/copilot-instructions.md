# GitHub Copilot instructions

Read [AGENTS.md](../AGENTS.md) first. It holds every rule for this repository. This file
is what Copilot loads by itself, so it repeats the parts a completion gets wrong most
often, and nothing else.

## The stack, and what not to suggest

PHP 8.4, no framework and no router. MySQL through PDO, no ORM and no query builder.
Vanilla ES modules, no bundler. CSS custom properties, no preprocessor.

Do not suggest Laravel, Symfony, Eloquent, Doctrine, Tailwind, Bootstrap, jQuery, React,
a service container package or a migration tool. Do not suggest `composer require`.

## Where a suggestion belongs

Feature-first. Everything about one subject sits in one folder, and the three trees
mirror each other:

```
src/Core/                            the foundation: App, Page, Action, Http, Data, View
src/Features/<Name>/                 domain classes of that feature
src/Features/<Name>/Pages/           one class per screen
views/layout/                        app, site-header, site-nav, site-footer
views/components/                    fragments any feature may use
views/features/<name>/               the templates of that feature
public/<screen>.php                  the URL: four lines, runs a page class
public/assets/css/base|layout|components|features/<name>/
```

Namespace is `Draagvlak\` plus the folders, one class per file, file named after the
class, class `final` unless something extends it. The autoloader is in `bootstrap.php`;
nothing has to be registered.

File names and URLs are English and kebab-case: `emergency-contacts.php`.

## What a screen looks like

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

        Response::redirect('contacts');
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['contacts' => $this->app->contacts->active($this->app->auth->id())];
    }
}
```

`Page::handle()` runs `authorise()`, then `submit()` on a POST, then `data()`, then the
template inside the layout. A page knows its feature from its namespace and its screen
name from its class name; nothing is configured.

A template only prints, and `$this` in it is the `View`: `$this->e()`, `$this->url()`,
`$this->asset()`, `$this->csrfField()`, `$this->partial('features/home/score-block')`.

## Queries

Always a prepared statement, always in a repository inside the feature, always together
with the id of the logged-in user:

```php
$this->database->all('SELECT * FROM contacts WHERE user_id = ?', [$userId]);
```

Never a value inside the SQL string. A repository returns typed objects (`Account`,
`Contact`, `Message`), not raw rows.

## Language

Code, file names and URLs are English. Dutch is only what a participant reads, and it
lives in `views/`. A class returns a key like `answered_in_time`; the Dutch sentence for
it is written in the template that prints it, or in `views/components/notices.php`. The
only Dutch in `src/` is the return of `title()` in a page class.

## Comments

Short. One sentence on a class. A docblock on a method only when the name and the
signature do not say it. Annotations where an editor cannot infer a type: an `array`
always gets a shape (`array<string, mixed>`, `list<Contact>`), `@throws` for an exception
a caller must handle, `@var` on every template for each variable it receives. Never
`@param string $name Name.`, never a comment that repeats the next line, never a name, a
date or a history note.

## Styling

Every colour, size, font and radius comes from a custom property in
`public/assets/css/base/tokens.css`. A literal hex code or pixel value anywhere else is
a bug. BEM-style class names: `.block`, `.block__element`, `.block--modifier`,
`.is-open`. Never remove a focus style, never add a second one next to it, and never give
an input a hover state.

## Formatting

Do not hand-format. `pint.json` and `.prettierrc` own it, and both run on save.

## Commits

The commit message button is given [commit-instructions.md](commit-instructions.md).
That file holds the shape; do not repeat it here.

## Do not put team things on a screen

No demo credentials, no seeded strangers, no "test" in anything a participant can read.
A new account starts empty and gets a code to share.
