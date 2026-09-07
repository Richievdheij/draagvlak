# Designsysteem

Alle kleuren, lettertypes, afstanden en hoekjes staan in één bestand:
`public/assets/css/tokens.css`. Wil je de hele app een andere kleur geven, dan pas je
daar een regel aan en verder niets. Zie je ergens anders een losse hexcode of een los
pixelgetal staan, dan is dat een fout die je mag herstellen.

## De richting, en waarom

Draagvlak ziet eruit als rustige instellingssoftware: veel wit, diep petrolblauw, dunne
lijnen, kleine hoekjes, geen schaduwen. Zoals iets van een gemeente of een
zorgverzekeraar.

Dat is een keuze en geen gebrek aan fantasie. Een scoresysteem dat er dreigend uitziet,
gelooft niemand, en dan test je alleen nog of mensen enge interfaces eng vinden. Het
oncomfortabele moet uit de tekst en de cijfers komen, niet uit de vormgeving. Maak het
dus niet donkerder, futuristischer of "dystopischer", en voeg geen gradients,
glaseffecten of zwevende kaartjes toe.

## Drie lagen tokens

Je gebruikt bijna altijd de middelste laag.

**Primitieven** zijn de kale kleuren: `--civic-700`, `--paper-100`, `--alarm-600`. Die
gebruik je nooit rechtstreeks in een component; ze bestaan alleen om de laag hieronder
mee te bouwen.

**Semantische tokens** zeggen waar iets voor is: `--color-surface`, `--color-text`,
`--color-accent`, `--color-danger`. Dit is wat je in `base.css`, `components.css` en
`screens.css` schrijft.

**Componenttokens** zet je in een component zelf, als dat component één waarde nodig
heeft die verder nergens bestaat.

Waarom die tussenlaag? Omdat je dan één keer beslist wat "de kleur van een rand" is, in
plaats van vijftig keer te kiezen welk grijs. En omdat licht en donker daardoor
automatisch meelopen.

| Token | Waarvoor |
| --- | --- |
| `--color-background` | de achtergrond van de pagina |
| `--color-surface` | kaarten, header, footer, invoervelden |
| `--color-surface-sunken` | een blok dat een niveau dieper ligt, zoals een codeblok |
| `--color-text` | alle gewone tekst |
| `--color-text-muted` | bijschriften en uitleg |
| `--color-text-inverse` | tekst op een gekleurd vlak |
| `--color-border` | randen en scheidslijnen |
| `--color-border-strong` | de rand van een invoerveld of een lege knop |
| `--color-accent` | de kleur van het systeem: knoppen, links, actief menu-item |
| `--color-accent-soft` | zachte vulling in die kleur |
| `--color-on-accent` | tekst op de accentkleur |
| `--color-danger` | verlies van punten, afwijzing, foutmelding |
| `--color-success` | winst van punten, bevestiging |
| `--color-focus` | de rand om het element waar je toetsenbord staat |

Rood en groen betekenen hier altijd hetzelfde: rood is punten kwijt, groen is punten
erbij. Gebruik ze nergens anders voor, anders wordt de app onleesbaar.

## Licht en donker

Elke semantische token is één regel met twee waarden:

```css
--color-surface: light-dark(var(--paper-0), var(--night-800));
```

De browser kiest de eerste waarde in een licht thema en de tweede in een donker thema.
Er is dus geen tweede kopie van het palet en geen media query per component. De knop in
de footer zet `data-theme="light"` of `data-theme="dark"` op de pagina; staat er niets,
dan volgt hij de instelling van het apparaat.

Voeg je een kleur toe, dan hoort daar dus meteen een donkere variant bij. Eén waarde
zonder `light-dark()` is bijna altijd een vergissing.

## Typografie

Twee lettertypes, met een duidelijke taakverdeling.

**Outfit** is de stem van het systeem: koppen, de naam bovenin, een getal dat moet
landen. Een geometrische schreefloze die zelfverzekerd oogt zonder luid te worden.

**Inter** is alles wat je als zin leest: lopende tekst, knoppen, labels, meldingen. Hij
is gemaakt voor schermen en blijft klein leesbaar.

