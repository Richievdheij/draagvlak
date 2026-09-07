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
git switch -c feature/contacten-scherm
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

```
Add contacts screen with response time sorting
Fix menu staying open after resizing to desktop
Move response time formatting out of the partial
```

Dus niet "changes", "update", "wip" of "aanpassing gedaan". Een commit die je over drie
weken niet meer kunt plaatsen is een verloren commit.

Commit vaak en klein. Eén commit die zeven bestanden en drie onderwerpen raakt is niet
te reviewen en niet terug te draaien.

## Pull requests

Push je branch en open de pull request **naar `develop`**, nooit naar `main`. Het
sjabloon staat er automatisch in: zet erin wat er verandert, waarom, en welk scherm de
ander moet openen om het te zien. Vraag iemand uit het team om te kijken voordat je
merget.

```bash
git push -u origin feature/contacten-scherm
```

Een PR die alleen bestaat uit opmaakwijzigingen op bestanden waar je verder niets aan
deed, kun je beter niet maken. Herformatteer geen code die je niet aan het aanpassen
bent.

## Voordat je pusht

```bash
composer lint
```

En open het scherm dat je hebt aangepast echt in de browser, ook in apparaatweergave op
een telefoon. Een groene lint zegt alleen dat je haakjes kloppen, niet dat de knop werkt.

Loop deze vijf punten langs:

De code is Engels en de schermtekst is Nederlands. Er staat geen Nederlandse tekst in
`src/`.

Elke nieuwe functie in `src/` heeft een docblock die uitlegt waarom hij bestaat.

Alles wat je print gaat door `e()`.

Er staat geen losse hexcode of pixelwaarde buiten `tokens.css`.

Je nieuwe scherm staat in `public/`, de opmaak ervan in `assets/css/pages/` onder
dezelfde naam, je herhaalde stukjes HTML in `views/partials/`, en je rekenwerk in `src/`.
Registreren hoef je niets: een nieuw bestand in `src/` of in een CSS-map doet het meteen.

Elke query is een prepared statement met vraagtekens, en je zoekt een rij altijd samen
met het id van de ingelogde gebruiker op.

## Wat je eerst overlegt

Een dependency toevoegen aan `composer.json` of `package.json`. Dit project draait
bewust zonder framework, zonder bundler en zonder ORM.

De manier waarop `bootstrap.php` de bestanden uit `src/` inlaadt, en de afspraak tussen
`page()` en `views/layouts/app.php`. Elk scherm hangt daaraan.

De getallen in `src/score/score-rules.php`. Die zijn het scenario, geen technische
keuze: verander je ze in je eentje, dan zijn de testsessies onderling niet meer te
vergelijken.

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
