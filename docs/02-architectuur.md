# Architectuur: hoe deze repo in elkaar zit

Dit project is **objectgeoriënteerd PHP zonder framework**, ingedeeld per onderwerp. Alles
wat bij één onderwerp hoort staat in één map, en dat geldt in `src/`, in `views/` en in
`assets/css/` op precies dezelfde manier. Wil je iets aan contacten aanpassen, dan open je
de map `Contacts` en daar staat alles.

Het is geen MVC. Er is geen router en er is geen controllerlaag: een pagina kent alleen
zijn eigen scherm. Dat is de hele leercurve.

De prijs die je daarvoor betaalt: er is geen centrale plek waar alle routes langskomen,
en als het project ooit veel groter wordt loopt dit vast. Dat gebeurt hier niet, want het
aantal schermen ligt vast met het scenario.

## De mappen

```
draagvlak/
├── public/                  De enige map die de browser mag zien
│   ├── index.php            De URL van een scherm: vier regels
│   ├── login.php            …en zo één per scherm
│   └── assets/
│       ├── css/             base, layout, components, features
│       ├── js/              app.js plus modules/
│       └── fonts/           Outfit en Inter, in de repo
├── src/
│   ├── Core/                Het fundament. Je opent het zelden.
│   │   ├── App.php          Alle onderdelen, één keer aan elkaar geknoopt
│   │   ├── Page.php         De basisklasse van elk scherm
│   │   ├── Action.php       Voor een scherm dat alleen doorstuurt
│   │   ├── Config.php  Paths.php
│   │   ├── Http/            Request Response Session Csrf
│   │   ├── Data/            Database JsonStore
│   │   └── View/            View Assets Html Url Format
│   └── Features/            Eén map per onderwerp
│       ├── Auth/            Account, AccountRepository, Guard,
│       │   │                LoginThrottle, Registration
│       │   └── Pages/       LoginPage RegisterPage LogoutPage
│       ├── Contacts/        Contact, ContactCode, ContactRepository
│       │   └── Pages/       ContactsPage EmergencyContactsPage
│       ├── Messages/        Message, MessageRepository
│       │   └── Pages/       ConversationPage
│       ├── Score/           ScoreRules, ScoreBoard, Outcome
│       │   └── Pages/       ScoreBureauPage
│       ├── Home/            Pages/HomePage
│       ├── Settings/        Pages/SettingsPage
│       ├── Screening/       Pages/CheckPage
│       ├── Reset/           Pages/ResetPage
│       └── Scenario/        Scenario: de startinhoud
├── views/
│   ├── layout/              Het document om elk scherm heen, plus header,
│   │                        menu, footer en het foutscherm
│   ├── components/          Stukjes die elke feature mag gebruiken
│   └── features/<naam>/     De templates van die feature
├── database/                schema.sql: hoe de tabellen eruitzien
├── bin/                     Scripts die je via Composer draait
├── data/                    scenario.json: de inhoud van het demo-account
├── docs/                    Deze documentatie
├── bootstrap.php            Autoloader, daarna App::boot()
├── config.php               Instellingen, per machine te overschrijven
├── .php-cs-fixer.php        Hoe PHP wordt opgemaakt
└── .prettierrc              Hoe CSS, JS en Markdown worden opgemaakt
```

Mappen in `src/` hebben een hoofdletter omdat ze de namespace zijn:
`src/Features/Contacts/` hoort bij `Draagvlak\Features\Contacts`. Elke andere map is
kleine letters.

Alle bestandsnamen en URL's zijn Engels, ook die van de schermen. De Nederlandse tekst
staat in `views/`.

## Waarom alleen `public/` bereikbaar is

De webserver wijst naar `public/`, niet naar de projectmap. Alles daarbuiten (`src/`,
`views/`, `data/`, `config.php`) kan een bezoeker dus niet opvragen, ook niet door de URL
te raden. Zou `config.php` te downloaden zijn, dan ligt je databasewachtwoord op straat.

`composer start` doet dat goed. Draai je via Apache met de projectmap als document root,
dan vangt de `.htaccess` in de wortel het op, maar het echte antwoord blijft: wijs je
document root naar `public/`.

## Wat een scherm doet

Een scherm is vier bestanden waarvan de namen uit elkaar volgen:

