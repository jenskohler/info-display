# Info Display

## Purpose/Zweck

**English**: Software for the info display at the faculty for computer sciences. This repo is of limited use for people outside of our university.

**Deutsch**: Software für das Info-Display an der Fakultät für Informatik.

## Erweiterungen

Jede/r kann gerne mit innovativen Ideen zu der Weiterentwicklung der Anzeige beitragen. Hierzu gibt es zwei Möglichkeiten:

  1. Einen Verbesserungsvorschlag als [Issue](https://github.com/informatik-mannheim/info-display/issues) in diesem Repository anlegen und hoffen, dass sich jemand findet, der diesen umsetzt.
  2. Selbst in die Tasten greifen und eine Erweiterung programmieren und per Pull-Request an dieses Repository schicken.

Möglichkeit 2 hat natürlich die besseren Chancen, dass die Idee schnell für alle zur Verfügung steht.

## Benutzung

Im Entwicklungsmodus ist die Anwendung unter der URL: `app_dev.php/...` erreichbar. Die Pfade nach app_dev.php ergeben sich aus der Konfiguration in der Datei `\symfony\src\HSMA\InfoDisplay\Resources\config\routing.yml` 

Als erstes muss das Framework installiert werden.
Im Verzeichnis `symfony` ist auszuführen (eventuell mit `sudo`, damit die Rechte stimmen):

    php composer.phar self-update
    php composer.phar update

Danach müssen die Rechte auf den cache-Verzeichnissen für den Webserver (`nobody` oder `www-data`) erteilt werden.

    sudo chown -R nobody symfony/app/cache/
    sudo chown -R nobody symfony/app/logs/

Die Login-Seite ist unter `app_dev.php/` erreichbar. Ein Test-User ist `t.smits` mit dem Passwort `test`.


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
