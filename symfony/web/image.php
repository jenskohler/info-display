<?php
/* testcode for ePaper-Displays - not relevant for the big screen */
header("Content-type: image/png");

$ePaperServer = '192.168.1.219:8001';

$im     = imagecreatefromgif("/Users/thomas/Temp/bild.gif");

imageantialias($im, false);

$black = imagecolorallocate($im, 0, 0, 0);
$white = imagecolorallocate($im, 255, 255, 255);

$fontLight = '/Users/thomas/Temp/FiraSans-Light.ttf';
$fontNormal = '/Users/thomas/Temp/FiraSans-Book.ttf';
$fontMedium = '/Users/thomas/Temp/FiraSans-Medium.ttf';
$fontBold = '/Users/thomas/Temp/FiraSans-Bold.ttf';

//$im = imagerotate($im, -90.0, $white);

imagettftext($im, 30, 0.0, 250, 48, $black, $fontNormal, 'von Neumann');
imagettftextright($im, 30, 0.0, 790, 48, $black, $fontLight, 'A212a');

imageline($im, 0, 55, 800, 55, $black);

imageline($im, 0, 460, 800, 460, $black);
imagettftext($im, 10, 0.0, 0, 477, $black, $fontLight, 'Aktualisiert: ' . date("Y-m-d H:i:s"));


function sessionInfo($im, $text, $slot, $title, $lecturer, $ypos, $scale = 1.0) {

    global $black;
    global $white;
    global $fontLight;
    global $fontNormal;

    $sizeHeader = 20 * $scale;
    $sizeLecture = 30 * $scale;
    $sizeLecturer = 20 * $scale;

    imagettftext($im, $sizeHeader, 0.0, 10, $ypos, $black, $fontNormal, $text);
    imagettftextright($im, $sizeHeader, 0.0, 790, $ypos, $black, $fontNormal, $slot);
    //imageline($im, 10, 122, 790, $ypos + 2, $black);

    $height = height($sizeHeader, $fontNormal);

    imagettftext($im, $sizeLecture, 0.0, 10, $ypos + $height + 30, $black, $fontNormal, $title);

    $height = height($sizeLecture, $fontNormal);

    imagettftext($im, $sizeLecturer, 0.0, 10, $ypos + $height + 52, $black, $fontNormal, $lecturer);
}

function height($size, $font) {
    $bbox = imagettfbbox($size, 0.0, $font, 'XgXgXgX');

    return abs($bbox[3]) + abs($bbox[5]);
}

sessionInfo($im, 'Aktuelle Veranstaltung',
    '9:45–11:15 Uhr',
    'Grundlagen der Informatik (GDI)',
    'Prof. Dr. Schmücker-Schend',
    130);

imagettftext($im, 16, 0.0, 10, 300, $black, $fontNormal, 'Nächste Veranstaltungen');
imagettftext($im, 18, 0.0, 10, 340, $black, $fontNormal, '11:30–13:00 Uhr');
imagettftext($im, 18, 0.0, 200, 340, $black, $fontNormal, 'Techniken der Programmentwicklung (TPE)');

imagettftext($im, 18, 0.0, 10, 380, $black, $fontNormal, '13:40–15:10 Uhr');
imagettftext($im, 18, 0.0, 200, 380, $black, $fontNormal, 'Datenbanken (DBA)');


imagettftext($im, 18, 0.0, 10, 420, $black, $fontNormal, '15:20–16:50 Uhr');
imagettftext($im, 18, 0.0, 200, 420, $black, $fontNormal, 'Datenbanken (DBA)');


//imageline($im, 10, 262, 790, 262, $black);

//imagefilledrectangle($im, 10, 60, 790, 400, $black);

//imagettftext($im, 32, 0.0, 20, 300, $white, $fontMedium, 'Achtung:');


//imagepng($im);
//$tempFile = tempnam(sys_get_temp_dir(), "image");

$im = imagerotate($im, 90.0, $white);

$tempFile = "/Users/thomas/Temp/result.png";


$temp = fopen($tempFile, "w");
imagepng($im, $temp);
//fclose($temp);

$resultFile = '/Users/thomas/Temp/new.png';

//$cmd = 'export DYLD_LIBRARY_PATH=opt/local/lib; /opt/local/bin/convert +dither  -colors 2  -colorspace gray -normalize ' . $tempFile . ' ' . $resultFile;
$cmd = 'export DYLD_LIBRARY_PATH=opt/local/lib; /opt/local/bin/convert -colorspace gray   -colors 2  -normalize ' . $tempFile . ' ' . $resultFile;


$results = shell_exec($cmd);
print_r($results);


sendPicture($ePaperServer, 'D1010873', $resultFile);


//unlink($tempFile);

//extract data from the post

//set POST variables
imagepng($im);
imagedestroy($im);

function imagettftextright($im, $fontSize, $angle, $xpos, $ypos, $color, $font, $text) {
    $box = imagettfbbox($fontSize, 0.0, $font, $text);
    $width = $box[2];

    imagettftext($im, $fontSize, 0.0, $xpos - $width, $ypos, $color, $font, $text);
}

function createJSON($label, $picture) {
    $pictureEncoded = base64_encode($picture);
    return '{"@title":"Send image","ImageTask":[{"@labelId":"' . $label . '","@page":0,' .
    '"@preload":false,"Image":"' . $pictureEncoded . '"}]}';
}

function sendPicture($ePaperServer, $label, $pictureFile) {

    // read the picture from disk
    $picture = file_get_contents($pictureFile);

    // create JSON from the picture
    $json = createJSON($label, $picture);

    // Open cURL connection
    $url = "http://$ePaperServer/service/task";
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json;charset=UTF-8",
        "Content-Length: " .strlen($json) ]);;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    // execute call
    $result = curl_exec($ch);

    curl_close($ch);
}