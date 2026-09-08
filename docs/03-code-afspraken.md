# Code-afspraken

Drie zinnen, en de rest van dit document zit eronder. De code is Engels en het scherm is
Nederlands. Alles wat bij één onderwerp hoort staat in één map. Comments zijn kort en
staan er alleen als ze iets zeggen wat de code niet zegt.

## Engels of Nederlands

Engels is alles wat alleen jij en je teamgenoten zien: klassenamen, methodes, variabelen,
comments, docblocks, CSS-klassen, JSON-sleutels, **bestandsnamen, URL's**, commit
messages, branchnamen en meldingen in de console.

Nederlands is alles wat de deelnemer op het scherm leest: knopteksten, koppen,
foutmeldingen, meldingen bovenin, en deze documentatie.

De grens loopt bij de map. **Nederlands staat in `views/`**, en nergens anders. Er is één
uitzondering: `title()` in een pagina-klasse geeft de Nederlandse naam van het scherm
terug, want dat is de naam van het scherm en hij staat in het tabblad.

Een klasse geeft dus een Engelse sleutel terug, en de zin die daarbij hoort staat op de
plek waar hij geprint wordt:

```php
// src/Features/Messages/MessageRepository.php
return new Outcome('answered_in_time', $message->contactName, $delta);
```

```php
// views/components/notices.php
'answered_in_time' => [
    'tone' => 'success',
    'title' => 'Op tijd gereageerd',
    'text' => '{contact} kreeg je bericht binnen de tijd. Je draagvlak gaat 1 punt omhoog.',
],
```

Zo kun je de tekst aanpassen zonder de logica aan te raken, en staat elke zin die de app
na een keuze kan zeggen in één lijst. Vergeet je die regel, dan wordt er niets geprint;
de sleutel zelf komt nooit op het scherm.

Hetzelfde geldt voor foutmeldingen in een formulier (een sleutel uit `Registration`, de
Nederlandse tekst boven in de template), voor de menulabels in
`views/layout/site-nav.php`, en voor de labels van de themaknop en de kopieerknop, die als
data-attribuut in de template staan in plaats van in de JavaScript.

## Waar iets hoort

Alles wat bij één onderwerp hoort staat in één map, en `src/Features/`,
`views/features/` en `assets/css/features/` zijn identiek ingedeeld.

- Hoort het bij één feature? Dan staat het daarin, helemaal.
- Gebruiken twee features het? Dan is het een component: `views/components/` en
  `assets/css/components/`.
- Is het leidingwerk waar elke feature op leunt? Dan `src/Core/`.

Een scherm is vier bestanden waarvan de namen uit elkaar volgen:

```
public/contacts.php                             de URL
src/Features/Contacts/Pages/ContactsPage.php    wat het doet
views/features/contacts/contacts.php            hoe het eruitziet
public/assets/css/features/contacts/            hoe het is opgemaakt
```

## Namen