```
public/contacts.php                             de URL
src/Features/Contacts/Pages/ContactsPage.php    wat het doet
views/features/contacts/contacts.php            hoe het eruitziet
public/assets/css/features/contacts/            hoe het is opgemaakt
```

Het bestand in `public/` is de URL en niets anders:

```php
$app = require __DIR__ . '/../bootstrap.php';

(new ContactsPage($app))->handle();
```

`handle()` staat in `src/Core/Page.php` en draait altijd dezelfde vier stappen in dezelfde
volgorde:

```
1. authorise()   wie mag dit zien
2. submit()      een POST afhandelen, en eindigen met een redirect
3. data()        alles wat de template nodig heeft, hier uitgerekend
4. render        de template van het scherm, binnen de layout
```

Door die volgorde hoeft een template nooit iets uit te zoeken. Zodra je tussen twee tags
staat te rekenen, hoort dat in `data()` of in een methode op het object dat je print.

**Er valt niets in te stellen.** Een pagina weet zijn feature uit zijn namespace en zijn
naam uit zijn klassenaam. `LoginPage` in `Features\Auth\Pages` is dus `/login.php`, print
`views/features/auth/login.php` en krijgt de stylesheets uit `assets/css/features/auth/`.
`EmergencyContactsPage` wordt `emergency-contacts`.

Je overschrijft alleen wat je scherm nodig heeft. `authorise()` vraagt standaard om een
login, `nav()` wijst standaard naar het scherm zelf, en `submit()` en `data()` doen
standaard niets. Een scherm dat alleen doorstuurt (uitloggen) erft van `Action` en heeft
helemaal geen template.

## De autoloader

