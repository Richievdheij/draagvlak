# Draagvlak

Prototype van een app uit 2038 die sociale steun omzet in een openbaar cijfer tussen 0
en 100. Speculatief ontwerpproject voor CMGT aan de Hogeschool Rotterdam, gebouwd om op
mensen getest te worden.

Gemaakt door Bram van Dijke en Richie van der Heij.

## Snel starten

Je hebt PHP 8.4 of nieuwer, Composer en MySQL nodig. Met Herd, Laragon of XAMPP heb je
alle drie al. Node is optioneel en alleen voor het controleren en opmaken van CSS en
JavaScript.

```bash
git clone https://github.com/Richievdheij/draagvlak.git
cd draagvlak
git switch develop
composer install
composer db:setup
composer start
```

Open daarna `http://localhost:8000` en log in met **sam@draagvlak.test** en wachtwoord
**draagvlak**. Of maak je eigen account aan; je begint dan met dezelfde beginstand.

Draait MySQL bij jou met een wachtwoord of op een andere poort? Kopieer dan `config.php`
naar `config.local.php` en pas daar alleen die regels aan. Dat bestand blijft op jouw
machine staan.

Zet je browser ook een keer in apparaatweergave op een telefoon: het is een app en moet
daar net zo goed staan.

## Commando's

| Commando | Wat het doet |
| --- | --- |
| `composer start` | Start de server op `http://localhost:8000` |
| `composer db:setup` | Maakt de database, de tabellen en de demo-inhoud |
| `composer db:fresh` | Gooit de database weg en bouwt hem opnieuw op |
| `composer lint` | Controleert elk PHP-bestand op syntaxfouten |
| `npm install` | Alleen als je de opmaaktools wilt gebruiken |
| `npm run lint` | Controleert de JavaScript |
| `npm run format` | Zet CSS, JS en Markdown recht |

Je hoeft nooit `composer dump-autoload` te draaien. Een nieuw bestand in `src/` wordt
vanzelf ingeladen, en een nieuw CSS-bestand vanzelf meegestuurd.

## Waar staat wat

```
public/           Eén PHP-bestand per scherm, plus CSS, JS en de fonts
  assets/css/     base, layouts, partials, components, pages: één map per plek
src/              Alle helpers, per onderwerp; wordt automatisch ingeladen
views/layouts/    Het document dat om elk scherm heen wordt geprint
views/partials/   Stukjes HTML die op meer dan één plek voorkomen
database/         schema.sql: hoe de tabellen eruitzien
bin/              Scripts die je via Composer draait
data/             scenario.json: de inhoud waarmee elke deelnemer begint
docs/             Documentatie voor het team, in het Nederlands
bootstrap.php     Wordt door elke pagina als eerste ingeladen
config.php        Databasegegevens, per machine te overschrijven
AGENTS.md         Instructies voor AI-assistenten
```

## Wat er al staat

Het startscherm, inloggen, registreren en uitloggen werken, met een echte database
erachter. De andere schermen (`contacten`, `gesprek`, `noodcontacten`, `instellingen`,
`check`, `scorebureau`, `reset`) zijn lege bestanden die al in het menu staan en al een
eigen stylesheet hebben. Daar bouw je verder.

## Een scherm maken

Een scherm is één bestand in `public/`. Je hoeft niets te importeren:

```php
<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

requireLogin();

$contacts = activeContacts(currentUserId());

page('Contacten');

?>
<section class="section">
    <h1>Contacten</h1>

    <?php foreach ($contacts as $contact): ?>
        <p><?= e($contact['name']) ?></p>
    <?php endforeach; ?>
</section>
```

De opmaak van dat scherm zet je in `public/assets/css/pages/contacten.css`. Die wordt
alleen daar ingeladen. De stap-voor-stap uitleg staat in
[docs/05-nieuwe-pagina-toevoegen.md](docs/05-nieuwe-pagina-toevoegen.md).

## Documentatie

Lees deze in volgorde als je nieuw bent in het project. Samen kost dat een half uur en
daarna kun je meebouwen.

1. [Het project en het scenario](docs/01-project-en-scenario.md) waar dit over gaat en
   welke toon de schermen moeten hebben
2. [Architectuur](docs/02-architectuur.md) hoe de mappen in elkaar zitten, hoe de
   database werkt en waarom het geen MVC is
3. [Code-afspraken](docs/03-code-afspraken.md) namen, comments, Engels tegenover
   Nederlands
4. [Designsysteem](docs/04-designsysteem.md) kleuren, lettertypes en waar je ze
   aanpast
5. [Een nieuw scherm toevoegen](docs/05-nieuwe-pagina-toevoegen.md) stap voor stap
6. [Onderzoek en wat we ermee doen](docs/06-onderzoek-en-inzichten.md) de
   testresultaten en welke keuzes daaruit volgen
7. [AI gebruiken](docs/07-ai-gebruiken.md) hoe je Claude, ChatGPT of Gemini op dit
   project instelt

Ga je meewerken, lees dan ook [CONTRIBUTING.md](CONTRIBUTING.md).

## Drie dingen die je meteen moet weten

We werken op `develop`. Maak je branch daarvandaan en zet je pull request er ook weer
naartoe. `main` is alleen voor een demo die draait.

De code is Engels en het scherm is Nederlands. Functienamen, comments en commit messages
in het Engels, alles wat een deelnemer leest in het Nederlands.

Alle kleuren en maten staan in `public/assets/css/base/tokens.css`. Een losse hexcode
ergens anders is een fout.

## Testsessie draaien

Geef elke deelnemer een eigen account via het registratiescherm; iedereen begint dan met
dezelfde drie contacten en hetzelfde beginpunt uit `data/scenario.json`. Wil je helemaal
schoon beginnen, dan draai je:

```bash
composer db:fresh
```

Dat gooit alle accounts en alle antwoorden weg. Doe het niet terwijl er iemand aan het
testen is. Wil je het scenario aanscherpen tussen twee sessies door, pas dan
`data/scenario.json` aan en niet de code.
