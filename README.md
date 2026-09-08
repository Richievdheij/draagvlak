# Draagvlak

Prototype van een app uit 2038 die sociale steun omzet in een openbaar cijfer tussen 0
en 100. Speculatief ontwerpproject voor CMGT aan de Hogeschool Rotterdam, gebouwd om op
mensen getest te worden.

Gemaakt door Bram van Dijke en Richie van der Heij.

## Snel starten

Je hebt **PHP 8.4**, Composer en MySQL nodig. Met Herd, Laragon of XAMPP heb je alle drie
al. Node 24 is optioneel en alleen voor het controleren en opmaken van CSS en JavaScript.

```bash
git clone https://github.com/Richievdheij/draagvlak.git
cd draagvlak
git switch develop
composer install
composer db:setup
```

Waar je de map neerzet en hoe je hem noemt maakt niet uit: elk pad in de code wordt
afgeleid uit het bestand zelf.

### Met Herd (aanbevolen)

```bash
herd link draagvlak      # maakt http://draagvlak.test
herd isolate 8.4         # zet deze site vast op PHP 8.4
```

Herd ziet de map `public/` en serveert die vanzelf als document root, dus `config.php` en
`src/` liggen buiten bereik van de browser. Daarna is de app altijd bereikbaar op
**http://draagvlak.test**, naast je andere sites en naast `http://phpmyadmin.test`. Beide
praten met dezelfde MySQL op `127.0.0.1:3306`, dus wat je in phpMyAdmin ziet is wat de
app gebruikt.

### Zonder Herd

```bash
composer start           # http://localhost:8000
```

### Inloggen

| Wat         | Waarde             |
| ----------- | ------------------ |
| E-mailadres | `test@example.com` |
| Wachtwoord  | `password`         |
| Contactcode | `SAM-2938`         |

Die drie staan vast in `src/Features/Scenario/DemoSeeder.php` en veranderen niet, ook niet
na een `composer db:fresh`. De code kun je dus gewoon uit je hoofd gebruiken als je een
tweede account eraan wilt koppelen.

Ze staan alleen hier en in de instructiebestanden; op het scherm zelf is er niets te zien
wat verraadt dat dit een testopstelling is. Het adres is `example.com` omdat dat domein
gereserveerd is voor precies dit doel: het kan nooit van een echt persoon zijn.

Of maak je eigen account aan. Je begint dan met dezelfde beginstand, een lege lijst en
een eigen code.

Draait MySQL bij jou met een wachtwoord of op een andere poort? Kopieer dan `config.php`
naar `config.local.php` en pas daar alleen die regels aan. Dat bestand blijft op jouw
machine staan.

Zet je browser ook een keer in apparaatweergave op een telefoon: het is een app en moet
daar net zo goed staan.

## Versies

| Wat   | Versie   | Waarom                                                                                                                              |
| ----- | -------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| PHP   | 8.4      | Wat Herd standaard levert, en ondersteund tot eind 2028. `composer.json` staat op `^8.4`, dus 8.5 werkt ook en 9.0 niet stilletjes. |
| Node  | 24 (LTS) | Alleen nodig voor de opmaaktools. `package.json` vraagt `>=24`.                                                                     |
| MySQL | 8.4 of 9 | Het schema gebruikt niets versiespecifieks.                                                                                         |

Zet je site vast met `herd isolate 8.4`. Verandert iemand later de globale PHP-versie van
Herd, dan blijft dit project op 8.4 draaien.

## Commando's

| Commando            | Wat het doet                                               |
| ------------------- | ---------------------------------------------------------- |
| `composer start`    | Start de server op `http://localhost:8000`                 |
| `composer check`    | Syntax en opmaak. Dit moet groen zijn voordat je pusht.    |
| `composer format`   | Maakt elk PHP-bestand op volgens `pint.json`               |
| `composer db:setup` | Maakt de database, de tabellen en de demo-inhoud           |
| `composer db:fresh` | Gooit de database weg en bouwt hem opnieuw op              |
| `npm install`       | Alleen als je de opmaaktools voor CSS en JS wilt gebruiken |
| `npm run check`     | Controleert de JavaScript en de opmaak van CSS en Markdown |
| `npm run lint`      | Controleert alleen de JavaScript                           |
| `npm run format`    | Maakt CSS, JS en Markdown op volgens `.prettierrc`         |

Je hoeft nooit `composer dump-autoload` te draaien. Een nieuwe klasse wordt vanzelf
gevonden en een nieuw CSS-bestand vanzelf meegestuurd.

