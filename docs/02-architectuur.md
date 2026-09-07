# Architectuur: hoe deze repo in elkaar zit

Dit project gebruikt een **page-based layout met gedeelde includes**. Elk scherm is één
PHP-bestand in `public/`, alle logica staat in `src/`, het document eromheen staat in
`views/layouts/`, en alles wat op meer dan één plek voorkomt staat in `views/partials/`.
Dat is de indeling die je in vrijwel elke PHP-cursus als eerste tegenkomt, alleen dan
opgeruimd: geen losse code midden in je HTML, en geen bestand dat twee keer hetzelfde
doet.

Het is bewust géén MVC. MVC vraagt dat je eerst een router, een controller en een model
snapt voordat je één knop kunt verplaatsen. Voor een prototype dat een paar weken leeft
en door een team wordt gebouwd waarvan niet iedereen MVC heeft gehad, kost dat meer dan
het oplevert. Wil je de knop op het contactenscherm aanpassen, dan open je
`public/contacten.php` en daar staat hij. Dat is de hele leercurve.

De prijs die je daarvoor betaalt: er is geen centrale plek waar alle routes langskomen,
en als het project ooit veel groter wordt loopt dit vast. Dat gebeurt hier niet, want het
aantal schermen ligt vast met het scenario.

## De mappen

```
draagvlak/
├── public/              De enige map die de browser mag zien
│   ├── index.php        Startscherm, en meteen het voorbeeld voor een nieuw scherm
│   ├── favicon.svg
│   └── assets/
│       ├── css/         fonts, tokens, base, components, screens
│       ├── js/          app.js plus modules/
│       └── fonts/       Outfit en Inter, in de repo
├── src/                 Alle helpers, per onderwerp gegroepeerd
│   ├── support/         Escapen en URL's, sessie en meldingen, request en redirect,
│   │                    en de paginatemplate
│   └── data/            De JSON-bestanden lezen en schrijven
├── views/
│   ├── layouts/         Het document dat om elk scherm heen wordt geprint
│   └── partials/        Stukjes HTML die op meer dan één plek staan
├── data/                JSON-bestanden in plaats van een database
├── docs/                Deze documentatie
├── bootstrap.php        Wordt door elke pagina als eerste ingeladen
└── composer.json        De start- en lint-commando's
```

Alle mapnamen zijn kleine letters. Staat er ergens een map met een hoofdletter, dan is
dat een fout die hersteld hoort te worden.

## Waarom alleen `public/` bereikbaar is

De webserver wijst naar `public/`, niet naar de projectmap. Alles daarbuiten (`src/`,
`views/`, `data/`, `bootstrap.php`) kan een bezoeker dus niet opvragen, ook niet door de
URL te raden. Zou je `data/contacts.json` in `public/` zetten, dan kan iedereen dat
bestand downloaden. Dit is de belangrijkste regel van de hele indeling en de reden dat
een professionele PHP-repo er anders uitziet dan de map waar je in les 1 mee begint.

## Wat een pagina doet

Elke pagina volgt dezelfde vier stappen, in deze volgorde:

```php
require __DIR__ . '/../bootstrap.php';   // 1. inladen
if (isPost()) { /* ... */ redirect(); }   // 2. formulier afhandelen
$contacts = loadJson('contacts');         // 3. data ophalen
page('Contacten');                        // 4. daarna alleen nog HTML
```

Er staat nooit een berekening na stap 4. Moet je iets uitrekenen, dan schrijf je daar
een functie voor in `src/` en roep je die aan in stap 3. Dat is de enige structurele
regel die je echt moet onthouden.

## Waarom je niets hoeft te importeren

`bootstrap.php` doet drie dingen. Het zet de paden en de sessie klaar, het laadt
`vendor/autoload.php` in als die er is, en daarna leest het **elk PHP-bestand in `src/`
in**, op alfabetische volgorde.

Daardoor bestaan `e()`, `page()`, `loadJson()` en de rest op elke pagina, zonder ook maar
één `use function`-regel. Voeg je een bestand toe aan `src/`, dan werkt het meteen: geen
lijst in `composer.json`, geen `composer dump-autoload`, geen namespace.

Dat betekent wel iets voor wat je in `src/` zet. Een bestand daar **declareert alleen**
functies en constanten. Zet er code in die zelf iets doet, dan draait die op elke pagina
van elk verzoek. Wil je dat een functie ook echt gevonden wordt, geef hem dan een naam
die verder nergens bestaat; twee functies met dezelfde naam is een fatale fout.

Krijg je "Call to undefined function", dan staat het bestand niet in `src/` of is de naam
verkeerd gespeld. Aan de autoloader ligt het niet meer.

## Waar de layout vandaan komt

`page('Contacten')` doet twee dingen: het onthoudt de titel, en het begint alles wat de
pagina daarna print op te vangen. Zodra het script klaar is, print
`views/layouts/app.php` het hele document eromheen: de `<head>`, de header met het menu,
de meldingen, jouw inhoud, en de footer.

Daarom staat er onderaan een scherm geen `pageFooter()` of `include`. Wil je iets op elke
pagina veranderen, dan doe je dat in de layout of in een partial, en niet in de schermen.

Een scherm dat alleen doorstuurt (bijvoorbeeld een resetknop) roept `page()` helemaal
niet aan: `redirect()` gooit de opgevangen inhoud weg en stuurt alleen een redirect.

## Waar staat de stand van het spel

Er is geen database en geen login. Wat een deelnemer tijdens één run doet, staat in de
PHP-sessie, dus in een cookie op die ene browser. Dat is precies wat een testsessie nodig
heeft: twee onderzoekers kunnen tegelijk testen zonder elkaars run te overschrijven.

De vaste gegevens staan in `data/*.json`. Wil je het scenario aanscherpen tussen twee
sessies door, dan pas je JSON aan en niet de code.

## Wat we bewust niet doen

Geen framework, geen router, geen bundler, geen CSS-library, geen database en geen
inlogsysteem. Elk van die dingen is verdedigbaar in een echt product en kost hier alleen
maar tijd die naar de schermen zelf moet. Kom je iets tegen dat er echt niet zonder kan,
bespreek dat dan eerst met het team in plaats van het toe te voegen.
