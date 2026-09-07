# Onderzoek en wat we ermee doen

Dit document legt vast wat er uit de Research Through Design-sessie kwam en welke
keuzes in de code daaruit volgen. Staat hier dat iets is afgewogen, gebruik dan die
reden in plaats van hem opnieuw af te leiden.

## Opzet

Research Through Design met observatie en een metriekenformulier, uitgevoerd met vijf
deelnemers: Jayden, Melissa, Bart, Furkan en Dirk. Eén onderzoeksleider en één notulist.
Eerst een nulmeting, dan scenario en taak, dan het prototype zonder uitleg vooraf.
Gemeten met de Self-Assessment Manikin op drie schalen van 1 tot 5: unhappy tegenover
happy, calm tegenover excited, en controlled tegenover in control.

De taak was bewust open: handel je berichten af zoals jij dat zou doen, en zeg hardop
wat je denkt. Getest is het moment waarop drie mensen op antwoord wachten terwijl je
zelf op 69 staat, één punt onder de grens waarboven de verzekeraar korting geeft.

## Wat eruit kwam

De harde uitkomst is dat veel deelnemers het abonnement afsloten. Niet als grap, maar
omdat het scherm op het juiste moment kwam: de verkoop begint bij je daling.

Deelnemers voelden zich sterk geneigd en zelfs gedwongen om de app te blijven gebruiken
en op tijd te reageren. Dat kwam terug in de gesprekken en in de dominantieschaal.

De emoties waren heftiger dan verwacht. De hypothese was dat mensen zich minder
gemanipuleerd zouden voelen dan bij het ontwerpdoel gedacht, en dat bleek niet zo. Het
ontwerpdoel is grotendeels gehaald, al hadden we op nog scherpere reacties gehoopt.

E�n observatie had direct gevolg voor de bouw: bij een harde grens van vijftien seconden
stopten deelnemers met het lezen van het bericht. Het aftellen nam de aandacht over en
de inhoud van wat iemand vroeg verdween.

## Wat dat betekent in de code

De harde grens van vijftien seconden is vervangen door een oplopende aftrek. Je verliest
één punt per interval van vijftien seconden, met een plafond van negen punten per
bericht. Daardoor blijft er druk staan zonder dat er één moment is waarop alles
omvalt, en blijft de tekst leesbaar.

Die drie getallen staan als constanten in `src/Score/score-rules.php`:
`DRAIN_INTERVAL_SECONDS`, `DRAIN_POINTS_PER_INTERVAL` en `DRAIN_MAX_PENALTY`. Verander
ze niet zonder overleg, want elke wijziging maakt de sessies onderling
onvergelijkbaar. Het plafond zit er om te voorkomen dat een deelnemer die even wegloopt
de rest van de test op nul staat.

De aftrek wordt op de server berekend op het moment dat het antwoord binnenkomt, niet in
de browser. De klok in JavaScript laat alleen zien wat het wachten tot nu toe kost. Een
deelnemer die het tabblad sluit ontloopt de aftrek dus niet, en dat hoort zo.

Het scorebureau staat er als volwaardig scherm in en niet als grapje, omdat het
abonnement de sterkste uitkomst van de test was.

## Voor de volgende ronde

Nog niet getest is wat er gebeurt als een deelnemer helemaal niets doet. Er is nu een
plafond, maar niemand heeft die situatie in een sessie doorlopen.

Ook onbekend is of de check-tool en het scorebureau los van elkaar werken. In deze ronde
kwamen ze aan het einde van dezelfde run, dus het effect van het een zit in het ander.
