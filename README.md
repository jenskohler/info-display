# Info Display

## Purpose/Zweck

**English**: Software for the info display at the faculty for computer sciences. This repo is of limited use for people outside of our university.

**Deutsch**: Software für das Info-Display an der Fakultät für Informatik.


## Erweiterungen

Jede/r kann gerne mit innovativen Ideen zu der Weiterentwicklung der Anzeige beitragen. Hierzu gibt es zwei Möglichkeiten:

  1. Einen Verbesserungsvorschlag als [Issue](https://github.com/informatik-mannheim/info-display/issues) in diesem Repository anlegen und hoffen, dass sich jemand findet, der diesen umsetzt.
  2. Selbst in die Tasten greifen und eine Erweiterung programmieren und per Pull-Request an dieses Repository schicken.

Möglichkeit 2 hat natürlich die besseren Chancen, dass die Idee schnell für alle zur Verfügung steht.


## Installation

Im Entwicklungsmodus ist die Anwendung unter der URL: `app_dev.php/...` erreichbar. Die Pfade nach app_dev.php ergeben sich aus der Konfiguration in der Datei `\symfony\src\HSMA\InfoDisplay\Resources\config\routing.yml` 

Als erstes muss das Framework installiert werden.
Im Verzeichnis `symfony` ist auszuführen (eventuell mit `sudo`, damit die Rechte stimmen):

Bevor die beiden Kommandos eingegeben werden können, muss sichergestellt werden, dass eine php.ini
vorhanden ist.

    php composer.phar self-update
    php composer.phar update
    
Sollten bei der Installation Fehler auftreten, müssen die genannten Module aus der Fehlermeldung
in der php.ini einkommiert werden. Des Weiteren muss die Variable "memory_limit" in 
der php.ini hochgesetzt werden, da 128MB nicht ausreichen. 

Danach müssen die Rechte auf den cache-Verzeichnissen für den Webserver (`nobody` oder `www-data`) erteilt werden.

    sudo chown -R nobody symfony/app/cache/
    sudo chown -R nobody symfony/app/logs/

Die Login-Seite ist unter `app_dev.php/` erreichbar. Ein Test-User ist `t.smits` mit dem Passwort `test`.

Falls PHP-Storm mit eingeschaltenen Plugin "symfony" zur Entwicklung genutzt wird, ist zu beachten,
dass das Aufrufen der app_dev.php aus dem Enticklungsstudio nicht funktioniert.
Hierzu muss unter "Run-Configuration" ein PHP-WebServer eingerichtet werden.

Das Datenbank-Password ist nicht gesetzt. Deswegen muss vor der Benutzung die Datei `parameters.yml` unter [symfony/src/HSMA/InfoDisplay/Resources/config](symfony/src/HSMA/InfoDisplay/Resources/config) aus der Datei [parameters.yml.template](symfony/src/HSMA/InfoDisplay/Resources/config/parameters.yml.template) erzeugt werden.

Die Datenbank Struktur und Testdaten finden sich im Verzeichnis [db](db/).


## Deployment für Live-Betrieb (manuell)

Im Verzeichnis `symfony` 

Voraussetzungen testen:

    php app/check.php

Cache leeren

    php app/console cache:clear --env=prod --no-debug

Assets erzeugen und installieren

    php app/console assetic:dump --env=prod --no-debug

Wichtig ist, dass der Server auf die Verzeichnisse unter `app/cache` schreiben kann. Wenn das nicht geht, müssen die Recht entsprechend gesetzt werden.

Nach dem Löschen der Ressourcen müssen die Verzeichnisrechte häufig neu gesetzt werden. Insbesondere, wenn das Löschen als root ausgeführt wurde.


## Deployment für Live-Betrieb (per Skript)

Alternativ gibt es unter `symfony` auch ein Shell-Skript `deploy.sh`, das die Rechte setzt und die Caches leert.


## Grober Aufbau der Software

Die Software besteht aus zwei Komponenten:

  1. Der Anzeige auf dem Bildschirm und deren [Controllern](symfony/src/HSMA/InfoDisplay/Controller/View) und
  2. dem Backend, um Daten auf den Bildschirm zu bringen und dessen [Controllern](symfony/src/HSMA/InfoDisplay/Controller/Admin).
