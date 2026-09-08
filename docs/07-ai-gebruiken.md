# AI gebruiken in dit project

Iedereen in het team gebruikt iets anders, en dat hoeft niet gelijkgetrokken te worden.
Wat wel moet: het model moet de afspraken van deze repo kennen, anders krijg je
antwoorden die hier niet passen.

## Wat welk model leest

`AGENTS.md` is de bron. De andere bestanden verwijzen daarnaar en voegen alleen
tool-specifieke dingen toe. Dit is wat elk model echt uit de repo oppikt:

| Tool                                    | Leest                                                           |
| --------------------------------------- | --------------------------------------------------------------- |
| Claude Code                             | `CLAUDE.md` en `AGENTS.md`, plus de skills in `.claude/skills/` |
| GitHub Copilot                          | `.github/copilot-instructions.md`                               |
| Gemini CLI                              | `GEMINI.md`                                                     |
| Cursor, Codex en andere agents          | `AGENTS.md`                                                     |
| ChatGPT, Gemini of Claude in de browser | niets uit de repo                                               |

Werk je met een van de eerste vier, dan hoef je niets te doen. Klopt een antwoord
duidelijk niet met onze afspraken, dan is de kans groot dat je in een tool zit die
`AGENTS.md` niet leest.

## Skills bij Claude Code

In `.claude/skills/` staat de werkwijze uitgeschreven als vijf skills. Ze zeggen hetzelfde
als deze `docs/`, maar korter en gericht op het doen:

| Skill                | Waarvoor                                                |
| -------------------- | ------------------------------------------------------- |
| `draagvlak-style`    | Vóór de eerste regel: comments, annotaties, opmaak      |
| `draagvlak-screen`   | Een scherm, een formulier, een stukje HTML, een melding |
| `draagvlak-design`   | CSS, tokens, ruimte, kleur, focus, toegankelijkheid     |
| `draagvlak-database` | Een query, een repository, een kolom, het schema        |
| `draagvlak-review`   | Voordat je iets teruggeeft of een pull request opent    |

Je kunt ze aanroepen met `/draagvlak-screen` en zo verder. Verandert er iets aan de
werkwijze, pas dan `AGENTS.md` én de skill aan; een regel die twee keer ergens staat gaat
uit elkaar lopen.

## In de browser

Werk je met ChatGPT, Gemini of Claude op de site, dan weet het model niets van dit
project. Pak dan het blok uit `docs/ai/chatgpt-en-gemini-paste.md` en zet dat eenmalig
bij je Project instructions, je Gem of je Claude-project. Daarna hoef je alleen je vraag
nog te stellen.

Plak er altijd het echte bestand bij dat je wilt laten aanpassen. Zonder dat bestand
verzint elk model paden en klassenamen die hier niet bestaan.

## Waar je op let

Een model dat de instructies niet heeft gehad, stelt bijna altijd Laravel, Eloquent of
Tailwind voor, of bouwt er een router en een controllerlaag bij. Dat is niet fout in het
algemeen, maar wel hier. Krijg je zo'n antwoord, dan zijn de instructies niet
meegestuurd.

Zoek elke voorgestelde klasse of methode in de repo op voordat je hem overneemt. Bestaat
hij niet, dan is hij verzonnen. Dat gebeurt het vaakst bij namen die logisch klinken,
zoals `getScore()` of `ContactService`.

Let op waar het antwoord dingen neerzet. Deze vier fouten glippen er het vaakst doorheen:

- iets van één onderwerp dat buiten zijn eigen feature belandt;
- logica in een template of een query in een pagina-klasse;
- een Nederlandse zin in `src/`;
- een docblock van vijf regels die alleen herhaalt wat de code al zegt.

Controleer ook of er niets op het scherm belandt dat alleen voor het team bestaat: een
testaccount, een demo-melding, mensen die er zomaar in staan.

Draai `composer check` en open het scherm in de browser voordat je commit. Een antwoord
dat er goed uitziet is niet hetzelfde als code die draait.

## Wat je niet aan een model overlaat

Alles wat uit het gebruikersonderzoek komt en in
[06-onderzoek-en-inzichten.md](06-onderzoek-en-inzichten.md) staat. Die keuzes zijn geen
afweging over goede code, dus laat ze met rust, ook als een model voorstelt ze te
"verbeteren". Hetzelfde geldt voor de toon van de schermteksten: het ongemak is het
product.
