---
name: draagvlak-style
description: "Use whenever writing or editing code in the Draagvlak prototype, before you type the first line. How comments, docblocks and annotations are written here: short, only where they add something, with the types an editor needs. Also what formatting is automatic and what is not, and what must never end up in a comment."
---

# How code is written here

Read `AGENTS.md` if you have not. This skill is about the shape of a file, not what it
does.

## Comments earn their place

A comment says something the code cannot. If it repeats the line under it, delete it.

**A class**: one sentence saying why it exists.

```php
/**
 * How often this browser may guess a password before the form closes.
 */
final readonly class LoginThrottle
```

A second sentence only when a rule of the scenario would otherwise be lost:

```php
/**
 * The rules of the scenario, in one place.
 *
 * These numbers are the concept, not a technical choice. Do not tune them to
 * make the prototype feel better: every session has to start the same.
 */
final class ScoreRules
```

**A method**: nothing when the name and the signature already say it.

```php
public function isPost(): bool          // no docblock
public function emailExists(string $email): bool   // no docblock
```

One line when there is a catch:

```php
/** Null when the contact is not this participant's. */
public function find(int $userId, int $contactId): ?Contact
```

More only when a caller would get it wrong otherwise:

```php
/**
 * The same list, with the clock started on everything that has a window.
 *
 * Reading this changes something on purpose: a response window counts from the
 * moment a participant sees the message. Call it once, and use open() elsewhere.
 *
 * @return list<Message>
 */
```

## Annotations, and why they are not optional

There is no static analysis in this project. Docblocks are the only thing your editor
has, so put a type where the signature cannot.

| Write it                            | When                                                    |
| ----------------------------------- | ------------------------------------------------------- |
| `@param array<string, mixed> $data` | Always for an `array`. `array` on its own says nothing. |
| `@return list<Contact>`             | Always for an `array`. Prefer returning an object.      |
| `@var array<string, string>`        | On a property whose type is an array.                   |
| `@throws RuntimeException When …`   | An exception a caller has to handle.                    |
| `@var Account $account`             | On every template, for each variable it receives.       |

Do not write `@param string $name Name of the account.` when the parameter is already
`string $name`. That is noise with a type on it.

A template lists its inputs, aligned in a column, because they arrive through `extract()`
and cannot be inferred:

```php
/**
 * Home: your number, then the people behind it.
 *
 * @var Account       $account  The visitor.
 * @var list<Message> $messages Messages still waiting on a decision.
 * @var int           $offerSecondsLeft Seconds the action price still lasts.
 */
```

## Inline comments

Only where correct code looks wrong. Here that is almost always the scenario itself:

```php
// Reading the inbox starts the clock on anything with a response window, so
// this line is the moment the pressure begins.
$messages = $this->app->messages->openForScreen($account->id);
```

```css
/* The way out is always the quietest button on the screen. That is not
   sloppiness, it is the subject of this prototype. */
```

Two lines at most. Without it, a teammate deletes the line because it looks like a bug.

## Never

- A comment that repeats the next line.
- History: "used to be", "since the test", "now that", "verified".
- A name, a date or the name of an AI tool.
- Commented-out code. Git remembers.
- A banner of dashes or equals signs to separate sections.
- An empty `@param` or a `@return void`.

## CSS and JavaScript

Every CSS file opens with one or two lines: what it styles, and which template it belongs
to.

```css
/*
 * One person on the contacts list.
 *
 * Belongs to views/features/contacts/contact-row.php.
 */
```

Every exported JavaScript function gets JSDoc with types, because there is no TypeScript.
Internal helpers get one line without tags.

```js
/**
 * The menu button that opens the navigation on a phone.
 *
 * @param {HTMLElement} button Button with aria-controls and aria-expanded.
 * @returns {void}
 */
export function init(button) {
```

## Formatting is not your job

```bash
composer format      # PHP, through .php-cs-fixer.php
npm run format       # CSS, JavaScript and Markdown, through .prettierrc
composer check       # syntax and formatting, before you push
```

Both run on save in VS Code. Do not hand-align anything they own: four spaces, LF, a
final newline, PSR-12, sorted imports, trailing commas, a blank line before a `return`
and before a block, one blank line between class members.

They deliberately leave two things to you:

- **How you align a docblock.** A column of `@var` types is readable; make it readable.
- **Where a blank line goes inside a method.** Group the lines that belong together and
  put a blank line between the groups. A method that reads as two or three short
  paragraphs beats one wall of statements.

```php
protected function submit(): void
{
    $this->requireValidCsrf('contacts');

    $account = $this->app->auth->user();

    $outcome = match ($this->app->request->input('action')) {
        'add' => $this->app->contacts->addByCode($account, $this->app->request->text('code')),
        'remove' => $this->app->contacts->remove($account->id, $this->app->request->inputInt('contactId')),
        default => null,
    };

    if ($outcome instanceof Outcome) {
        $this->app->session->flash($outcome->result, $outcome->values());
    }

    Response::redirect('contacts');
}
```

Guard first, then what you need, then the decision, then leave. Same shape every time.
