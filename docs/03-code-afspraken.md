# Code-afspraken

Kort samengevat: de code is Engels, het scherm is Nederlands. Alle namen volgen één
vaste schrijfwijze, en je documenteert waarom iets bestaat en niet wat de volgende regel
doet. Onder die drie zinnen zit de rest van dit document.

## Engels of Nederlands

Engels is alles wat alleen jij en je teamgenoten zien: functienamen, variabelen,
comments, docblocks, CSS-klassen, JSON-sleutels, bestandsnamen, commit messages,
branchnamen en meldingen in de console.

Nederlands is alles wat de deelnemer op het scherm leest: knopteksten, koppen,
foutmeldingen, meldingen bovenin, en deze documentatie.

Dat botst soms, en dan geldt deze regel: een interne sleutel blijft Engels, ook als hij
iets Nederlands betekent. Een functie geeft `strong`, `tight`, `low` of `critical` terug;
de Nederlandse zin die daarbij hoort staat in de partial die hem print. Zo blijft `src/`
vrij van schermtekst en kun je de tekst aanpassen zonder de logica aan te raken.

Er staat dus nooit Nederlandse tekst in `src/`. In een pagina of een partial mag het wel,
want dat zijn de plekken waar tekst hoort. Daarom staan de menulabels in
`views/partials/site-nav.php` en niet in een helper, en staan de labels van de
themaknop als data-attribuut in `views/partials/site-footer.php` in plaats van in
`theme.js`.

## Namen

| Wat | Schrijfwijze | Voorbeeld |
| --- | --- | --- |
| Map | kleine letters, kebab-case | `src/support` |
| PHP-functie | camelCase | `penaltyForDelay()` |
| PHP-class | PascalCase | `ScoreCalculator` |
| PHP-constante | HOOFDLETTERS_MET_STREEPJES | `DATA_PATH` |
| PHP-variabele | camelCase | `$secondsWaiting` |
| Bestand met functies | kebab-case | `json-store.php` |
| Bestand met een class | PascalCase | `ScoreCalculator.php` |
| Pagina in `public/` | Nederlands, kleine letters | `noodcontacten.php` |
| Layout en partial | kebab-case | `site-header.php` |
| CSS-blok | kebab-case | `.contact-row` |
| CSS-onderdeel | dubbele underscore | `.contact-row__name` |
| CSS-variant | dubbel streepje | `.contact-row--slow` |
| CSS-toestand | `is-` ervoor | `.is-open` |
| CSS-variabele | `--groep-naam` | `--color-accent` |
| JS-functie en variabele | camelCase | `startComponents` |
| JS-bestand | kebab-case | `nav-toggle.js` |
| JSON-sleutel | camelCase | `responseMinutes` |
| Data-attribuut in HTML | kebab-case | `data-component` |

Paginabestanden zijn Nederlands omdat ze in de URL terechtkomen en de deelnemer die
ziet. Verder is alles Engels.

Namen beschrijven wat iets is, niet hoe het eruitziet. Dus `.contact-row` en niet
`.grey-box`, en `$secondsWaiting` en niet `$x`. Een klasse die `.red-text` heet is fout,
ook als de tekst rood is: verandert de kleur, dan klopt de naam niet meer.

## Comments en docblocks

Boven elke functie in `src/`, elke geëxporteerde functie in een JS-module, elke layout en
elke partial staat een docblock. Die legt uit waarom het ding bestaat en wat een
aanroeper moet weten. Wat er regel voor regel gebeurt hoeft er niet in, want dat staat er
al onder.

In PHP:

```php
/**
 * Points lost for leaving a message unanswered for a number of seconds.
 *
 * The penalty grows one point per interval and stops at a maximum, so a
 * participant who walks away does not end the session at zero.
 *
 * @param int $secondsWaiting Seconds between the message arriving and the reply.
 */
function penaltyForDelay(int $secondsWaiting): int
```

`@param` en `@return` zet je erbij als er iets uit te leggen valt. Staat er al
`int $score` in de signature en heet hij `$score`, dan voegt `@param int $score` niets
toe en laat je hem weg. Bij een array zet je hem er wél bij, want daar zegt het type
`array` niets: `@return list<array{delta: int, reason: string}>` maakt het verschil
tussen wel en geen autocomplete.

Elke layout en elke partial begint met een docblock waarin staat welke variabelen hij
verwacht:

```php
/**
 * @var array<string, mixed> $contact One entry from data/contacts.json.
 * @var string|null          $href    Optional link target for the whole row.
 */
```

Dat is geen formaliteit. Die variabelen komen binnen via `extract()` en je editor kan
ze niet zelf afleiden, dus zonder die regels heb je geen enkele hulp.

In JavaScript gebruik je JSDoc met types erin, omdat er geen TypeScript is en JSDoc het
enige is waar je editor iets aan heeft:

```js
/**
 * The menu button that opens the navigation on a phone.
 *
 * @param {HTMLElement} button Button with aria-controls and aria-expanded.
 * @returns {void}
 */
export function init(button) {
```

Interne hulpfuncties die niet geëxporteerd worden krijgen één regel uitleg zonder tags.

### Wanneer je een inline comment schrijft

Alleen als de code er fout uitziet terwijl hij dat niet is. In dit project is dat bijna
altijd het scenario zelf: een schakelaar die niet werkt, een grijze uitwegknop, een
aftrek die doorloopt als je wegloopt. Zonder comment haalt een teamgenoot dat weg omdat
het op een bug lijkt.

```css
/* The way out is always greyer than the option we want you to take. That is
   not sloppiness, it is the subject of this prototype. */
```

### Wanneer je er geen schrijft

Bij een regel die zichzelf uitlegt. `$score = $score - 1; // trek er één af` is ruis.

Verder staat er nooit uitgecommentarieerde code in de repo, want daar is Git voor. En er
staat nooit geschiedenis in een comment: "was vroeger", "sinds de test", "gecheckt op
maandag". Dat hoort in je commit message. Namen en datums horen er ook niet in.

## Escapen

Alles wat je print en niet zelf letterlijk hebt getypt gaat door `e()`:

```php
<span><?= e($contact['name']) ?></span>
```

Vergeet je dat, dan kan een naam met een `<script>` erin je pagina overnemen. Ook al
komt de data hier uit je eigen JSON: de gewoonte is het punt, want in een echt project
komt hij van een gebruiker.

Bouw je attributen op uit data, gebruik dan `attributes()`. Die escapet elke waarde en
laat een attribuut weg als de waarde `null` of `false` is.

## Formulieren

Een formulier gaat met POST, krijgt `<?= csrfField() ?>` erin, en de pagina die hem
afhandelt controleert met `isValidCsrf()` en eindigt met `redirect()`. Zonder die
redirect verstuurt een deelnemer die de pagina ververst zijn antwoord nog een keer.

## Wat niet mag

Geen `@` om een foutmelding te onderdrukken, geen `eslint-disable`, geen
`@phpstan-ignore`. Als een tool klaagt, los je de oorzaak op. Kom je er niet uit, vraag
het dan in de groepsapp in plaats van de melding weg te zetten.

Geen dode code, geen ongebruikte variabelen, geen functie die nergens vandaan wordt
aangeroepen. Weg ermee; Git onthoudt het wel.

## Voordat je pusht

```bash
composer lint     # controleert elk PHP-bestand op syntaxfouten
npm run lint      # controleert de JavaScript
npm run format    # zet CSS, JS en Markdown recht (optioneel maar prettig)
```

En open het scherm dat je hebt aangepast echt even in de browser, ook op telefoonbreedte.
Een groene lint zegt alleen dat je haakjes kloppen.
