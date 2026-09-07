# Het project en het scenario

Draagvlak is een speculatief ontwerpproject voor CMGT aan de Hogeschool Rotterdam. Het
prototype laat een app uit 2038 zien die sociale steun omzet in een openbaar cijfer
tussen 0 en 100. Het is gebouwd om op mensen getest te worden, niet om uitgebracht te
worden.

Lees dit document voordat je tekst op een scherm aanpast. De toon van dit prototype is
het onderwerp van het onderzoek, dus een zin vriendelijker maken is geen kleine
redactionele keuze maar een inhoudelijke.

## Het scenario

Niemand beoordeelt elkaar in 2038. Dat is juist het punt. Draagvlak berekent je cijfer
uit wat er toch al gebeurt: wie er op je berichten reageert, hoe snel, wie is gestopt
met reageren, en hoeveel mensen jou als noodcontact hebben opgegeven. Je kunt geen
punten geven en je kunt niemand afstraffen. Je kunt alleen zelf iemands cijfer laten
dalen door niet te reageren, en dat weet iedereen. Daardoor is antwoorden geen
beleefdheid meer maar een verplichting, en voelt een dag stilte als iets wat je iemand
aandoet.

Het begon als iets leuks. Draagvlak liet zien wie er echt om je gaf, in een tijd waarin
steeds meer mensen zich alleen voelden, en mensen deden mee omdat het plezier gaf.
Meedoen is nog steeds vrijwillig, maar niet meedoen is duurder geworden: verhuurders
vragen je cijfer, stagebedrijven kijken ernaar, verzekeraars geven korting boven de
zeventig. Uitschrijven betekent op nul beginnen, en dat leest als iets te verbergen
hebben. Sinds 2035 zit er zelfs een melding in de app als je cijfer je slaap verstoort.
Mensen klikken die weg en gaan door.

Wie het al zwaar heeft wordt het hardst geraakt. Ben je ziek, zorg je voor iemand, of
ben je net verhuisd, dan zakt je cijfer precies wanneer je anderen het hardst nodig
hebt. Er is een handel ontstaan in aandacht: je kunt betalen voor reacties, en er zijn
bedrijven die voor een maandbedrag je cijfer bijhouden. Jongeren zeggen ondertussen dat
het meevalt en dat ze het vooral gezellig vinden, terwijl instanties naar dezelfde
cijfers kijken en schrikken. Zo is een systeem dat liet zien wie steun had veranderd in
een systeem dat bepaalt wie steun krijgt.

## De rol van de deelnemer

De deelnemer is Sam Vermeer, 22 jaar, net verhuisd voor een stage. Het cijfer staat op
69, negen punten lager dan voor de verhuizing, en één punt onder de zeventig waarboven
de zorgverzekeraar vijf euro korting geeft. Drie mensen wachten op antwoord.

Die opzet staat in `data/user.json` en `data/messages.json` en is met opzet zo gekozen:
één punt onder een grens is de plek waar het systeem zich het scherpst laat voelen. Pas
die startwaarde niet aan zonder het met het team te overleggen, want alle testsessies
moeten met dezelfde stand beginnen.

## De schermen en wat ze moeten doen

Het startscherm opent met je cijfer, groot, met de daling ernaast. Niet met wie je kent
maar met wat je kwijt bent.

Een gesprek toont het bericht met een klok die loopt en een aftrek die oploopt. De
deelnemer kiest een antwoord of laat het liggen, en beide kosten iets.

Contacten staan gesorteerd op traagheid, met de traagste bovenaan en in het rood. Wie
ziek is of voor een ander zorgt, ziet er hier uit als een slechte investering.

Noodcontacten laten wederkerigheid zien als punten. Naast elk contact staat wat het
oplevert, en er staat een melding dat iemand je van zijn lijst heeft gehaald.

Instellingen bevat een schakelaar om je badge te verbergen die niet werkt en niet
beschikbaar is in jouw regio, en een uitschrijfknop die je op nul zet. De uitweg
bestaat, maar alleen als belofte.

De check-tool is het scherm dat de verhuurder ziet: kandidaat 3 van 12, een cijfer, een
grens, en geen reden. Ziekte en mantelzorg zijn onzichtbaar voor wie beoordeelt.

Het scorebureau verkoopt onderhoud van je cijfer voor 14,99 of 39 euro per maand. Wie
het het hardst nodig heeft, heeft het geld het minst.

## Wat je nooit doet

Geen scherm vriendelijker maken. Geen geruststellende disclaimer toevoegen. De
uitzetknop niet laten werken. Geen uitleg toevoegen die de deelnemer waarschuwt dat dit
een prototype is; dat gebeurt mondeling, voor de sessie begint.

Twijfel je of iets te ver gaat, dan is dat een teamvraag en geen codevraag.
