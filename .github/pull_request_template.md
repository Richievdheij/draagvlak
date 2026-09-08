# Wat verandert er

Kort: wat doet deze branch, en waarom.

## Zo bekijk je het

Welk scherm moet de ander openen om het te zien?

```
http://draagvlak.test/
```

## Nagelopen

- [ ] `composer check` is schoon, en `npm run check` als je CSS, JavaScript of Markdown
      hebt aangeraakt
- [ ] Scherm geopend in de browser, ook in apparaatweergave op 390 pixels breed
- [ ] Alles van één onderwerp staat in één feature: de vier bestanden van een scherm
      heten naar elkaar, en wat twee features gebruiken staat in `components/`
- [ ] Code, bestandsnamen en URL's zijn Engels; schermtekst is Nederlands en staat in
      `views/`
- [ ] Elke melding die je met `flash()` in de wachtrij zet heeft een regel in
      `views/components/notices.php`
- [ ] Alles wat geprint wordt gaat door `$this->e()`
- [ ] Elke query staat in een repository, met vraagtekens en het id van de ingelogde
      gebruiker erbij
- [ ] Geen losse hexcode of pixelwaarde buiten `tokens.css`, en nog steeds één focusrand
- [ ] Niets op het scherm dat alleen voor het team bestaat
- [ ] Deze PR gaat naar `develop`
