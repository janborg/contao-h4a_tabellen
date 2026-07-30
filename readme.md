# Spielpläne und Tabellen deines Vereins von handball.net in Contao CMS integrieren

[![GitHub license](https://img.shields.io/github/license/janborg/contao-h4a_tabellen)](https://github.com/janborg/contao-h4a_tabellen)
![Packagist Version](https://img.shields.io/packagist/v/janborg/contao-h4a_tabellen)
![Packagist](https://img.shields.io/packagist/dt/janborg/contao-h4a_tabellen)

## Features
- Backend Module zur Anzeige der Saisons, Teams und Tournaments eines Clubs
- Contentelement zur Anzeige der Spiele eines Teams einer Saison
- Contentelement zur Anzeige der aktuellen Tabelle einer Liga
- Contentelement zur Anzeige der offiziellen [Widgets](https://www.handball.net/widgets) von handball.net
- Import der Spiele einer Mannschaft in einen Contao-Kalender 
- Cron zum Update der Spiele im Contao-Kalender 
- Cron zum Update der Ergebnisse der Spiele im Contao-Kalender
- Cron zum Abruf neuer Saisons eines Clubs
- Cron zum Abruf neuer Teams und Tounaments in einer Saison

(IDs der Ligen und Mannschaften kann der Verein in seinem Vereinsaccount abrufen)


## Installation

Installation erfolgt über den Contao Manager oder direkt per Composer: 

```
composer require janborg/contao-h4a_tabellen
```

## Einrichtung

Zuerst muss der CLub im Backend Module "Handballnet Teams" angeegt werden. Dazu wird die ID des Vereins benötigt, diese findest du unter https://www.handball.net/vereine, z.Bsp:
  
  - handball4all.schleswig-holstein.1518 für den THW Kiel
  - handball4all.suedbaden.592 für die HSG Konstanz


Zeige dann die Kindelemente an und klicke dort auf den Button "Update Clubsaisons". Im Anschluss siehst du die verfügbaren Saisons. 

Zeige dann die Kindelemente einer Saison an und klicke dort auf den Button "Update Teams". Dadurch werden die Teams der Saison abgerufen.

Nun kannst die die Contentelemente auf einer Seite einbauen, indem du dort über die Select Felder den CLub, die Saison und das Team auswählst.

Außerdem kannst du in einem Kalender die Handballnet Updates aktivieren und dort ebenfalls die Teams auswählen, deren Spiele im Kalender angezeigt werden soll.
