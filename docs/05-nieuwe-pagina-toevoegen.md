# Een nieuw scherm toevoegen

Stel je maakt het scherm "Aandacht kopen", waar je kunt betalen voor reacties. Dat gaat
in vijf stappen en duurt vijf minuten.

## 1. Maak het bestand

Nieuw bestand: `public/aandacht.php`. De naam is Nederlands en met kleine letters, want
hij komt in de URL terecht. Begin met dit skelet en pas de namen aan:

```php
<?php

declare(strict_types=1);

/**
 * One sentence saying what this screen is for.
 */

require __DIR__ . '/../bootstrap.php';

$contacts = loadJson('contacts');

page('Aandacht kopen');

?>
<section class="section">
    <h1>Aandacht kopen</h1>

    <p>Je hebt <span class="numeric"><?= e(count($contacts)) ?></span> contacten.</p>
</section>
```

Meer is het niet. Je hoeft niets te importeren en je zet er onderaan niets bij: de
layout wordt vanzelf om je scherm heen geprint zodra de pagina klaar is.

`page()` neemt de titel die de deelnemer in het tabblad ziet. Wil je meer regelen, dan
kan dat met een tweede argument:

```php
page('Aandacht kopen', [
    'nav' => 'index',              // welk menu-item oplicht
    'description' => 'Wat Google van dit scherm mag weten.',
    'bodyClass' => 'page-aandacht', // om alleen dit scherm te stylen
]);
```

## 2. Haal je data op boven de HTML

Alles wat je moet weten haal je op vóór de `?>`, in variabelen. Onder de `?>` staat
alleen nog HTML met die variabelen erin. Zodra je onder de `?>` iets gaat uitrekenen,
hoort dat in een functie in `src/`.

## 3. Print alles met `e()`

```php
<span><?= e($contact['name']) ?></span>
```

Zonder `e()` kan tekst uit je data je pagina slopen. Doe het altijd, ook bij data die je
zelf hebt getypt.

## 4. Gebruik bestaande klassen

Kijk eerst in `public/assets/css/components.css` of het blok dat je nodig hebt al
bestaat: `btn`, `card`, `card-grid`, `notice`, `field`, `badge`, `hero`. Nieuwe CSS
schrijf je pas als er echt niets past, en dan in `screens.css` met een naam die zegt wat
het is.

Losse kleuren typ je nooit. Gebruik `var(--color-danger)` en niet `#a62b1f`.

## 5. Zet hem in het menu, als hij daar hoort

Het menu staat in `views/partials/site-nav.php`, in de array bovenin:

```php
$items = [
    'index' => 'Start',
    'aandacht' => 'Aandacht',
];
```

De sleutel is de bestandsnaam zonder `.php`, de waarde is het Nederlandse label. Vier of
vijf items is het maximum, anders wordt de balk onleesbaar. Hoort je scherm er niet in,
link er dan vanaf een ander scherm naartoe met `<a href="<?= e(url('aandacht')) ?>">`.

## Een formulier erbij

Een formulier gaat altijd zo: POST, een token erin, afhandelen bovenaan de pagina, en
eindigen met een redirect zodat vernieuwen niets opnieuw verstuurt.

```php
if (isPost()) {
    if (!isValidCsrf()) {
        flash('Het formulier is verlopen. Probeer het opnieuw.', 'danger');
    } else {
        flash('Gelukt.', 'success');
    }

    redirect('aandacht');
}
```

```html
<form method="post" action="<?= e(url('aandacht')) ?>">
    <?= csrfField() ?>

    <div class="field">
        <label class="field__label" for="amount">Bedrag</label>
        <input class="field__input" type="text" id="amount" name="amount">
    </div>

    <button class="btn" type="submit">Betalen</button>
</form>
```

De melding uit `flash()` verschijnt vanzelf bovenaan de volgende pagina. Tonen kan met
`info`, `success` of `danger`.

## Controleren

```bash
composer lint
composer start
```

Open `http://localhost:8000/aandacht.php`, en zet de browser in apparaatweergave op een
telefoon. Klopt het daar en op je laptop, dan ben je klaar.

---

# Een functie toevoegen aan `src/`

Stel je wilt uitrekenen wat een abonnement je per maand aan punten oplevert.

## 1. Kies de juiste map

Gaat het over data inlezen, dan `src/data/`. Is het een hulpje voor de pagina zelf
(escapen, sessie, request, layout), dan `src/support/`. Gaat het over een onderwerp uit
het scenario, maak dan een nieuwe map met een Engelse naam in kleine letters, zoals
`src/score/`.

## 2. Schrijf de functie met een docblock erboven

```php
<?php

declare(strict_types=1);

/**
 * Points the paid subscription promises per month.
 *
 * The number is marketing, not a calculation: the screen has to sell something.
 */
function promisedMonthlyGain(int $currentScore): int
{
    return min(24, 100 - $currentScore);
}
```

Engels, camelCase, types op elke parameter en op de return. Geen namespace: alles staat
in dezelfde ruimte, dus kies een naam die nog niet bestaat.

## 3. Gebruik hem

Nergens registreren, niets importeren. `bootstrap.php` laadt elk bestand in `src/` in,
dus je functie bestaat meteen op elke pagina:

```php
$gain = promisedMonthlyGain($score);
```

Krijg je toch "Call to undefined function", dan staat je bestand niet in `src/`, heeft
het geen `.php` als extensie, of is de naam anders gespeld dan je denkt.

Let op één ding: een bestand in `src/` mag alleen functies en constanten declareren. Zet
er losse code in die iets doet, dan draait die bij elk verzoek van elke pagina.

---

# Iets toevoegen dat in de browser beweegt

JavaScript hangt aan een attribuut, niet aan een import. Zet op het element dat het nodig
heeft:

```html
<div data-component="countdown" data-seconds="45">…</div>
```

En maak `public/assets/js/modules/countdown.js`:

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

Zorg wel dat het scherm ook zonder JavaScript te gebruiken is.
