# Een nieuw scherm toevoegen

De schermen uit het scenario staan er al als leeg bestand: `contacten.php`,
`gesprek.php`, `noodcontacten.php`, `instellingen.php`, `check.php`, `scorebureau.php`
en `reset.php`. Ze staan in het menu, ze hebben hun eigen stylesheet, en ze wachten op
inhoud. Open er een en begin.

Wil je er zelf een bij maken, bijvoorbeeld "Aandacht kopen", dan gaat dat in vier
stappen.

## 1. Maak het bestand

Nieuw bestand: `public/aandacht.php`. De naam is Nederlands en met kleine letters, want
hij komt in de URL terecht. Begin met dit skelet:

```php
<?php

declare(strict_types=1);

/**
 * One sentence saying what this screen is for.
 */

require __DIR__ . '/../bootstrap.php';

requireLogin();

$contacts = activeContacts(currentUserId());

page('Aandacht kopen');

?>
<section class="section">
    <h1>Aandacht kopen</h1>

    <p>Je hebt <span class="numeric"><?= e(count($contacts)) ?></span> contacten.</p>
</section>
```

Meer is het niet. Je hoeft niets te importeren en je zet er onderaan niets bij: de layout
wordt vanzelf om je scherm heen geprint zodra de pagina klaar is.

`requireLogin()` op de eerste regel sluit het scherm voor wie niet is ingelogd. Laat dat
staan op elk scherm dat iemands eigen gegevens toont.

`page()` neemt de titel die de deelnemer in het tabblad ziet. Wil je meer regelen, dan
kan dat met een tweede argument:

```php
page('Aandacht kopen', [
    'nav' => 'index',               // welk menu-item oplicht
    'description' => 'Wat Google van dit scherm mag weten.',
    'bodyClass' => 'page-aandacht', // om alleen dit scherm te stylen
]);
```

## 2. Maak het CSS-bestand ernaast

Nieuw bestand: `public/assets/css/pages/aandacht.css`. Die heet precies zoals je pagina
en wordt alleen daar ingeladen. Registreren hoef je niets.

Kijk eerst of het blok dat je nodig hebt al bestaat in `components/`: `btn`, `card`,
`field`, `notice`, `avatar`, `meter`, `timer`, `offer`, `summary`. Bestaat het al, dan
gebruik je die klasse. Ga je iets bouwen dat op een tweede scherm ook zou passen, zet het
dan meteen in `components/` met een eigen bestand.

Losse kleuren typ je nooit. Gebruik `var(--color-accent)` en niet `#2f7d5e`.

## 3. Haal je data op boven de HTML

Alles wat je moet weten haal je op vóór de `?>`, in variabelen. Onder de `?>` staat alleen
nog HTML met die variabelen erin. Zodra je onder de `?>` iets gaat uitrekenen, hoort dat
in een functie in `src/`.

Uit de database lezen doe je zo:

```php
$contact = dbFirst('SELECT * FROM contacts WHERE id = ? AND user_id = ?', [$id, currentUserId()]);
```

Altijd met vraagtekens, en altijd samen met het id van de ingelogde gebruiker.

## 4. Print alles met `e()` en zet hem in het menu

```php
<span><?= e($contact['name']) ?></span>
```

Zonder `e()` kan tekst uit je data je pagina slopen. Doe het altijd, ook bij data die je
zelf hebt getypt.

Het menu staat in `views/partials/site-nav.php`, in de array bovenin:

```php
$items = [
    'index' => 'Start',
    'contacten' => 'Contacten',
    'aandacht' => 'Aandacht',
];
```

De sleutel is de bestandsnaam zonder `.php`, de waarde is het Nederlandse label. Hoort je
scherm er niet in, link er dan vanaf een ander scherm naartoe met
`<a href="<?= e(url('aandacht')) ?>">`.

## Een formulier erbij

Een formulier gaat altijd zo: POST, een token erin, afhandelen bovenaan de pagina, en
eindigen met een redirect zodat vernieuwen niets opnieuw verstuurt.

```php
if (isPost()) {
    if (!isValidCsrf()) {
        flash('Het formulier is verlopen. Probeer het opnieuw.', 'danger');
        redirect('aandacht');
    }

    // Doe hier wat er moet gebeuren.

    flash('Je betaling is verwerkt.', 'success', 'Gelukt');
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
`info`, `success`, `loss` of `danger`; het derde argument is de vette kop erboven.

Moet een formulier terugkomen met wat er al ingevuld stond, gebruik dan `rememberForm()`
vlak voor de redirect en `takeForm()` bovenaan de pagina. Zo doet `registreren.php` het.

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

Gaat het over de score, dan `src/score/`. Over contacten, dan `src/contacts/`. Over
berichten, `src/messages/`. Over inloggen, `src/auth/`. Over de database of de
startinhoud, `src/data/`. Is het een hulpje voor de pagina zelf (escapen, sessie,
request, opmaak, layout), dan `src/support/`. Past het nergens, maak dan een nieuwe map
met een Engelse naam in kleine letters.

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
    return min(24, SCORE_MAX - $currentScore);
}
```

Engels, camelCase, types op elke parameter en op de return. Geen namespace: alles staat
in dezelfde ruimte, dus kies een naam die nog niet bestaat.

Geeft je functie iets terug wat op het scherm komt, geef dan een Engelse sleutel terug en
geen Nederlandse zin. De zin die daarbij hoort schrijf je op de pagina. Zo doen
`answerMessage()` en `validateRegistration()` het ook.

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

# Iets toevoegen aan de database

Zet de tabel of de kolom in `database/schema.sql` en draai daarna:

```bash
composer db:fresh
```

Dat gooit de database weg en bouwt hem opnieuw op, inclusief de demo-inhoud. Er zijn geen
migraties in dit project: het prototype leeft kort en de inhoud komt uit
`data/scenario.json`. Overleg wel even voordat je dit draait terwijl iemand aan het
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
kloppende pagina, JavaScript houdt hem alleen bij.
