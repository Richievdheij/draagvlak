# Designsysteem

Alle kleuren, lettertypes, afstanden en hoekjes staan in één bestand:
`public/assets/css/base/tokens.css`. Wil je de hele app een andere kleur geven, dan pas
je daar een regel aan en verder niets. Zie je ergens anders een losse hexcode of een los
pixelgetal staan, dan is dat een fout die je mag herstellen.

## De richting, en waarom

Draagvlak ziet er vriendelijk uit: zachte mint, witte kaarten op een lichtgroene
achtergrond, ronde hoeken, dunne lijntjes en nergens een slagschaduw. Het lijkt op een
app die je al op je telefoon hebt staan.

Dat is een keuze en geen gebrek aan durf. Een scoresysteem dat er dreigend uitziet,
gelooft niemand, en dan test je alleen nog of mensen enge interfaces eng vinden. Het
ongemak moet uit de tekst en de cijfers komen, niet uit de vormgeving.

Daarom is verlies hier ook nooit rood. Een contact dat wegzakt wordt stil en beige, en de
app zegt pas achteraf wat een keuze kostte. Rood bestaat alleen voor een formulier dat
echt fout is ingevuld.

Maak het dus niet donkerder, futuristischer of "dystopischer", en voeg geen gradients of
glaseffecten toe.

## Drie lagen tokens

Je gebruikt bijna altijd de middelste laag.

**Primitieven** zijn de kale kleuren: `--mint-600`, `--paper-100`, `--ink-900`. Die
gebruik je nooit rechtstreeks in een component; ze bestaan alleen om de laag hieronder
mee te bouwen.

**Semantische tokens** zeggen waar iets voor is: `--color-surface`, `--color-text`,
`--color-accent`, `--color-danger`. Dit is wat je in een CSS-bestand schrijft.

**Componenttokens** zet je in het component zelf, als één onderdeel een maat nodig heeft
die verder nergens bestaat. Zo staat `--avatar-size` bovenin `components/avatar.css`.

| Token | Waarvoor |
| --- | --- |
| `--color-background` | de achtergrond van de pagina |
| `--color-surface` | kaarten, invoervelden, meldingen |
| `--color-surface-muted` | een blok dat stiller is dan de rest |
| `--color-text` | alle gewone tekst |
| `--color-text-soft` | uitleg onder een kop |
| `--color-text-faint` | bijschriften, wachttijden, de kleine regel onderaan |
| `--color-border` | randen en scheidslijnen |
| `--color-border-muted` | de rand van iets wat de app liever niet aanbiedt |
| `--color-accent` | de kleur van het systeem: het woordmerk, knoppen, de meter |
| `--color-accent-soft` | zachte vulling in die kleur |
| `--color-on-accent` | tekst op de accentkleur |
| `--color-danger` | alleen voor een fout ingevuld formulier |
| `--color-focus` | de rand om het element waar je toetsenbord staat |

## Licht en donker

Elke semantische token is één regel met twee waarden:

```css
--color-surface: light-dark(var(--paper-0), var(--night-800));
```

De browser kiest de eerste waarde in een licht thema en de tweede in een donker thema. Er
is dus geen tweede kopie van het palet en geen media query per component. De knop in de
voettekst zet `data-theme="light"` of `data-theme="dark"` op de pagina; staat er niets,
dan volgt hij de instelling van het apparaat.

Voeg je een kleur toe, dan hoort daar dus meteen een donkere variant bij. Eén waarde
zonder `light-dark()` is bijna altijd een vergissing.

## Typografie

Twee lettertypes, met een duidelijke taakverdeling.

**Outfit** is de stem van het systeem: het woordmerk, koppen, en elk getal dat moet
landen. **Inter** is alles wat je als zin leest: lopende tekst, knoppen, labels,
meldingen.

Beide staan als woff2 in `public/assets/fonts/` en worden geladen in `base/fonts.css`. Ze
komen dus niet van een CDN: tijdens een testsessie op een slechte verbinding staat de
typografie er gewoon, en er gaat geen verzoek naar buiten. Voeg geen derde lettertype
toe.

Getallen die veranderen of onder elkaar staan krijgen `class="numeric"`. Dat zet
tabelcijfers aan, zodat een cijfer niet verspringt terwijl het optelt.

De schaal staat in tokens: `--text-score` voor het grote cijfer en `--text-2xl` tot
`--text-2xs` voor de rest. Gebruik geen tussenmaten die er niet in staan.

## Ruimte en vorm

Afstanden gaan met `--space-1` tot `--space-9`. Er zit geen enkele andere waarde in het
ontwerp. Hoeken lopen van `--radius-xs` (een balkje) via `--radius-sm` (knoppen) en
`--radius-md` (kaarten) naar `--radius-pill`. Diepte maak je met een haarlijn, of met
`--ring-accent` voor het ene blok dat wél om aandacht vraagt. Slagschaduwen gebruiken we
niet.

## Eén map per plek

De CSS is opgebouwd zoals de code. Elk onderdeel heeft zijn eigen bestand, en dat bestand
heet naar het onderdeel:

```
base/         fonts, tokens, reset, elements, utilities
layouts/      één bestand per layout in views/layouts/
partials/     één bestand per partial in views/partials/
components/   één bestand per component dat op meer dan één scherm voorkomt
pages/        één bestand per scherm in public/, en alleen daar ingeladen
```

Ze worden in die volgorde ingeladen, en het bestand van de pagina komt als laatste. Je
hoeft niets te registreren: zet `partials/score-block.css` in de map en hij wordt
meegestuurd, en `pages/contacten.css` wordt alleen op `contacten.php` geladen.

Twijfel je waar iets hoort? Gebruik je het op één scherm, dan `pages/`. Hoort het bij een
partial, dan `partials/` met dezelfde naam. Kan elk scherm het gebruiken, dan
`components/`.

## Klassen schrijven

We gebruiken BEM-achtige namen: `.blok`, `.blok__onderdeel`, `.blok--variant`. Een
toestand die JavaScript aan- en uitzet heet `.is-iets`.

```html
<article class="contact-card contact-card--urgent">
    <span class="contact-card__name">Sanne de Wit</span>
</article>
```

Er staat maar één klasse in een selector, dus geen `.section .contact-card span`. Zo kan
niemand per ongeluk iets overschrijven wat hij niet bedoelde, en werkt een component
overal hetzelfde.

## Mobiel

De app is één kolom van `--width-app` breed: op een telefoon vult die het scherm, op een
laptop staat hij in het midden. Dat is expres. Het moet lezen als een app en niet als een
website die is uitgerekt.

Test in de browser op 390 pixels breed én op een normale laptopbreedte.

## Toegankelijkheid

Dit is de ondergrens, geen extraatje. Contrast minimaal AA. De focusrand blijft zichtbaar
en wordt nooit weggehaald. Elk invoerveld heeft een label. Knoppen zijn minstens 44 bij
44 pixels, want daaronder mis je ze met je duim. Beweging staat uit voor mensen die dat
in hun systeem hebben aangegeven, en dat is al geregeld in `tokens.css`. Iconen die
betekenis dragen krijgen tekst erbij of een `aria-label`.

Bovenaan elke pagina staat een verborgen link "Direct naar de inhoud", die zichtbaar
wordt zodra je met tab begint. Laat die staan.
