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
│   ├── index.php        Startscherm: je cijfer en de mensen erachter
│   ├── inloggen.php     Inloggen, registreren en uitloggen
│   ├── contacten.php    …en de andere schermen, nog leeg
│   ├── favicon.svg
│   └── assets/
│       ├── css/         base, layouts, partials, components, pages
│       ├── js/          app.js plus modules/
│       └── fonts/       Outfit en Inter, in de repo
├── src/                 Alle helpers, per onderwerp gegroepeerd
│   ├── support/         Instellingen, escapen en URL's, sessie en meldingen,
│   │                    request en redirect, opmaak, en de paginatemplate
│   ├── data/            De database, de queries en de startinhoud
│   ├── auth/            Registreren, inloggen en schermen afschermen
│   ├── score/           De regels van het scenario en wat een keuze kost
│   ├── contacts/        De lijst met mensen, en iemand eraf halen
│   └── messages/        De inbox, het reactievenster en de uitkomst
├── views/
│   ├── layouts/         Het document dat om elk scherm heen wordt geprint
│   └── partials/        Stukjes HTML die op meer dan één plek staan
├── database/            schema.sql: hoe de tabellen eruitzien
├── bin/                 Scripts die je via Composer draait
├── data/                scenario.json: de inhoud waarmee een deelnemer begint
├── docs/                Deze documentatie
├── bootstrap.php        Wordt door elke pagina als eerste ingeladen
├── config.php           Databasegegevens, per machine te overschrijven
└── composer.json        De start-, lint- en databasecommando's
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

Elke pagina volgt dezelfde vijf stappen, in deze volgorde:

```php
require __DIR__ . '/../bootstrap.php';        // 1. inladen
requireLogin();                                // 2. wie mag dit zien
if (isPost()) { /* ... */ redirect(); }        // 3. formulier afhandelen
$contacts = activeContacts(currentUserId());   // 4. data ophalen
page('Contacten');                             // 5. daarna alleen nog HTML
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

## De database

Er is een echte database: MySQL, via PDO. Geen ORM en geen query builder, gewoon SQL die
je zelf leest.

```bash
composer db:setup    # maakt de database, de tabellen en de demo-inhoud
composer db:fresh    # gooit alles weg en bouwt het opnieuw op
```

De tabellen staan in `database/schema.sql`. Het zijn er vier:

`users` is een account met een naam, een e-mailadres, een gehasht wachtwoord en een
cijfer. `contacts` zijn de mensen van één account. `messages` zijn hun berichten, met het
moment van binnenkomst, het reactievenster en wat er uiteindelijk mee gebeurd is.
`score_events` is de geschiedenis: elke puntenverandering wordt daar apart bijgeschreven,
zodat je een sessie achteraf kunt teruglezen.

Vier functies gebruik je in de praktijk: `dbAll()` voor meer rijen, `dbFirst()` voor één
rij, `dbValue()` voor één waarde en `dbRun()` voor iets wat je verandert. Alles gaat als
prepared statement, dus met vraagtekens en een aparte lijst waarden:

```php
$contacts = dbAll('SELECT * FROM contacts WHERE user_id = ?', [$userId]);
```

Zet nooit een waarde in de tekst van de query zelf, ook niet eentje die je zelf hebt
getypt. Zo houd je SQL-injectie buiten de deur, en het is een gewoonte die je in elk
volgend project nodig hebt.

Zoek een rij ook altijd samen met het id van de ingelogde gebruiker op. Anders komt
iemand met een aangepast nummer in de URL bij andermans gegevens.

De Nederlandse startinhoud staat niet in de code maar in `data/scenario.json`. Bij het
aanmaken van een account wordt die met `seedScenarioFor()` in de database gezet, zodat
elke deelnemer met dezelfde drie mensen begint.

## Inloggen

`requireLogin()` op de eerste regel van een scherm sluit het af voor wie niet is
ingelogd; die wordt naar het inlogscherm gestuurd en komt na het inloggen alsnog op de
pagina die hij wilde. `requireGuest()` doet het omgekeerde op het inlog- en
registratiescherm.

Wachtwoorden worden gehasht met `password_hash()` en nergens anders bewaard. Bij een
geslaagde login krijgt de sessie een nieuw id, en na vijf mislukte pogingen gaat het
formulier een kwartier op slot.

Elk formulier met POST krijgt `<?= csrfField() ?>` mee en wordt afgehandeld achter
`isValidCsrf()`. Wat een deelnemer tijdens één bezoek doet, staat verder in de sessie;
alles wat een refresh moet overleven staat in de database.

## Wat we bewust niet doen

Geen framework, geen router, geen bundler, geen CSS-library en geen ORM. Elk van die
dingen is verdedigbaar in een echt product en kost hier alleen maar tijd die naar de
schermen zelf moet. Kom je iets tegen dat er echt niet zonder kan, bespreek dat dan eerst
met het team in plaats van het toe te voegen.
