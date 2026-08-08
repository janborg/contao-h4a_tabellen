# Spielpläne und Tabellen deines Vereins von handball.net in Contao CMS integrieren

[![GitHub license](https://img.shields.io/github/license/janborg/contao-h4a_tabellen)](https://github.com/janborg/contao-h4a_tabellen)
![Packagist Version](https://img.shields.io/packagist/v/janborg/contao-h4a_tabellen)
![Packagist](https://img.shields.io/packagist/dt/janborg/contao-h4a_tabellen)

Dieses Bundle integriert Spielpläne, Ergebnisse und Tabellen deines Handballvereins von
[handball.net](https://www.handball.net) (handball4all) direkt in Contao. Daten werden über
die handball.net-API abgerufen, im Backend verwaltet und per Content-Elementen oder im
Contao-Kalender im Frontend ausgegeben.

## Features

- **Backend-Verwaltung** für Clubs, Saisons und Teams eines Vereins
- **Content-Element „Spielplan“** zur Anzeige der Spiele eines Teams einer Saison
- **Content-Element „Tabelle“** zur Anzeige der aktuellen Tabelle einer Liga
- **Content-Element „Widget“** zur Einbindung der offiziellen [handball.net-Widgets](https://www.handball.net/widgets)
- **Kalender-Integration**: Import der Spiele einer Mannschaft in einen Contao-Kalender
- **Cron-Jobs** für die automatische Aktualisierung von Spielen, Ergebnissen, Saisons und Teams
- **Console-Commands** zur manuellen Aktualisierung und zum Debugging

> Tipp: Zusätzliche Spielstatistiken liefert das ergänzende Bundle
> [janborg/contao-h4a_gamestats](https://github.com/janborg/contao-h4a_gamestats).

## Voraussetzungen

- PHP `^8.3`
- Contao `^5.3` (core-bundle & calendar-bundle)

## Installation

Über den Contao Manager oder direkt per Composer:

```
composer require janborg/contao-h4a_tabellen
```

Führe anschließend die Datenbank-Migration im Contao Manager bzw. per
`vendor/bin/contao-console contao:migrate` aus.

## Einrichtung

1. **Club anlegen:** Lege den Verein im Backend-Modul **„Handballnet Teams“** an. Dazu wird
   die ID des Vereins benötigt. Diese findest du unter <https://www.handball.net/vereine>,
   z. B.:
   - `handball4all.schleswig-holstein.1518` für den THW Kiel
   - `handball4all.suedbaden.592` für die HSG Konstanz

2. **Saisons abrufen:** Zeige die Kindelemente des Clubs an und klicke auf den Button
   **„Update Clubsaisons“**. Danach werden die verfügbaren Saisons angezeigt.

3. **Teams abrufen:** Zeige die Kindelemente einer Saison an und klicke auf den Button
   **„Update Teams“**. Dadurch werden die Teams (und Tournaments) der Saison abgerufen.

4. **Content-Elemente einbauen:** Füge auf einer Seite die Content-Elemente ein und wähle
   dort über die Auswahlfelder Club, Saison und Team aus.

5. **Kalender-Integration (optional):** Aktiviere in einem Contao-Kalender die
   Handballnet-Updates und wähle die Teams aus, deren Spiele im Kalender angezeigt werden
   sollen.

## Content-Elemente

| Element | Beschreibung |
| --- | --- |
| Handballnet Spielplan | Spiele eines Teams einer Saison |
| Handballnet Tabelle | Aktuelle Tabelle einer Liga |
| Handballnet Widget | Offizielles handball.net-Widget |

## Cron-Jobs

Die folgenden Cron-Jobs werden automatisch über den Contao-Cron ausgeführt:

| Aufgabe | Intervall |
| --- | --- |
| Update der Spiele im Contao-Kalender | stündlich |
| Update der Ergebnisse der Spiele im Contao-Kalender | stündlich |
| Abruf neuer Saisons eines Clubs | täglich |
| Abruf neuer Teams und Tournaments einer Saison | täglich |

## Console-Commands

| Command | Beschreibung |
| --- | --- |
| `handballnet:update:events` | Spiele der Kalender aktualisieren |
| `handballnet:update:results` | Ergebnisse der Spiele aktualisieren |
| `handballnet:show:schedule` | Spielplan eines Teams anzeigen |
| `handballnet:show:teams` | Teams eines Clubs anzeigen |
| `handballnet:show:gamesdata` | Rohdaten der Spiele anzeigen (Debugging) |

## Lizenz

Veröffentlicht unter der [MIT-Lizenz](LICENCE.md).

## Support

- Issues: <https://github.com/janborg/contao-h4a_tabellen/issues>
- Quellcode: <https://github.com/janborg/contao-h4a_tabellen>
