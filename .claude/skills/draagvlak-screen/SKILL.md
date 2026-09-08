---
name: draagvlak-screen
description: "Use when adding or changing a screen in the Draagvlak prototype: a new page, a form, a fragment, a flash message, or anything under public/, src/Features/*/Pages/ or views/. Covers the four files a screen is made of, the four steps a page runs through, and where Dutch is allowed."
---

# A screen in Draagvlak

Read `AGENTS.md` if you have not, and invoke `draagvlak-style` before you write. This is
the short version, aimed at doing the work.

## Four files, and their names follow from each other

Say you are adding "Aandacht kopen" to a new `Attention` feature.

```
public/attention.php                              the URL
src/Features/Attention/Pages/AttentionPage.php    what it does
views/features/attention/attention.php            what it looks like
public/assets/css/features/attention/attention.css   how it looks
```

Nothing is configured. `AttentionPage` in `Features\Attention\Pages` is `/attention.php`,
prints `views/features/attention/attention.php` and loads every stylesheet in
`assets/css/features/attention/`. A two-word class becomes kebab-case:
`EmergencyContactsPage` is `emergency-contacts`.

Does it belong to a feature that already exists? Then put it there instead of making a
new one. Only make a feature when the subject is genuinely new.

## 1. The URL

```php
<?php

declare(strict_types=1);

use Draagvlak\Features\Attention\Pages\AttentionPage;

/**
 * One sentence saying what this screen is for.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new AttentionPage($app))->handle();
```

Four lines. Never put logic here.

## 2. The page class

```php
<?php

declare(strict_types=1);

namespace Draagvlak\Features\Attention\Pages;

use Draagvlak\Core\Http\Response;
use Draagvlak\Core\Page;

/**
 * Why this screen exists, and what a participant is meant to feel on it.
 */
final class AttentionPage extends Page
{
    protected function title(): string
    {
        return 'Aandacht kopen';
    }

    protected function submit(): void
    {
        $this->requireValidCsrf('attention');

        // Change something.

        $this->app->session->flash('attention_bought', ['price' => '4,99']);

        Response::redirect('attention');
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['contacts' => $this->app->contacts->active($this->app->auth->id())];
    }
}
```

`handle()` always runs `authorise()`, then `submit()` on a POST, then `data()`, then the
template inside the layout. Override only what you need:

- `authorise()` already requires a login. Override it with `requireGuest()` for a screen
  a logged-in visitor should not see.
- `nav()` and `description()` are there when a screen wants a different menu item or a
  meta description.
- A screen that only redirects extends `Action` instead and has no template.

The title is the one Dutch string allowed in `src/`. Everything else a participant reads
is in `views/`.

## 3. The template

`$this` is the `View`. Everything else comes in through `data()`.

```php
<?php

declare(strict_types=1);

use Draagvlak\Features\Contacts\Contact;

/**
 * What this screen shows, in one sentence.
 *
 * @var list<Contact> $contacts Everyone still on the list.
 */

?>
<section class="section">
    <h1>Aandacht kopen</h1>

    <?php foreach ($contacts as $contact): ?>
        <p><?= $this->e($contact->name) ?></p>
    <?php endforeach; ?>
</section>
```

Available on `$this`: `e()`, `attributes()`, `url()`, `asset()`, `csrfField()`,
`partial()`. `partial()` takes the full path under `views/`, so you always see which file
you get:

```php
$this->partial('components/message-card', ['message' => $message]);
$this->partial('features/attention/price-row', ['cents' => $cents]);
```

A template only prints. The moment you find yourself counting, comparing or formatting,
move it to `data()` or to a method on the object you are printing.

A fragment used by one feature lives in that feature. One used by two lives in
`views/components/`.

## 4. The stylesheet

Anything in `assets/css/features/attention/` is loaded on that feature's screens and
nowhere else. Give the file the name of what it styles, not of the screen it happens to
be on today.

Check `components/` first: `btn`, `card`, `field`, `notice`, `avatar`, `message-card`,
`timer`. If it exists, use that class.

Never type a colour. `var(--color-accent)`, not `#2f7d5e`.

## Forms

POST, a token, handled at the top, ending in a redirect.

```php
<form method="post" action="<?= $this->e($this->url('attention')) ?>">
    <?= $this->csrfField() ?>

    <div class="field">
        <label class="field__label" for="amount">Bedrag</label>
        <input class="field__input" type="text" id="amount" name="amount">
    </div>

    <button class="btn" type="submit">Betalen</button>
</form>
```

Reading it back: `$this->app->request->text('amount')` or `->inputInt('amount')`.

A form that fails validation calls `$this->app->session->rememberForm($values, $errors)`
before the redirect, and the next `data()` picks it up with `takeForm()`. The errors are
English keys; the Dutch for them is a small map at the top of the template.

## Flash messages

A page queues a key, never a sentence:

```php
$this->app->session->flash('attention_bought', ['price' => '4,99']);
```

The Dutch lives in `views/components/notices.php`, in one list. Add a line there with a
tone (`info`, `success`, `loss`, `danger`), an optional title, and a text with
`{placeholders}` for the values. A key without a line prints nothing at all, so add both
in the same change.

## Menu

`views/layout/site-nav.php`, in the array at the top. The key is the screen name, the
value is the Dutch label.

## Checks

```bash
composer check
composer start
```

Open the screen at `http://localhost:8000`, and put the browser in device view at 390px.
`composer check` only says the syntax and the formatting are right.