`bootstrap.php` registreert een PSR-4 autoloader voor `Draagvlak\` en geeft daarna de
`App` terug. Zet je een bestand in de map die bij de namespace hoort, met de naam van de
klasse erin, dan bestaat die klasse meteen. Geen lijst in `composer.json` om bij te
houden, geen `composer dump-autoload`.

Composer zelf is niet nodig om de app te draaien. De enige dependency is de formatter, en
die is `require-dev`.

Krijg je "Class not found", dan komt de mapnaam niet overeen met de namespace, of heet het
bestand anders dan de klasse.

## De layout en de templates

De pagina rendert eerst de template van het scherm tot een string, en print die daarna
binnen `views/layout/app.php`: de `<head>`, de header met het menu, de meldingen, jouw
inhoud, en de footer.

In een template is `$this` de `View`. Vandaar `$this->e()` om te escapen, `$this->url()`
voor een link, `$this->csrfField()` in een formulier en `$this->partial()` voor een stukje
dat vaker voorkomt. Bij `partial()` schrijf je het hele pad uit, zodat je altijd ziet welk
bestand je krijgt:

```php
$this->partial('components/notices', ['flashes' => $flashes]);
$this->partial('features/home/score-block', ['account' => $account]);
```

Een stukje dat één feature gebruikt staat in die feature. Een stukje dat twee features
gebruiken staat in `views/components/`.

## Welke stylesheets een scherm krijgt

```
base/              altijd, in de volgorde fonts, tokens, reset, elements, utilities
layout/            altijd
components/        altijd
features/<naam>/   alleen op de schermen van die feature
```

Registreren hoef je niets. Gebruik je iets op één feature, dan hoort het in die feature.
Gebruiken twee features het, dan is het een component.

## Alles wat een pagina kan bereiken

`App` is de plek waar alle onderdelen aan elkaar geknoopt worden, en het is de enige
plek. Een pagina vraagt erom via `$this->app`:

| `$this->app->…` | Wat het is                                                       |
| --------------- | ---------------------------------------------------------------- |
| `request`       | De methode, wat er is ingevuld, welk scherm dit is               |
| `session`       | Wat bij dit bezoek hoort, meldingen, een formulier dat terugkomt |
| `csrf`          | Het formuliertoken                                               |
| `view`          | Een template renderen                                            |
| `assets`        | Asset-URL's en de stylesheets van een feature                    |
| `config`        | Instellingen uit config.php                                      |
| `database`      | Prepared statements, meer niet                                   |
| `auth`          | Wie is ingelogd, en de bewaking                                  |
| `accounts`      | De users-tabel, wachtwoorden en contactcodes                     |
| `registration`  | Het registratieformulier controleren                             |
| `contacts`      | De lijst, toevoegen met een code, verwijderen                    |
| `messages`      | De inbox en wat reageren kost                                    |
| `scores`        | Een cijfer aanpassen en opschrijven waarom                       |
| `scenario`      | De startinhoud, voor het demo-account                            |

Niets bouwt halverwege een methode zijn eigen afhankelijkheid en niets grijpt naar een
globale variabele. Een nieuw onderdeel knoop je aan in `App::__construct()`, waar je het
hele plaatje in één scherm ziet.

## De database

Een echte database: MySQL, via PDO. Geen ORM en geen query builder, gewoon SQL die je
zelf leest.

```bash
composer db:setup    # maakt de database, de tabellen en de demo-inhoud
composer db:fresh    # gooit alles weg en bouwt het opnieuw op
```

De tabellen staan in `database/schema.sql`. Het zijn er vier:

`users` is een account met een naam, een e-mailadres, een gehasht wachtwoord, een cijfer
en de code die je deelt. `contacts` zijn de mensen van één account. `messages` zijn hun
berichten. `score_events` is de geschiedenis: elke puntenverandering wordt daar apart
bijgeschreven, zodat je een sessie achteraf kunt teruglezen.

Queries staan in een repository binnen hun eigen feature, nooit in een pagina en nooit in
een template. Een repository geeft objecten terug (`Account`, `Contact`, `Message`) en
geen kale rijen, zodat je editor weet wat erin zit.

```php
$contacts = $this->database->all('SELECT * FROM contacts WHERE user_id = ?', [$userId]);
```

Zet nooit een waarde in de tekst van de query zelf, ook niet eentje die je zelf hebt
getypt. En zoek een rij altijd samen met het id van de ingelogde gebruiker op, anders
komt iemand met een aangepast nummer in de URL bij andermans gegevens.

### Twee soorten contact

`contacts.contact_user_id` wijst naar een echt account zodra twee mensen elkaars code
hebben ingevuld. Voor de mensen uit `data/scenario.json` is die kolom leeg; die horen bij
niemand.

Daarom joint elke leesquery in `ContactRepository` de tabel `users` met `COALESCE`: bij
een gekoppeld contact zie je de naam en het cijfer van het account zelf, bij een
scenariocontact de kopieën in de rij. Zo zien twee mensen altijd hetzelfde getal.

## Inloggen en contacten

`Guard::requireLogin()` sluit een scherm af voor wie niet is ingelogd; die wordt naar het
inlogscherm gestuurd en komt na het inloggen alsnog op de pagina die hij wilde.
`requireGuest()` doet het omgekeerde op het inlog- en registratiescherm.

Wachtwoorden worden gehasht met `password_hash()` en verlaten `AccountRepository` nooit
in een andere vorm. Bij een geslaagde login krijgt de sessie een nieuw id, en na vijf
mislukte pogingen gaat het formulier een kwartier op slot.

Een nieuw account begint leeg, met een eigen code zoals `SAM-7QK4`. Vult iemand die code
in, dan staan jullie allebei bij elkaar in de lijst. Er wordt niemand voor je toegevoegd.
Alleen het demo-account krijgt de inhoud van `data/scenario.json`.

Elk formulier met POST krijgt `<?= $this->csrfField() ?>` mee en wordt afgehandeld achter
`requireValidCsrf()`. Wat een deelnemer tijdens één bezoek doet staat verder in de
sessie; alles wat een refresh moet overleven staat in de database.

## Als er iets misgaat

Met `app.debug` op `true` zie je de fout op het scherm. Zet je hem uit voor een demo, dan
vangt `App::boot()` de fout af, schrijft hem naar het foutlogboek en print
`views/layout/failure.php`: een gewoon scherm dat zegt dat er iets misging. Een demo met
een lege witte pagina is erger dan een demo die dat toegeeft.

## Wat we bewust niet doen

Geen framework, geen router, geen bundler, geen CSS-library en geen ORM. Elk van die
dingen is verdedigbaar in een echt product en kost hier alleen maar tijd die naar de
schermen zelf moet. Kom je iets tegen dat er echt niet zonder kan, bespreek dat dan eerst
met het team in plaats van het toe te voegen.