Werk je in VS Code, zeg dan ja tegen de aanbevolen extensies. Daarna wordt elk bestand bij
het opslaan opgemaakt zoals hierboven, en hoeft niemand daar nog over te praten.

## MCP

`.mcp.json` staat in de repo en zet twee servers klaar zodra je het project opent in een
client die MCP ondersteunt (Claude Code, Claude Desktop, Cursor). De eerste keer moet je
ze goedkeuren, met `/mcp` of bij het opstarten.

| Server            | Wat het doet                                               |
| ----------------- | ---------------------------------------------------------- |
| `draagvlak-files` | Bestanden lezen en aanpassen, begrensd tot deze projectmap |
| `herd`            | Sites, PHP-versies, HTTPS en isolatie beheren via Herd     |

`draagvlak-files` gebruikt een relatief pad, dus hij werkt bij iedereen ongeacht waar de
map staat. `herd` wijst naar de MCP-server die Herd zelf meelevert; heb je geen Herd, dan
start die server niet en verder gebeurt er niets.

## Waar staat wat

Alles wat bij één onderwerp hoort staat in één map, en `src/`, `views/` en `assets/css/`
zijn identiek ingedeeld.

```
public/               Eén PHP-bestand per URL, plus CSS, JS en de fonts
  assets/css/         base, layout, components, features
src/
  Core/               Het fundament: App, Page, Action, Http, Data, View
  Features/<Naam>/    Eén map per onderwerp, met Pages/ voor de schermen
views/
  layout/             Het document om elk scherm heen
  components/         Stukjes die elke feature mag gebruiken
  features/<naam>/    De templates van die feature
database/             schema.sql: hoe de tabellen eruitzien
bin/                  Scripts die je via Composer draait
data/                 scenario.json: de inhoud van het demo-account
docs/                 Documentatie voor het team, in het Nederlands
bootstrap.php         Autoloader, daarna de App die elk scherm meekrijgt
config.php            Instellingen, per machine te overschrijven
AGENTS.md             Instructies voor AI-assistenten
```

De features zijn nu `Auth`, `Contacts`, `Messages`, `Score`, `Home`, `Settings`,
`Screening`, `Reset` en `Scenario`.

## Wat er al staat

Het startscherm, contacten, inloggen, registreren en uitloggen werken, met een echte
database erachter. De andere schermen (`conversation`, `emergency-contacts`, `settings`,
`check`, `score-bureau`, `reset`) hebben al een klasse, een template en een stylesheet, en
staan in het menu. Daar bouw je verder.

## Een scherm maken

Vier bestanden waarvan de namen uit elkaar volgen.

```php
// public/contacts.php - de URL
$app = require __DIR__ . '/../bootstrap.php';

(new ContactsPage($app))->handle();
```

```php
// src/Features/Contacts/Pages/ContactsPage.php - wat het doet
final class ContactsPage extends Page
{
    protected function title(): string
    {
        return 'Contacten';
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['contacts' => $this->app->contacts->active($this->app->auth->id())];
    }
}
```

```php
// views/features/contacts/contacts.php - hoe het eruitziet
<?php foreach ($contacts as $contact): ?>
    <p><?= $this->e($contact->name) ?></p>
<?php endforeach; ?>
```

De opmaak zet je in `public/assets/css/features/contacts/`. Die wordt alleen op de
contactenschermen ingeladen. Er valt niets in te stellen: een pagina weet zijn feature uit
zijn namespace en zijn naam uit zijn klassenaam. De stap-voor-stap uitleg staat in
[docs/05-nieuwe-pagina-toevoegen.md](docs/05-nieuwe-pagina-toevoegen.md).

## Documentatie

Lees deze in volgorde als je nieuw bent in het project. Samen kost dat een half uur en
daarna kun je meebouwen.

1. [Het project en het scenario](docs/01-project-en-scenario.md) waar dit over gaat en
   welke toon de schermen moeten hebben
2. [Architectuur](docs/02-architectuur.md) hoe de mappen in elkaar zitten, hoe een
   scherm werkt en hoe de database eruitziet
3. [Code-afspraken](docs/03-code-afspraken.md) namen, klassen, comments, opmaak, Engels
   tegenover Nederlands
4. [Designsysteem](docs/04-designsysteem.md) kleuren, lettertypes en waar je ze
   aanpast
5. [Een nieuw scherm toevoegen](docs/05-nieuwe-pagina-toevoegen.md) stap voor stap
6. [Onderzoek en wat we ermee doen](docs/06-onderzoek-en-inzichten.md) de
   testresultaten en welke keuzes daaruit volgen
