# Spielpläne und Tabellen über die json API von handball4all ins Contao CMS integrieren

## Features
- Contentelemente zur direkten Ausgabe aller Spiele oder der Tabelle einer Mannschaft
- Import und Update (dailyCron) von Spielen einer Mannschaft als Kalendereinträge in einem Contao-Kalender
- Update der Ergebnisse in den Kalendereinträgen über einen hourlyCron

(IDs der Ligen und Mannschaften kann der Verein in seinem Vereinsaccount abrufen)

## Nur Handballvereine folgender Verbände bzw. Oberligen dürfen die Daten verwenden
- Badischer HV
- Hamburger HV
- HV Rheinhessen
- HV Saar
- HV Schleswig-Holstein
- Südbadischer HV
- Thüringer HV
- HV Württemberg
- Oberliga Rheinland-Pfalz/Saar
- Oberliga Mitteldeutschland
- Fédération Luxembourgeoise de Handball
- Vorarlberger HV

Jede weitere Verwendung der Schnittstellen bzw. der abgerufenen Daten ist nicht erlaubt. Dies gilt insbesondere für die Darstellung auf Webseiten die:

- nicht vereinsbezogen sind,
- mehrere Ligen und/oder mehrere Vereine darstellen, sofern keine Spielgemeinschaft besteht und/ oder
- als Portalseiten ein möglichst weitreichendes Abbild unserer Daten verfolgen.


# Funktionen
## Elemente
Es stehen zwei Content-Elemente zur Verfügung, um für ein bestimmte Mannschaft einen Spielplan mit Ergebnissen oder eine aktuelle Tabelle im Frontend auszugeben.

### h4a-Spielplan
Bei diesem Elementtyp stehen folgende Eingabefelder zur Verfügung:
1. Überschrift
2. Team ID: eine 6-stellige ID der Mannschaft, die der Übersicht im Vereinsaccount von handball4all entnommen werden kann
3. Mein Team: Hier muss manuell die genaue Bezeichnung der Mannschaft eingegeben werden. Im Frontend kann dann über die CSS Klasse "myteam" eine Hervorhebung der Mannschaft erfolgen

### h4a-Tabelle
Bei diesem Elementtyp stehen folgende Eingabefelder zur Verfügung:
1. Überschrift
2. Liga ID: eine 5-stellige ID der anzuzeigenden Liga, die der Übersicht im Vereinsaccount von handball4all entnommen werden kann
3. Mein Team: Hier muss manuell die genaue Bezeichnung der Mannschaft eingegeben werden. Im Frontend kann dann über die CSS Klasse "myteam" eine Hervorhebung der Mannschaft erfolgen.

### h4a-aktuelle Spiele
Bei diesem Elementtyp stehen folgende Eingabefelder zur Verfügung:
1. Überschrift
2. Verein ID: eine ID des anzuzeigenden Vereins, die der Übersicht im Vereinsaccount von handball4all entnommen werden kann
3. Mein Team: Hier muss manuell die genaue Bezeichnung der Mannschaft eingegeben werden. Im Frontend kann dann über die CSS Klasse "myteam" eine Hervorhebung der Mannschaft erfolgen.


## Caching
Da der Abruf der .json-Datei von h4a insbesondere für den Spielplan einige Zeit benötigt und sich das sehr negativ auf die Ladezeit der Seite auswirkt, wird die .json-Datei im Cache zwischengespeichert. Unter den Einstellungen/handball4all-Einstellungen sollte daher eine Zeit in Sekunden angegeben werden, wie lange diese Datei aus dem Cache aufgerufen werden soll, bevor ein erneuter Abruf von von handball4all erfolgen soll.  

Für das Element h4a-aktuelle Spiele ist das Caching automatisch auf 300s gesetzt, da hier eine Aktualität sinnvoll ist.

## Kalender
