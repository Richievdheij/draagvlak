# AI gebruiken in dit project

Iedereen in het team gebruikt iets anders, en dat hoeft niet gelijkgetrokken te worden.
Wat wel moet: het model moet de afspraken van deze repo kennen, anders krijg je
antwoorden die hier niet passen.

## In de editor

Werk je met Claude Code, Copilot, Cursor of Gemini CLI, dan hoef je niets te doen. Die
lezen `AGENTS.md`, `CLAUDE.md`, `GEMINI.md` of `.github/copilot-instructions.md` vanzelf
uit de repo. `AGENTS.md` is de bron; de andere drie verwijzen daarnaar en voegen alleen
tool-specifieke dingen toe.

## In de browser

Werk je met ChatGPT, Gemini of Claude op de site, dan weet het model niets van dit
project. Pak dan het blok uit `docs/ai/chatgpt-en-gemini-paste.md` en zet dat eenmalig
bij je Project instructions, je Gem of je Claude-project. Daarna hoef je alleen je vraag
nog te stellen.

Plak er altijd het echte bestand bij dat je wilt laten aanpassen. Zonder dat bestand
verzint elk model paden en functienamen die hier niet bestaan.

## Waar je op let

Een model dat de instructies niet heeft gehad, stelt bijna altijd MVC, Laravel of
Tailwind voor. Dat is niet fout in het algemeen, maar wel hier. Krijg je zo'n antwoord,
dan zijn de instructies niet meegestuurd.

Zoek elke voorgestelde functienaam in de repo op voordat je hem overneemt. Bestaat hij
niet, dan is hij verzonnen. Dat gebeurt vaker bij helper-achtige namen die logisch
klinken, zoals `getScore()` of `renderView()`.

Let ook op imports. Een model dat dit project niet kent, zet bovenin je pagina een rij
`use function`-regels of een namespace. Die horen hier niet: `bootstrap.php` laadt alles
uit `src/` zelf in.

Controleer of er geen Nederlandse tekst in `src/` belandt en geen Engelse tekst op het
scherm. Dat is de fout die het vaakst doorheen glipt.

Draai `composer lint` en open het scherm in de browser voordat je commit. Een antwoord
dat er goed uitziet is niet hetzelfde als code die draait.

## Wat je niet aan een model overlaat

Alles wat uit het gebruikersonderzoek komt en in
[06-onderzoek-en-inzichten.md](06-onderzoek-en-inzichten.md) staat. Die keuzes zijn geen
afweging over goede code, dus laat ze met rust, ook als een model voorstelt ze te
"verbeteren". Hetzelfde geldt voor de toon van de schermteksten: het ongemak is het
product.
