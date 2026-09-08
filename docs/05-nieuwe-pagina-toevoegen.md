# Een nieuw scherm toevoegen

De schermen uit het scenario staan er al: `conversation`, `emergency-contacts`,
`settings`, `check`, `score-bureau` en `reset`. Ze hebben hun eigen klasse, template en
stylesheet, ze staan in het menu, en ze wachten op inhoud. Open er een en begin.

Wil je er zelf een bij maken, bijvoorbeeld "Aandacht kopen", dan is dat vier bestanden
waarvan de namen uit elkaar volgen.

## Waar hoort het?

Eerst deze vraag: past je scherm bij een onderwerp dat al bestaat? `Auth`, `Contacts`,
`Messages`, `Score`, `Home`, `Settings`, `Screening` of `Reset`? Zet het er dan bij. Maak
alleen een nieuwe feature als het onderwerp echt nieuw is.

Zeg dat "Aandacht kopen" nieuw is. Dan wordt het:

```
public/attention.php                                 de URL
src/Features/Attention/Pages/AttentionPage.php       wat het doet
views/features/attention/attention.php               hoe het eruitziet
public/assets/css/features/attention/attention.css   hoe het is opgemaakt
```

Er valt niets in te stellen. `AttentionPage` in `Features\Attention\Pages` is
`/attention.php`, print `views/features/attention/attention.php` en laadt alles uit
`assets/css/features/attention/`. Een klassenaam van twee woorden wordt kebab-case:
`EmergencyContactsPage` is `emergency-contacts`.

## 1. De URL

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

Vier regels. Hier komt nooit logica te staan.

## 2. De klasse

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

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['contacts' => $this->app->contacts->active($this->app->auth->id())];
    }
}
```

`handle()` draait vier stappen: `authorise()`, dan `submit()` als het een POST is, dan
`data()`, dan de template binnen de layout. Je overschrijft alleen wat je nodig hebt:

- `authorise()` vraagt standaard om een login. Alleen het inlog- en registratiescherm
  draaien dat om met `requireGuest()`.
- `nav()` en `description()` zijn er als je een ander menu-item wilt laten oplichten of
  een meta description wilt meegeven.
- Een scherm dat alleen doorstuurt erft van `Action` en heeft helemaal geen template.

De titel is de enige Nederlandse tekst die in `src/` mag staan.

## 3. De template

```php
<?php

declare(strict_types=1);

use Draagvlak\Features\Contacts\Contact;

/**
 * Wat dit scherm laat zien, in één zin.
 *
 * @var list<Contact> $contacts Iedereen die nog in de lijst staat.
 */

?>
<section class="section">
    <h1>Aandacht kopen</h1>

    <p>Je hebt <span class="numeric"><?= $this->e(count($contacts)) ?></span> contacten.</p>
</section>
```

In een template is `$this` de `View`: `$this->e()` om te escapen, plus
`$this->attributes()`, `$this->url()`, `$this->asset()`, `$this->csrfField()` en
`$this->partial()`. Alles wat je verder nodig hebt komt binnen via `data()`.

Bij `partial()` schrijf je het hele pad uit, zodat je altijd ziet welk bestand je krijgt:

```php
$this->partial('components/message-card', ['message' => $message]);
$this->partial('features/attention/price-row', ['cents' => $cents]);
```

Zodra je tussen twee tags staat te rekenen, vergelijken of opmaken, hoort dat in `data()`
of in een methode op het object dat je print.

## 4. De stylesheet

Alles in `assets/css/features/attention/` wordt op de schermen van die feature geladen en
nergens anders. Geef het bestand de naam van wat het opmaakt.

Kijk eerst of het blok dat je nodig hebt al bestaat in `components/`: `btn`, `card`,
`field`, `notice`, `avatar`, `message-card`, `timer`. Bestaat het al, dan gebruik je die
klasse.

Losse kleuren typ je nooit. Gebruik `var(--color-accent)` en niet `#2f7d5e`.

## In het menu

`views/layout/site-nav.php`, in de array bovenin:

```php
$items = [
    'home' => 'Start',
    'contacts' => 'Contacten',
    'attention' => 'Aandacht',
];
```

De sleutel is de naam van het scherm, de waarde is het Nederlandse label. Hoort je scherm
er niet in, link er dan vanaf een ander scherm naartoe met
`<a href="<?= $this->e($this->url('attention')) ?>">`.

## Een formulier erbij

POST, een token, afhandelen in `submit()`, en eindigen met een redirect zodat vernieuwen
niets opnieuw verstuurt.

```php
protected function submit(): void
{
    $this->requireValidCsrf('attention');

    $amount = $this->app->request->inputInt('amount');

    // Doe hier wat er moet gebeuren.

    $this->app->session->flash('attention_bought', ['price' => '4,99']);

    Response::redirect('attention');
}
```

