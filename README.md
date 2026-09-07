# Draagvlak

Prototype van een app uit 2038 die sociale steun omzet in een openbaar cijfer tussen 0
en 100. Speculatief ontwerpproject voor CMGT aan de Hogeschool Rotterdam, gebouwd om op
mensen getest te worden.

Gemaakt door Bram van Dijke en Richie van der Heij.

## Snel starten

Je hebt PHP 8.4 of nieuwer en Composer nodig. Node is optioneel en alleen voor het
opmaken en controleren van CSS en JavaScript.

```bash
git clone https://github.com/Richievdheij/draagvlak.git
cd draagvlak
git switch develop
composer install
composer start
```

Open daarna `http://localhost:8000`. Zet je browser ook een keer in apparaatweergave op
een telefoon: het moet daar net zo goed staan als op een laptop.

Controleer of PHP nieuw genoeg is met `php -v`. Zie je 8.3 of lager staan, installeer
dan eerst een nieuwere versie. Op Windows gaat dat het makkelijkst via Laragon of XAMPP,
op macOS via `brew install php`, op Linux via je pakketbeheerder.

## Commando's

| Commando | Wat het doet |
| --- | --- |
| `composer start` | Start de server op `http://localhost:8000` |
| `composer lint` | Controleert elk PHP-bestand op syntaxfouten |
| `npm install` | Alleen als je de opmaaktools wilt gebruiken |
| `npm run lint` | Controleert de JavaScript |
| `npm run format` | Zet CSS, JS en Markdown recht |

Je hoeft nooit `composer dump-autoload` te draaien. Een nieuw bestand in `src/` wordt
vanzelf ingeladen.

## Waar staat wat

```
public/           Eén PHP-bestand per scherm, plus CSS, JS en de fonts
src/              Alle helpers, per onderwerp; wordt automatisch ingeladen
views/layouts/    Het document dat om elk scherm heen wordt geprint
views/partials/   Stukjes HTML die op meer dan één plek voorkomen
data/             JSON-bestanden in plaats van een database
docs/             Documentatie voor het team, in het Nederlands
bootstrap.php     Wordt door elke pagina als eerste ingeladen
AGENTS.md         Instructies voor AI-assistenten
```

## Een scherm maken

Een scherm is één bestand in `public/` en meer niet. Je hoeft niets te importeren:

```php
<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$contacts = loadJson('contacts');

page('Contacten');

?>
<section class="section">
    <h1>Contacten</h1>

    <?php foreach ($contacts as $contact): ?>
        <p><?= e($contact['name']) ?></p>
    <?php endforeach; ?>
</section>
```

De stap-voor-stap uitleg staat in
[docs/05-nieuwe-pagina-toevoegen.md](docs/05-nieuwe-pagina-toevoegen.md).

## Documentatie

Lees deze in volgorde als je nieuw bent in het project. Samen kost dat een half uur en
daarna kun je meebouwen.

1. [Het project en het scenario](docs/01-project-en-scenario.md) waar dit over gaat en
   welke toon de schermen moeten hebben
2. [Architectuur](docs/02-architectuur.md) hoe de mappen in elkaar zitten en waarom het
   geen MVC is
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

Alle kleuren en maten staan in `public/assets/css/tokens.css`. Een losse hexcode ergens
anders is een fout.

## Testsessie draaien

Open `http://localhost:8000` en geef het apparaat aan de deelnemer. Tussen twee
deelnemers door begin je schoon met een nieuw venster, of met een scherm dat
`sessionReset()` aanroept.

Wil je het scenario aanpassen tussen twee sessies door, pas dan de JSON in `data/` aan
en niet de code. Doe dat niet terwijl er iemand aan het testen is.
