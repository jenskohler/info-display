<?php
/* testcode for ePaper-Displays - not relevant for the big screen */
require_once('../src/HSMA/InfoDisplay/Graphics/Sign.php');
require_once('../src/HSMA/InfoDisplay/Persistency/RESTClient.php');
require_once('../src/HSMA/InfoDisplay/Entity/Domain/Booking.php');
require_once('../src/HSMA/InfoDisplay/Entity/Domain/Utility.php');
require_once('../vendor/nategood/httpful/bootstrap.php');

use HSMA\InfoDisplay\Graphics\Sign;
use HSMA\InfoDisplay\Entity\Domain\Booking;
use HSMA\InfoDisplay\Entity\Domain\Utility;
use HSMA\InfoDisplay\Persistency\RESTClient;

$bookings = [
    null,
    new Booking("A107", "GDI", "Gründlägen der Informatik", "SMS", "Prof. Dr. Schmücker-Schend", "1UIB", "I", 2, 1, new DateTime(), new DateTime(), new DateTime(), "", true),
    new Booking("A107", "TPE", "Techniken der Programmentwicklung", "SMI", "Prof. Smits", "3IB", "I", 3, 1, new DateTime(), new DateTime(), new DateTime(), ""),
    null,
    new Booking("A107", "THIN", "Theöretische Gründlagen der Informatik", "SMS", "Prof. Schmücker-Schend", "3UIB", "I", 5, 1, new DateTime(), new DateTime(), new DateTime(), ""),
    new Booking("A107", "SMI", "Standards in der Medizin", "SMP", "Prof. Schmücker", "4IMB", "I", 6, 1, new DateTime(), new DateTime(), new DateTime(), ""),
];

$client = new RESTClient('https://services.informatik.hs-mannheim.de');

$room = 'A008';
$bookings = $client->readRoomBookings($room);

$now = new DateTime('8:00');
$s = new Sign($room, $bookings, $now, '/Users/thomas/Temp/bild.gif');
$s->toImage('/Users/thomas/Temp/result2.png');