Beide staan als woff2 in `public/assets/fonts/` en worden geladen in `fonts.css`. Ze
komen dus niet van een CDN: tijdens een testsessie op een slechte verbinding staat de
typografie er gewoon, en er gaat geen verzoek naar buiten. Voeg geen derde lettertype toe.

Getallen die veranderen of onder elkaar staan krijgen `class="numeric"`. Dat zet
tabelcijfers aan, zodat een cijfer niet verspringt terwijl het optelt.

De schaal staat in tokens: `--text-3xl` tot `--text-xs`. De drie grootste maten
schalen mee met het scherm via `clamp()`, dus een kop hoeft geen media query om op een
telefoon niet te schreeuwen. Gebruik geen tussenmaten die er niet in staan.

## Ruimte en vorm

Afstanden gaan met `--space-1` tot `--space-9`, van 4 tot 96 pixels. Er zit geen enkele
andere waarde in het ontwerp. Hoeken lopen van `--radius-xs` (2px) tot `--radius-lg`
(14px) en `--radius-pill` voor een badge. Diepte maak je met een haarlijn en niet met een
schaduw.

De breedte van de inhoud staat ook in tokens: `--width-page` voor de hele kolom,
`--width-prose` voor tekst die leesbaar moet blijven, en `--gutter` voor de marge links
en rechts, die meegroeit met het scherm.

## De CSS-bestanden

Ze worden in deze volgorde ingeladen en je mag die volgorde niet omdraaien.

`fonts.css` bevat alleen de `@font-face`-regels.

`tokens.css` bevat alleen variabelen en de themaschakelaar. Geen componenten.

`base.css` bevat de reset, hoe kale HTML-elementen eruitzien, en de layouthulpjes:
`.container`, `.section`, `.stack`, `.prose`, `.visually-hidden`.

`components.css` bevat alles wat op meer dan één scherm voorkomt: de header, het menu,
knoppen, kaarten, meldingen, invoervelden, de badge, de footer.

`screens.css` bevat wat maar op één scherm bestaat. Heb je iets uit dit bestand op een
tweede plek nodig, verplaats het dan naar `components.css`.

Zet je een nieuw CSS-bestand in `assets/css/`, dan wordt het vanzelf ingeladen, na deze
vijf en op alfabetische volgorde. Je hoeft niets toe te voegen aan de layout.

## Klassen schrijven

We gebruiken BEM-achtige namen: `.blok`, `.blok__onderdeel`, `.blok--variant`. Een
toestand die JavaScript aan- en uitzet heet `.is-iets`.

```html
<div class="contact-row contact-row--slow">
    <span class="contact-row__name">Farah Yildiz</span>
</div>
```

Er staat maar één klasse in een selector, dus geen `.section .contact-row span`. Zo kan
niemand per ongeluk iets overschrijven wat hij niet bedoelde, en werkt een component
overal hetzelfde.

## Mobiel

Dit is een responsieve site en geen telefoonschil. De opmaak is geschreven voor een
smalle telefoon en groeit daarna mee: de marges worden groter, het menu klapt open naar
een rij, en de kaarten leggen zichzelf naast elkaar zodra er ruimte is. Er is precies
één breekpunt, bij 48em, en dat is genoeg.

Test in de browser op 390 pixels breed én op een normale laptopbreedte. Klopt het op
allebei, dan ben je klaar.

## Toegankelijkheid

Dit is de ondergrens, geen extraatje. Contrast minimaal AA. De focusrand blijft zichtbaar
en wordt nooit weggehaald. Elk invoerveld heeft een label. Knoppen zijn minstens 44 bij
44 pixels, want daaronder mis je ze met je duim. Beweging staat uit voor mensen die dat
in hun systeem hebben aangegeven, en dat is al geregeld in `tokens.css`. Iconen die
betekenis dragen krijgen tekst erbij of een `aria-label`.

Bovenaan elke pagina staat een verborgen link "Direct naar de inhoud", die zichtbaar
wordt zodra je met tab begint. Laat die staan.
