Betriebssystem: Ubuntu 17.04 (Server-Variante)

PHP-Version: 7.0

Java-Version: 1.8u152

Es empfiehlt sich einen Desktop für das o.g. Betriebssystem zu installieren.
Dies erleichtert das Einrichten des Lancomm Wireless AccessPoints, der epaper Displays und letztlich wird das Debuggen der PHP-Anwendung (und die Kontrolle der generierten Bilder für die epaper Displays) erleichtert.
Als Desktopumgebung wird im Folgenden XFCE verwendet, da dieser leichtgewichtige Desktop eine einfache Möglichkeit für einen Remote-Desktop-Zugriff bietet:

sudo apt-get install xfce4

Als Benutzer (mit sudo-Rechten) wird der Benutername 'epaper' verwendet.

Eine aktuelle Java-Version herunterladen und in das Verzeichnis /opt entpacken.
Ensprechende Zugriffsrechte für das /opt Verzeichnis lassen sich folgendermaßen setzen:

sudo chown -R epaper:epaper /opt/

Danach die JAVA-HOME und JRE-HOME Variablen setzen:

sudo vi /etc/environment

JAVA_HOME="/opt/jdk1.8.0_131/"
JRE_HOME="/opt/jdk1.8.0_131/jre"

PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/jdk1.8.0_131/jre/bin"


Für das Einrichten des AccessPoints wird das Lancomm-Tool LANConfig empfohlen.
Leider ist nur eine Windows-Version verfügbar. Die Emulationssoftware WINE
ist aber ebenfalls (trotz einiger Warnhinweise) in der Lage, das Tool auszuführen.

sudo apt-get install wine


Nun folgt die Installation der Programmierumgebung:


sudo apt-get install php

Für PHP werden folgende Erweiterungen benötigt:

sudo apt-get install php-gd

sudo apt-get install php-curl

sudo apt-get install php7.0-xml

sudo apt-get install php7.0-mysql

sudo phpenmod pdo_mysql

sudo apt-get install php7.0-mbstring

sudo apt-get install pngquant


In der php.ini sollten folgende Werte gesetzt werden:

memory_limit = 1028M

date.timezone = "Europe/Paris"

Es gilt zu beachten, dass in der verwendeten PHP-Version zwei 
php.ini Versionen existieren. Eine für den 
Apache2 Webserver und eine für das PHP-Commandline-Interface (CLI).

Als best practice kann empfohlen werden, zuerst die Apache-Version zu ändern 
und den Apache2 Server neuzustarten. Wenn dieser ohne Fehlermeldungen startet, 
kann die php.ini einfach vom Apache2-Verzeichnis in das CLI-Verzeichnis kopiert
werden. Z.B. von 

sudo cp /etc/php/7.0/apache2/php.ini ../cli/



Zum Abschluss sollte der Apache2 Server neu gestartet werden:

sudo service apache2 restart

Um den aktuellen Entwicklungsstand des PHP-Rendering-Projekts aus dem GitHub-Repository herunterladen zu können, wird die Installation von Git empfohlen:

sudo apt-get install git

Das PHP-Rendering-Projekt ist unter folgender Adresse zu finden:
https://github.com/informatik-mannheim/info-display

Um das PHP-Rendering-Projekt ausführen zu können, sollten die entsprechenden Installationsschritte im zugehörigen README.md des Projekts ausgeführt werden.

Die folgenden Schritte sollten im Unterverzeichnis 'symfony' ausgeführt werden:

Um möglichst einfach die Benutzerrechte für folgende Verzeichnise richtig zu setzen, werden die existierenden Verzeichnise einfach gelöscht. Diese werden später automatisch (mit den richtigen Rechten) neu angelegt:

sudo rm -rf cache/ logs/


php composer.phar self-update

php composer.phar update

Dies schließt die Installation des PHP-Rendering-Projekts ab.

Die Rendering-Skripte für die epaper Displays finden sich im Verzeichnis:
~/info-display/symfony/src/HSMA/InfoDisplay/Graphics

Da das PHP-Rendering-Projekt unter dem Benutzerverzeichnis 'epaper' abgelegt ist, müssen folgende Verzeichnise angelegt werden:

mkdir -p /home/epaper/temp/epaper

Ferner muss eine Bild-Datei (background.png) in das Verzeichnis
/home/epaper/temp
kopiert werden. Diese Bilddatei (zwingend *.png, mit einer Auflösung von 800x480px) wird
als Hintergrund für das Rendering der Inhalte auf die epaper Displays verwendet.


Das Rendering der Inhalte (*.png Dateien) lässt sich z.B. folgendermaßen starten:
php epaper-display.php -v -v


Um nun die generierten Bilder an die epaper Displays senden zu können, wird der Lancom Wireless epaper Server auf der gleichen Maschine installiert.
Dazu werden die notwendigen Dateien in das /opt Verzeichnis kopiert.

Der Server kann nun folgendermaßen gestartet werden:
java -jar server.jar

Um nun einen AccessPoint beim Server zu registrieren, wird das Tool Lancomm LANconfig empfohlen. Das Tool muss unter Linux mit Hilfe des Emulators WINE installiert und ausgeführt werden.

wine LANconfig-10.12.0039-Rel.exe

Eventuell auftretende Warnungen und Fehlermeldungen können ignoriert werden. Unter Ubuntu 17.04 konnte das Tool korrekt installiert und ausgeführt werden.

Achtung: Nachdem ein AccessPoint dem Server hinzugefügt wurde, kann es bis zu 60 Min. dauern, bis der AccessPoint richtig im Server angezeigt wird. Diverse Fehlermeldungen (Firmware Image konnte nicht gelesen werden, etc.) können dabei ignoriert werden. Nach ca. 60 Min. sollte der AccessPoint aber richtig im Server angezeigt werden.

Nun lassen sich die epaper Displays registrieren und mit Inhalten befüllen. Nach der Registrierung der Displays am AccessPoing kann es bis zu 2 Stunden (!) dauern, bis diese
korrekt angezeigt werden. Gründe dafür sind das proprietäre Übertragungsprotokoll, das Aushandeln der Verschlüsselung, etc.

Für das automatische und zeitgesteuerte Befüllen der epaper Displays wird ein 
cronjob nach folgendem Beispiel empfohlen:

*/5 * * * * php epaper-display.php -v -v > /dev/null
