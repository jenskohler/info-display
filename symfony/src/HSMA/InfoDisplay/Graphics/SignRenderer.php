<?php
namespace HSMA\InfoDisplay\Graphics;

/**
 * Class SignRenderer
 * @package HSMA\InfoDisplay\Graphics
 *
 * Class to render room information into a picture.
 */
class SignRenderer {

    /**
     * @var string URL to access the ePaper Server.
     */
    public $ePaperServer;

    /**
     * @var string path where the fonts are stored
     */
    public $fontsPath;


    public function renderSign($roomNumber, $roomName) {

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
}