7. [AI gebruiken](docs/07-ai-gebruiken.md) hoe je Claude, Copilot, ChatGPT of Gemini op
   dit project instelt

Ga je meewerken, lees dan ook [CONTRIBUTING.md](CONTRIBUTING.md).

## Vier dingen die je meteen moet weten

We werken op `develop`. Maak je branch daarvandaan en zet je pull request er ook weer
naartoe. `main` is alleen voor een demo die draait.

De code is Engels en het scherm is Nederlands. Klassen, methodes, comments, bestandsnamen,
URL's en commit messages in het Engels; alles wat een deelnemer leest in het Nederlands,
en dat staat in `views/`.

Alle kleuren en maten staan in `public/assets/css/base/tokens.css`. Een losse hexcode
ergens anders is een fout.

Opmaken doe je niet met de hand. `composer format` en `npm run format` doen het, en in VS
Code gebeurt het bij het opslaan.

## Testsessie draaien

Geef elke deelnemer een eigen account via het registratiescherm. Iedereen begint dan op
hetzelfde cijfer, met een lege lijst en een eigen code. Wil je dat twee deelnemers elkaar
zien, laat ze dan elkaars code invullen op het contactenscherm.

Alleen het demo-account `test@example.com` krijgt de drie mensen uit `data/scenario.json`,
zodat je het startscherm kunt laten zien zonder eerst iemand uit te nodigen.

Wil je helemaal schoon beginnen:

```bash
composer db:fresh
```

Dat gooit alle accounts en alle antwoorden weg. Doe het niet terwijl er iemand aan het
testen is. Wil je het scenario aanscherpen tussen twee sessies door, pas dan
`data/scenario.json` aan en niet de code.

Zet `app.debug` in `config.local.php` op `false` voordat je aan publiek demonstreert. Een
fout wordt dan een gewoon scherm in plaats van een stacktrace.

## Op een server zetten

Lokaal en op een server draait dezelfde code; het verschil zit alleen in
`config.local.php`. Dat bestand staat in `.gitignore`, dus je maakt het op de server zelf
aan:

```php
<?php

declare(strict_types=1);

return [
    'app' => [
        'debug' => false,   // geen stacktrace op het scherm
        'demo'  => false,   // geen demo-account, het wachtwoord staat in deze README
    ],
    'db' => [
        'host'     => '127.0.0.1',
        'name'     => 'draagvlak',
        'user'     => 'draagvlak',
        'password' => 'iets wat niemand raadt',
    ],
];
```

Verder:

1. **Wijs de document root naar `public/`.** Dat is de belangrijkste stap: staat hij op de
   projectmap, dan is `config.php` met je wachtwoord te downloaden. De `.htaccess` in de
   wortel vangt dat op bij Apache, maar het is een vangnet en geen oplossing.
2. **Zet de site achter HTTPS.** De sessiecookie krijgt dan vanzelf de `secure`-vlag.
3. **Draai `composer db:setup` op de server.** Met `app.demo` op `false` maakt hij alleen
   de tabellen: geen demo-account en geen scenario-inhoud.
4. **Neem je lokale database niet mee.** Daar staat `test@example.com` met het wachtwoord
   `password` in, en dat staat hierboven te lezen.
5. `composer install` hoeft niet eens. De app heeft geen enkele runtime-dependency: de
   autoloader zit in `bootstrap.php` en `vendor/` bevat alleen de formatter. Getest door
   de map weg te halen en de app te draaien.

En de vraag die daaronder zit: dit is een onderzoeksprototype. Het is gebouwd om in een
sessie op een paar mensen getest te worden, niet om open op het internet te staan waar
iedereen zich kan inschrijven. Wil je het publiek zetten, overleg dat eerst met het team.

## Rechten en gebruik

Deze repository staat publiek zodat je hem kunt lezen, nakijken en beoordelen. Dat is
iets anders dan open source: er zit geen MIT- of andere open licentie op, en die komt er
ook niet.

Kijken mag. Overnemen niet. Kopiëren, aanpassen, publiceren of hergebruiken van welk deel
dan ook kan alleen met schriftelijke toestemming van Bram van Dijke en Richie van der
Heij. Inleveren als je eigen schoolwerk is plagiaat. De volledige tekst staat in
[LICENSE](LICENSE).

Meeschrijven doet alleen wie als collaborator is toegevoegd. De rest van GitHub kan de
repository lezen en forken, want dat hoort bij publiek staan en valt niet uit te zetten.