```html
<form method="post" action="<?= $this->e($this->url('attention')) ?>">
    <?= $this->csrfField() ?>

    <div class="field">
        <label class="field__label" for="amount">Bedrag</label>
        <input class="field__input" type="text" id="amount" name="amount">
    </div>

    <button class="btn" type="submit">Betalen</button>
</form>
```

De melding uit `flash()` verschijnt vanzelf bovenaan de volgende pagina. Let op: je geeft
een **sleutel** mee en geen zin. De Nederlandse tekst zet je erbij in
`views/components/notices.php`:

```php
'attention_bought' => [
    'tone' => 'success',
    'title' => 'Betaald',
    'text' => 'Je hebt {price} betaald voor extra aandacht.',
],
```

Zonder die regel wordt er niets geprint. Voeg ze dus in dezelfde wijziging toe. Tonen kan
met `info`, `success`, `loss` of `danger`.

Moet een formulier terugkomen met wat er al ingevuld stond, gebruik dan
`$this->app->session->rememberForm($values, $errors)` vlak voor de redirect, en
`takeForm()` in `data()`. Zo doet `RegisterPage` het.

## Controleren

```bash
composer check
composer start
```

Open `http://localhost:8000/attention.php`, en zet de browser in apparaatweergave op een
telefoon. Klopt het daar en op je laptop, dan ben je klaar.

---

# Een klasse toevoegen

Stel je wilt uitrekenen wat een abonnement je per maand aan punten oplevert.

## 1. Kies de juiste map

Gaat het over de score, dan `src/Features/Score/`. Over contacten, `Contacts/`. Over
berichten, `Messages/`. Over accounts en inloggen, `Auth/`. Over de startinhoud,
`Scenario/`.

Is het leidingwerk waar elke feature op leunt (de database, de sessie, het request,
opmaak, templates), dan `src/Core/`. Maar dat komt bijna nooit voor: het fundament is af.

De mapnaam is de namespace. `src/Features/Score/PlusOffer.php` wordt
`Draagvlak\Features\Score\PlusOffer`.

## 2. Schrijf de klasse

```php
<?php

declare(strict_types=1);

namespace Draagvlak\Features\Score;

/**
 * What the paid subscription promises per month.
 *
 * The number is marketing, not a calculation: the screen has to sell something.
 */
final class PlusOffer
{
    public static function promisedMonthlyGain(int $currentScore): int
    {
        return min(24, ScoreRules::SCORE_MAX - $currentScore);
    }
}
```

Engels, types op elke parameter en op de return, en `final` tenzij er iets van hoort te
erven. Heeft je klasse iets nodig (de database, de sessie), zet dat dan in de constructor
en knoop het aan in `src/Core/App.php`.

Geeft je klasse iets terug wat op het scherm komt, geef dan een Engelse sleutel terug en
geen Nederlandse zin.

## 3. Gebruik hem

Nergens registreren. De autoloader in `bootstrap.php` vindt hem zolang de map de
namespace is en het bestand naar de klasse heet:

```php
$gain = PlusOffer::promisedMonthlyGain($account->score);
```

Krijg je "Class not found", dan klopt de mapnaam niet met de namespace, of heet het
bestand anders dan de klasse.

---

# Iets toevoegen aan de database

Zet de tabel of de kolom in `database/schema.sql`, en zet hem erbij in het object dat de
rij leest (`Account`, `Contact`, `Message`). Draai daarna:

```bash
composer db:fresh
```

Dat gooit de database weg en bouwt hem opnieuw op, inclusief de demo-inhoud. Er zijn geen
migraties in dit project. Overleg wel even voordat je dit draait terwijl iemand aan het
testen is.

---

# Iets toevoegen dat in de browser beweegt

JavaScript hangt aan een attribuut, niet aan een import. Zet op het element dat het nodig
heeft:

```html
<div data-component="countdown" data-seconds="45">…</div>
```

En maak `public/assets/js/modules/countdown.js` met een `init()` erin:

```js
/**
 * What this component does and what it needs.
 *
 * @param {HTMLElement} element Element carrying data-seconds.
 * @returns {void}
 */
export function init(element) {
    // ...
}
```

`app.js` vindt het element, laadt jouw module en roept `init()` aan. Je hoeft dus niets
toe te voegen aan `app.js` of aan de layout, en op een pagina zonder dat attribuut wordt
je bestand niet eens opgehaald.

Zorg wel dat het scherm ook zonder JavaScript te gebruiken is: de server print een
kloppende pagina, JavaScript houdt hem alleen bij. Staat er een knop die zonder
JavaScript niets doet, print die dan `hidden` en laat je module hem zichtbaar maken, zoals
de kopieerknop op het contactenscherm.

Nederlandse tekst die je module nodig heeft geef je mee als data-attribuut, zodat de
schermtekst in de template blijft staan.