| Wat                        | Schrijfwijze                                | Voorbeeld                      |
| -------------------------- | ------------------------------------------- | ------------------------------ |
| Namespace                  | `Draagvlak\` plus de mappen                 | `Draagvlak\Features\Contacts`  |
| Map in `src/`              | hoofdletter, gelijk aan de namespace        | `src/Features/Auth/Pages`      |
| Elke andere map            | kleine letters, kebab-case                  | `views/features/contacts`      |
| Bestand met een klasse     | PascalCase, één klasse per bestand          | `ContactRepository.php`        |
| PHP-klasse                 | PascalCase, `final` tenzij er iets van erft | `ScoreBoard`                   |
| PHP-methode en variabele   | camelCase                                   | `$secondsWaiting`              |
| PHP-constante              | HOOFDLETTERS, met type                      | `public const int START_SCORE` |
| Pagina-klasse              | wat het scherm is, plus `Page`              | `EmergencyContactsPage`        |
| URL, bestand, template     | Engels, kleine letters, kebab-case          | `emergency-contacts.php`       |
| Layout- en componentstukje | kebab-case                                  | `message-card.php`             |
| Tabel in de database       | meervoud, snake_case                        | `score_events`                 |
| Kolom in de database       | snake_case                                  | `respond_within_seconds`       |
| CSS-bestand                | heet naar wat het opmaakt                   | `components/message-card.css`  |
| CSS-blok                   | kebab-case                                  | `.message-card`                |
| CSS-onderdeel              | dubbele underscore                          | `.message-card__name`          |
| CSS-variant                | dubbel streepje                             | `.message-card--urgent`        |
| CSS-toestand               | `is-` ervoor                                | `.is-open`                     |
| CSS-variabele              | `--groep-naam`                              | `--color-accent`               |
| JS-functie en variabele    | camelCase                                   | `startComponents`              |
| JS-bestand                 | kebab-case                                  | `nav-toggle.js`                |
| JSON-sleutel               | camelCase                                   | `respondWithinSeconds`         |
| Data-attribuut in HTML     | kebab-case                                  | `data-component`               |

Namen beschrijven wat iets is, niet hoe het eruitziet. Dus `.contact-row` en niet
`.grey-box`, en `$secondsWaiting` en niet `$x`. Een klasse die `.red-text` heet is fout,
ook als de tekst rood is: verandert de kleur, dan klopt de naam niet meer.

Let op die ene naad: in de database heet een kolom `respond_within_seconds`, en dezelfde
waarde heet in PHP en in JSON `respondWithinSeconds`.

## Klassen

Een klasse is `final`, tenzij er iets van hoort te erven. Op dit moment zijn alleen
`Page` en `Action` dat niet.

Eigenschappen zijn `private readonly`, tenzij een template ze nodig heeft. Een object dat
alleen waarden bij elkaar houdt en nooit verandert is een `final readonly class`, zoals
`Account`, `Contact`, `Message` en `Outcome`.

Een klasse krijgt wat hij nodig heeft in zijn constructor. Niets bouwt halverwege een
methode zijn eigen database of sessie, en niets grijpt naar een globale variabele. Alles
komt bij elkaar in `App::__construct()`.

`static` gebruik je alleen voor iets wat nergens van afhangt en dus altijd hetzelfde
antwoord geeft: escapen (`Html::e()`), een URL bouwen (`Url::to()`), een bedrag opmaken
(`Format::price()`), een regel van het scenario (`ScoreRules::forAnswer()`). Alles met
een verbinding, een sessie of een bezoeker erin is een gewoon object.

## Comments

Kort. Een comment verdient zijn plek door iets te zeggen wat de code niet zegt. Staat er
hetzelfde als op de regel eronder, dan haal je hem weg.

**Een klasse** krijgt één zin waarom hij bestaat. Een tweede alleen als er anders een
regel van het scenario verloren gaat.

**Een methode** krijgt niets als de naam en de signature het al zeggen:

```php
public function isPost(): bool                      // geen docblock
public function emailExists(string $email): bool    // geen docblock
```

Eén regel als er een addertje zit:

```php
/** Null when the contact is not this participant's. */
public function find(int $userId, int $contactId): ?Contact
```

Meer alleen als een aanroeper het anders fout doet.

**Annotaties** zijn niet optioneel, want er is geen statische analyse en je editor heeft
verder niets:

| Schrijf                             | Wanneer                                                     |
| ----------------------------------- | ----------------------------------------------------------- |
| `@param array<string, mixed> $data` | Altijd bij een `array`. `array` alleen zegt niets.          |
| `@return list<Contact>`             | Altijd bij een `array`. Beter nog: geef een object terug.   |
| `@var array<string, string>`        | Op een property waarvan het type een array is.              |
| `@throws RuntimeException When …`   | Bij een exception die de aanroeper moet afvangen.           |
| `@var Account $account`             | In elke template, voor elke variabele die hij binnenkrijgt. |

Schrijf nooit `@param string $name Name of the account.` als er al `string $name` staat.
Dat is ruis met een type erop.

Een template begint met één zin en zijn `@var`-lijst, netjes onder elkaar:

```php
/**
 * Home: your number, then the people behind it.
 *
 * @var Account       $account  The visitor.
 * @var list<Message> $messages Messages still waiting on a decision.
 */
```

**Een inline comment** alleen als kloppende code fout lijkt. In dit project is dat bijna
altijd het scenario zelf:

```css
/* The way out is always the quietest button on the screen. That is not
   sloppiness, it is the subject of this prototype. */
