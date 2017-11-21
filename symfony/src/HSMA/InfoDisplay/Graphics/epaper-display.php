#!/usr/bin/php
<?php

namespace InfoDisplay\Graphics;

// No autoloader here, therefore we have to load the files manually.


require_once('Sign.php');
require_once(__DIR__ . '/../Persistency/RESTClient.php');
#require_once('HSMA/InfoDisplay/Persistency/RESTClientDisplays.php');
require_once(__DIR__ . '/../Entity/Domain/Booking.php');
require_once(__DIR__ . '/../Entity/Domain/Utility.php');
require_once(__DIR__ . '/../../../../vendor/nategood/httpful/bootstrap.php');
// Load config
require_once('config.php');

use HSMA\InfoDisplay\Entity\Domain\Utility;
use HSMA\InfoDisplay\Graphics\Sign;
use HSMA\InfoDisplay\Persistency\RESTClient;


// analyze command line arguments

$verbose = ($argv[1] == "-v" || $argv[2] == "-v");

// date from the command line or current date
$now = $argv[1] == '-D' ? \DateTime::createFromFormat(\DateTime::ATOM, $argv[2]) : new \DateTime();

// set the timezone

\date_default_timezone_set($config['timezone']);
// get information on the last run of this script
$data = @file_get_contents($config['imagePath'] . '/lastrun.txt');
if ($data == false) {
    // first run, no lastrun date available, use some very old date
    $lastRun = \DateTime::createFromFormat(\DateTime::ATOM, '2000-01-01T00:00:01+00:00');
} else {
    $lastRun = \DateTime::createFromFormat(\DateTime::ATOM, $data);
}
writeLn();
writeLn('Updating signs: Last run was ' . $lastRun->format('d.m.Y H:i'));
writeLn("---------------------------------------------");
// create REST client
$clientBookings = new RESTClient($config['bookingURL']);
$clientEPaper = new RESTClient($config['ePaperServer']);
// Loop over all configured rooms and update displays
foreach ($config['rooms'] as $id => $room) {
    $name = $room['name'];
    $type = $room['type'];
    $display = $room['display'];
    $enabled = $room['enabled'];
    if (!$enabled) {
        // room is disabled
        continue;
    }
    writeLn("Room: $id - $name");
    if ($type == 'lab') {
        // Lab
        $schedule = $config['updates']['lab'];
    } else {
        // Meeting room
        $schedule = $config['updates']['meeting'];
    }

    // Scheduled updated times
    $scheduleLastRun = Utility::findTimeAfter($schedule, $lastRun);
    $scheduleNow = Utility::findTimeAfter($schedule, $now);
    if ($scheduleLastRun == $scheduleNow) {
        // Sign is up-to-date
        writeLn("    Sign is up-to-date");
        continue;
    }

    // Retrieve bookings
    writeLn("    Retrieving data from REST service");
    $bookings = $clientBookings->readRoomBookings($id, $now);
    // Generate image
    writeLn("    Generating image");
    $s = new Sign($config, $room, $id, $bookings, $now);
    $fileName = $config['imagePath'] . '/' . $id . '.png';
    $s->toFile($fileName);
    // compress image

    //$fileCompressedPath = $config['imagePath'] . '/' . $id . '_sw.png';
    $s->compress_png($fileName);

//$cmd = 'export DYLD_LIBRARY_PATH=opt/local/lib; /opt/local/bin/convert +dither  -colors 2  -colorspace gray -normalize ' . $tempFile . ' ' . $resultFile;
    //$cmd = 'export DYLD_LIBRARY_PATH=opt/local/lib; /opt/local/bin/convert -colorspace gray  -colors 2  -normalize ' . $fileName . ' ' . $fileCompressed;
    //$results = shell_exec($cmd);
    // Send image to ePaper display
    writeLn("    Sending image to display $display");
    if ($display != null && strlen($display) > 5) {
        $clientEPaper->sendPicture($display, $fileName, $config['ePaperServer']);
    }
}
writeLn();
// store information on the last run
writeLn("Storing last run information");
$f = fopen($config['imagePath'] . '/lastrun.txt', 'w');
fwrite($f, $now->format(\DateTime::ATOM));
fclose($f);
writeLn("Done");
writeLn();
/**
 * Write string on console but obey the global verbose flag. If flag set
 * to false, no data is printed.
 *
 * @param string $string data to print.
 */
function write($string)
{
    global $verbose;
    if ($verbose) {
        print($string);
    }
}

/**
 * Write string on console but obey the global verbose flag. If flag set
 * to false, no data is printed. Terminate output with a newline character.
 *
 * @param string $string data to print.
 */
function writeLn($string = '')
{
    write($string);
    write("\n");
}


