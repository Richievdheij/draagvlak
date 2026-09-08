# Meewerken aan dit project

Lees eerst [docs/02-architectuur.md](docs/02-architectuur.md) en
[docs/03-code-afspraken.md](docs/03-code-afspraken.md). Dit document gaat alleen over
de werkwijze: branches, commits en wat er moet kloppen voordat je iets pusht.

## Branches

Er zijn twee vaste branches.

`develop` is waar we werken. Alles komt daar samen en die branch mag best een keer
half af zijn.

`main` is waar een demo van draait. Daar komt alleen iets in vanuit `develop`, als het
werkt. Werk dus nooit rechtstreeks op `main`, ook niet voor een kleine wijziging.

Je begint altijd vanaf `develop`:

```bash
git switch develop
git pull
git switch -c feature/contacts-screen
```

Geef je branch een Engelse naam met een prefix die zegt wat het is:

```
feature/contacts-screen
fix/menu-stays-open-on-resize
docs/research-findings
```

Eén branch per onderwerp. Zit je aan twee losse dingen te werken, maak dan twee
branches.

## Commits

Engels, tegenwoordige tijd, zeg wat de wijziging doet en niet wat jij hebt gedaan.

De vorm is `type(scope): korte titel`, dan een lege regel, dan een body die vertelt wat
er verandert en waarom. Welke types en scopes er zijn, en wat er nooit in een
commitbericht hoort, staat in
[.github/commit-instructions.md](.github/commit-instructions.md).

```
feat(contacts): add the contacts screen with a response window
fix(home): keep the menu closed after resizing to desktop
refactor(messages): move response time formatting out of the partial
```

Dus niet "changes", "update", "wip" of "aanpassing gedaan". Een commit die je over drie
weken niet meer kunt plaatsen is een verloren commit.

Verplicht is die vorm niet: typ je er zelf een zonder prefix, dan gaat er niets stuk. Wel
schrijft de knop met het sterretje boven het commitvak in Source Control hem zo, met een
body erbij, omdat dat bestand eraan gekoppeld staat. Eén regel is over drie maanden een
stuk minder waard dan drie zinnen die zeggen waarom.

Commit vaak en klein. Eén commit die zeven bestanden en drie onderwerpen raakt is niet
te reviewen en niet terug te draaien.

## Pull requests

Push je branch en open de pull request **naar `develop`**, nooit naar `main`. Het
sjabloon staat er automatisch in: zet erin wat er verandert, waarom, en welk scherm de
ander moet openen om het te zien. Vraag iemand uit het team om te kijken voordat je
merget.

```bash
git push -u origin feature/contacts-screen
```

Een PR die alleen bestaat uit opmaakwijzigingen op bestanden waar je verder niets aan
deed, kun je beter niet maken. Herformatteer geen code die je niet aan het aanpassen
bent.

## Voordat je pusht

```bash
composer check    # de PHP: syntax en opmaak
npm run check     # de CSS, JavaScript en Markdown, als je die hebt aangeraakt
```

En open het scherm dat je hebt aangepast echt in de browser, ook in apparaatweergave op
een telefoon. Een groene check zegt alleen dat je haakjes en je opmaak kloppen, niet dat
de knop werkt.

Loop deze punten langs:

Alles wat bij één onderwerp hoort staat in één map. Je scherm is vier bestanden waarvan
de namen uit elkaar volgen: `public/<scherm>.php`,
`src/Features/<Naam>/Pages/<Naam>Page.php`, `views/features/<naam>/<scherm>.php` en
`assets/css/features/<naam>/`. Gebruiken twee features iets, dan is het een component.
Registreren hoef je niets.

De code is Engels en de schermtekst is Nederlands. Bestandsnamen en URL's zijn ook
Engels. Er staat geen Nederlandse zin in `src/`, op `title()` in een pagina-klasse na.
Elke melding die je met `flash()` in de wachtrij zet heeft een regel in
`views/components/notices.php`.

Elke klasse heeft één zin waarom hij bestaat. Een methode heeft alleen een docblock als
de naam en de signature het niet al zeggen. Elke `array` heeft een vorm in de docblock,
en elke template noemt bovenin welke variabelen hij verwacht.

Alles wat je print gaat door `$this->e()`.

Er staat geen losse hexcode of pixelwaarde buiten `tokens.css`, en de focusrand is nog
steeds de enige focusrand.

Elke query staat in een repository binnen zijn eigen feature, is een prepared statement
met vraagtekens, en zoekt een rij altijd samen met het id van de ingelogde gebruiker op.

Er staat niets op het scherm dat alleen voor het team bestaat: geen testaccount, geen
demo-melding, geen mensen die er zomaar in staan.

## Wat je eerst overlegt

Een dependency toevoegen aan `composer.json` of `package.json`. Dit project draait
bewust zonder framework, zonder bundler en zonder ORM. De enige die er staat is de
formatter, en die is `require-dev`.

De manier waarop `bootstrap.php` de `App` bouwt, en de vier stappen in
`src/Core/Page.php`. Elk scherm hangt daaraan.

De getallen in `src/Features/Score/ScoreRules.php`. Die zijn het scenario, geen
technische keuze: verander je ze in je eentje, dan zijn de testsessies onderling niet meer
te vergelijken.

De regels in `pint.json`, `.prettierrc` of `.vscode/`. Die bepalen hoe elk
bestand van iedereen eruitziet, dus een wijziging daar raakt de hele repo in één keer.

Een kleur of een lettertype vervangen in `base/tokens.css`. Eén token bijstellen is
normaal werk; het palet omgooien niet.

`database/schema.sql` aanpassen, want daarna moet iedereen `composer db:fresh` draaien en
is zijn lokale data weg. En helemaal niet terwijl er iemand aan het testen is; dat geldt
ook voor `data/scenario.json`.

## Je eigen databasegegevens

Draait MySQL bij jou met een wachtwoord of op een andere poort? Zet dat niet in
`config.php`, maar maak `config.local.php` met alleen jouw regels erin:

```php
<?php

return ['db' => ['user' => 'draagvlak', 'password' => 'secret']];
```

Dat bestand staat in `.gitignore` en blijft dus op jouw machine.

## AI

Gebruik het gerust. Zorg wel dat het model de instructies van deze repo kent, anders
krijg je antwoorden die hier niet passen. Hoe je dat instelt staat in
[docs/07-ai-gebruiken.md](docs/07-ai-gebruiken.md).

Wat een model oplevert is een voorstel en geen resultaat. Zoek elke functienaam op in de
repo voordat je hem overneemt, en draai het scherm. Jij zet je naam onder de commit.