```

Hooguit twee regels. Zonder die comment haalt een teamgenoot het weg omdat het op een bug
lijkt.

**Nooit**: een comment die de volgende regel herhaalt, geschiedenis ("was vroeger", "sinds
de test", "gecheckt op maandag"), een naam, een datum, de naam van een AI-tool,
uitgecommentarieerde code, of een streepjesbalk om een blok af te scheiden.

Elk CSS-bestand opent met één of twee regels: wat het opmaakt en bij welke template het
hoort. Elke geëxporteerde JS-functie krijgt JSDoc met types, want er is geen TypeScript;
interne hulpfuncties krijgen één regel zonder tags.

## Opmaak doet de computer

```bash
composer format     # PHP, via pint.json
npm run format      # CSS, JS en Markdown, via .prettierrc
composer check      # syntax en opmaak, voordat je pusht
```

Allebei draaien ze bij het opslaan in VS Code. Handmatig uitlijnen hoeft dus niet: vier
spaties, LF, een lege regel aan het eind, PSR-12, gesorteerde imports zonder ongebruikte,
komma's achter het laatste argument, een lege regel voor een `return` en voor een blok, en
één lege regel tussen de onderdelen van een klasse.

Twee dingen laten ze expres aan jou:

- **Hoe je een docblock uitlijnt.** Een kolom `@var`-types is leesbaar; maak hem leesbaar.
- **Waar een lege regel in een methode staat.** Zet de regels die bij elkaar horen bij
  elkaar en een lege regel ertussen. Een methode die als twee of drie korte alinea's
  leest, is makkelijker dan één muur.

```php
protected function submit(): void
{
    $this->requireValidCsrf('contacts');

    $account = $this->app->auth->user();

    $outcome = match ($this->app->request->input('action')) {
        'add' => $this->app->contacts->addByCode($account, $this->app->request->text('code')),
        default => null,
    };

    if ($outcome instanceof Outcome) {
        $this->app->session->flash($outcome->result, $outcome->values());
    }

    Response::redirect('contacts');
}
```

Eerst de bewaking, dan wat je nodig hebt, dan de beslissing, dan weg. Elke keer dezelfde
vorm.

## Escapen

Alles wat je print en niet zelf letterlijk hebt getypt gaat door `$this->e()`:

```php
<span><?= $this->e($contact->name) ?></span>
```

Vergeet je dat, dan kan een naam met een `<script>` erin je pagina overnemen. Bouw je
attributen op uit data, gebruik dan `$this->attributes()`.

## Queries

Elke query staat in een repository binnen zijn eigen feature, en is een prepared
statement:

```php
$contacts = $this->database->all('SELECT * FROM contacts WHERE user_id = ?', [$userId]);
```

Nooit zo, ook niet met een getal dat je zelf hebt bedacht:

```php
$contacts = $this->database->all("SELECT * FROM contacts WHERE user_id = $userId");
```

En zoek een rij altijd samen met het id van de ingelogde gebruiker op.

## Formulieren

Een formulier gaat met POST, krijgt `<?= $this->csrfField() ?>` erin, en de pagina die hem
afhandelt begint `submit()` met `$this->requireValidCsrf('scherm')` en eindigt met
`Response::redirect()`. Zonder die redirect verstuurt een deelnemer die de pagina ververst
zijn antwoord nog een keer.

## Wat niet mag

Geen `@` om een foutmelding te onderdrukken, geen `eslint-disable`, geen
`@phpstan-ignore`. Als een tool klaagt, los je de oorzaak op.

Geen dode code, geen ongebruikte variabelen, geen methode die nergens vandaan wordt
aangeroepen. Weg ermee; Git onthoudt het wel.

Niets op het scherm dat alleen voor het team bestaat: geen testaccount, geen "demo", geen
mensen die er zomaar in staan. Dat hoort in de README.

## Voordat je pusht

```bash
composer check
npm run check
```

En open het scherm dat je hebt aangepast echt even in de browser, ook op telefoonbreedte.
Een groene check zegt alleen dat je haakjes kloppen en je opmaak klopt.
