<?php
namespace HSMA\InfoDisplay\Graphics;

use HSMA\InfoDisplay\Entity\Domain\Booking;
use HSMA\InfoDisplay\Entity\Domain\Utility;


/**
 * Class Sign
 * @package HSMA\InfoDisplay\Graphics
 *
 * Class representing a sign. It encapsulate the drawing calls.
 */
class Sign {

    /**
     * Left margin for the header.
     */
    const LEFT_MARGIN_HEADER = 32;

    /**
     * Left margin for text data.
     */
    const LEFT_MARGIN_TEXT = 100;

    /**
     * @var resource the image handle
     */
    private $im;

    /**
     * @var int the color black
     */
    private $black;

    /**
     * @var int the color white
     */
    private $white;

    /**
     * @var string normal font
     */
    private $fontNormal;

    /**
     * @var string bold font
     */
    private $fontBold;

    /**
     * @var int pixel font 10x20
     */
    private $gdFont10x20normal;

    /**
     * @var int pixel font 11x19
     */
    private $gdFont11x19normal;

    /**
     * @var int pixel font 6x12
     */
    private $gdFont6x12normal;

    /**
     * @var string Name of the room
     */
    private $roomName;

    /**
     * Render the name of the room on the sign.
     *
     * @param $building string the Building
     * @param $room string room number
     * @param $name string name of the room
     */
    private function roomInfo($building, $room, $name) {
        $this->textTTFLeftAligned(52, 32,  80, $this->fontNormal, $building);
        $this->textTTFLeftAligned(26, 74,  80, $this->fontNormal, $room);
        $this->textTTFLeftAligned(26, 32, 116, $this->fontNormal, $name);
        $this->line(0, 132, 800, 132);
    }

    /**
     * Create a new sign.
     *
     * @param $baseImage string path to base image, i.e. the image used as background. The provided image
     *          must be in the PNG format and have the dimension 800x480 pixel.
     */
    public function __construct($config, $room, $id, $bookings, $now)
    {

        $this->config = $config;

        $this->im = imagecreatefrompng($this->config['template']);



        // initialize graphics library
        //if ($baseImage != null) {
        //     $this->im = imagecreatefrompng('/home/jens/temp/bild.png');
        //}
        //else {
        //     $this->im = imagecreate(800, 480);
        //}

        $block = Utility::timeToBlock($now);

        // load fonts and colors
        $this->gdFont10x20normal = imageloadfont(__DIR__ . '/../Resources/fonts/gd/X_12x24_LE.gdf');
        $this->gdFont11x19normal = imageloadfont(__DIR__ . '/../Resources/fonts/gd/Luc_11x19_LE.gdf');
        $this->gdFont6x12normal  = imageloadfont(__DIR__ . '/../Resources/fonts/gd/X_9x15_LE.gdf');

        $this->fontNormal = __DIR__ . '/../Resources/fonts/ttf/FiraSans-Book.ttf';
        $this->fontBold   = __DIR__ . '/../Resources/fonts/ttf/FiraSans-Medium.ttf';

        $this->black = imagecolorallocate($this->im, 0, 0, 0);
        $this->white = imagecolorallocate($this->im, 255, 255, 255);

        // derive name of room from id
        $this->roomName = $room['name'];

        // render room info

        $this->roomInfo(substr($id, 0, 1), substr($id, 1), $this->roomName);

        $lab = !($id == 'A105a' || $id == 'A212a');
        $roomBookings = [null, null, null, null, null, null, null];
        if ($lab) {

            foreach ($bookings as $booking) {
                $roomBookings[$booking->block - 1] = $booking;
            }
        }


        if ($lab) {
            $this->renderLabRoom($block, $roomBookings);
        }
        else {
            $this->renderMeetingRoom($now, $bookings);
        }

        imagestring($this->im, $this->gdFont6x12normal, self::LEFT_MARGIN_TEXT ,
            455, 'Aktualisiert ' . $now->format('d.m.Y H:i') . ' Uhr', $this->black);
    }

    /**
     * Render a text using a pixel font.
     *
     * @param int $font the font
     * @param int $xPos x position
     * @param int $yPos y position
     * @param string $string the text to be rendered
     */
    private function textPixelLeftAligned($font, $xPos, $yPos, $string) {
        $stringLatin1 = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
        imagestring($this->im, $font, $xPos, $yPos, $stringLatin1, $this->black);
    }

    /**
     * Render a text using a pixel font, right aligned.
     *
     * @param int $font the font
     * @param int $xPos x position
     * @param int $yPos y position
     * @param string $string the text to be rendered
     */
    private function textPixelRightAligned($font, $xPos, $yPos, $string) {
        $newXPos = $xPos - self::textWidth($font, $string);
        $this->textPixelLeftAligned($font, $newXPos, $yPos, $string);
    }

    /**
     * Calculate the width of a pixel text.
     *
     * @param int $font the font
     * @param string $string the text
     *
     * @return int width in pixel
     */
    private static function textWidth($font, $string) {
        return imagefontwidth($font) * strlen($string);
    }

    /**
     * Convert the content of this object to a image file.
     *
     * @param $filePath string path to the file
     */
    public function toImage($filePath) {
        imagepng($this->im, $filePath);
        $cmd = 'export DYLD_LIBRARY_PATH=opt/local/lib; /opt/local/bin/convert -colorspace gray -colors 2 -normalize -threshold 99% '
            . $filePath . ' ' . '/home/jens/temp/result_sw.png';
        $results = shell_exec($cmd);
    }

    /**
     * Calculates the width of a string.
     *
     * @param int $fontSize selected font size
     * @param string $font selected font
     * @param string $string the text to measure
     *
     * @return int the width in pixel
     */
    private static function width($fontSize, $font, $string) {
        $box = imagettfbbox($fontSize, 0.0, $font, $string);
        return $box[2];
    }

    /**
     * Draw a line on the image.
     *
     * @param $x1 int x1 position
     * @param $y1 int y1 position
     * @param $x2 int x2 position
     * @param $y2 int y2 position
     */
    private function line($x1, $y1, $x2, $y2) {
        imageline($this->im, $x1, $y1, $x2, $y2, $this->black);
    }

    /**
     * Right align a text written using a TTF font.
     *
     * @param $fontSize int size of the text
     * @param $xpos int x position of the text
     * @param $ypos int y position of the text
     * @param $font int font to be used
     * @param $text string text to be rendered
     */
    private function textTTFRightAligned($fontSize, $xpos, $ypos, $font, $text) {
        $box = imagettfbbox($fontSize, 0.0, $font, $text);
        $width = $box[2];
        imagefttext($this->im, $fontSize, 0.0, $xpos - $width, $ypos, $this->black, $font, $text);
    }

    /**
     * Left align a text written using a TTF font.
     *
     * @param $fontSize int size of the text
     * @param $xpos int x position of the text
     * @param $ypos int y position of the text
     * @param $font int font to be used
     * @param $text string text to be rendered
     */
    private function textTTFLeftAligned($fontSize, $xpos, $ypos, $font, $text) {
        imagefttext($this->im, $fontSize, 0.0, $xpos, $ypos, $this->black, $font, $text);
    }

    /**
     * Returns the name of a room specified by the number.
     *
     * @param string $number the number of the room (e.b. A106)
     *
     * @return string the room name
     */
    private function roomName($number) {
        switch ($number) {
            case 'A005':  return 'Raskin';
            case 'A008':  return 'Dijkstra';
            case 'A010':  return 'Noether';
            case 'A012':  return 'Gray';
            case 'A012a': return 'Wingert';
            case 'A105a': return 'Jobs';
            case 'A107':  return 'Turing';
            case 'A108':  return 'Nygaard';
            case 'A111':  return 'Shannon';
            case 'A205':  return 'Ritchie';
            case 'A212a': return 'von Neumann';
            case 'L303':  return 'Zuse';
            default:      return 'Hörsaal';
        }
    }

    /**
     * Render sign for a lab room.
     *
     * @param int $block the current block
     * @param Booking[] $bookings the bookings
     */
    private function renderLabRoom($block, $bookings) {

        // we assume that the blocks are stored in the array with the block number - 1 as index.
        // empty blocks are indicated by null
        $currentLecture = $bookings[$block - 1];

        $time = Utility::decodeBlock($block);

        if ($currentLecture == null) {
            $lecture = "Raum ist zur Zeit nicht belegt";
            $responsible = "";
            $cancelled = false;
        }
        else {
            $lecture = "$currentLecture->description ($currentLecture->lecture)";
            // $responsible = $currentLecture->responsibleLong;
            $cancelled = $currentLecture->cancelled;
        }

        $this->renderCurrentEvent($lecture, $time, null, $cancelled);

        if ($block >= 7) {
            return;
        }

        $this->textPixelLeftAligned($this->gdFont11x19normal, self::LEFT_MARGIN_TEXT, 300, 'Nächste Veranstaltungen:');

        $ypos = 325;
        $count = 0;

        for ($i = $block; $i < count($bookings); $i++) {
            $l = $bookings[$i];
            $time = Utility::decodeBlock($i + 1, false);

            if ($l == null) {
                $lecture = "<Raum ist nicht belegt>";

                $this->textPixelRightAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT + 151, $ypos, $time);
                $this->textPixelLeftAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT + 185, $ypos, $lecture);
            }
            else {
                //$lecture = "$l->description ($l->lecture)";
                $arr = explode("\n", $l->description, 3);
                $lecture = $l->lecture . ' ' . ($l->responsibleLong);
                //$lectureDetails = $arr[0];

                $this->textPixelRightAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT + 151, $ypos, $time);
                $this->textPixelLeftAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT + 185, $ypos, $lecture);
                //$this->textPixelLeftAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT + 200, $ypos, $lectureDetails);
            }



            $ypos += 25;

            if (++$count > 4) {
                break;
            }
        }
    }

    /**
     * Render the information for a meeting room.
     *
     * @param \DateTime $now now
     * @param Booking[] $bookings the bookings for the room
     */
    private function renderMeetingRoom(\DateTime $now, $bookings) {
        $ypos = 315 - 10;

        $count = 0;
        $occupied = false;

        foreach ($bookings as $booking) {

            // skip sessions that were in the past
            if ($booking->end < $now) {
                continue;
            }

            // data of the event
            $start = $booking->start->format('H:i');
            $end   = $booking->end->format('H:i');


            if (empty($booking->description)) {
                $event = $booking->lecture;
            } else {
                $event = $booking->description;
            }



            $time = "$start-$end Uhr";

            if ($booking->start <= $now && $now <= $booking->end) {
                // the current event
                $this->renderCurrentEvent($event, $time, $booking->responsibleLong);
                $occupied = true;
            }

            else {
                // an event in the future


                $event = substr($event, 0, 30);

                $this->textPixelRightAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT + 151, $ypos, $time);
                $this->textPixelLeftAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT + 185, $ypos, $event . ' ' . $booking->responsibleLong);

                $ypos += 25;
                $count++;
            }

            if ($count > 5) {
                // do not show more than 5 future events
                break;
            }

        }


        $nextSession = $this->findNextSession($now, $bookings);

        if (!$occupied) {
            // no current sessions means that the room is vacant
            $lecture = "<Raum ist nicht belegt>";

            if ($nextSession != null) {
                // it is free from now until the next session
                $time = $now->format('H:i') . "-" . $nextSession->start->format('H:i') . " Uhr";
            }
            else {
                $time = '';
            }

            $this->renderCurrentEvent($lecture, $time, null);
        }

        if ($nextSession != null) {
            // only show heading for next session if there is one
            $this->textPixelLeftAligned($this->gdFont11x19normal, self::LEFT_MARGIN_TEXT, 275, 'Nächste Reservierungen:');
        }

    }

    /**
     * Find the next session that will happen.
     *
     * @param \DateTime $now now
     * @param Booking[] $bookings the bookings we are searching in
     *
     * @return Booking|null the found booking or null if none was available
     */
    private function findNextSession(\DateTime $now, $bookings) {

        foreach ($bookings as $booking) {
            if ($booking->start >= $now) {
                return $booking;
            }
        }

        return null;
    }

    /**
     * Render the current event.
     *
     * @param string $eventName the name of the event
     * @param string $time the time span
     * @param string $responsible the person responsible for the event
     * @param boolean $cancelled true if event was cancelled
     */
    private function renderCurrentEvent($eventName, $time, $responsible, $cancelled = false) {

        // adjust font size to avoid running over the right margin
        $fontSizeLecture = 24;

        do {
            $fontSizeLecture--;
            $widthLecture = self::width($fontSizeLecture, $this->fontBold, $eventName);
        } while ($widthLecture > 630);

        // render the time
        $xPosTime = 480;// + $widthLecture;
        $this->textPixelRightAligned($this->gdFont10x20normal, $xPosTime, 160, $time);

        // render event and person responsible
        $this->textTTFLeftAligned($fontSizeLecture, self::LEFT_MARGIN_TEXT, 212, $this->fontBold, $eventName);
        $this->textPixelLeftAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT, 225, $responsible);

        // cross out cancelled events
        if ($cancelled) {
            $this->line(self::LEFT_MARGIN_TEXT - 10, 199, self::LEFT_MARGIN_TEXT + $widthLecture + 10, 199);
            $this->line(self::LEFT_MARGIN_TEXT - 10, 200, self::LEFT_MARGIN_TEXT + $widthLecture + 10, 200);
            $this->line(self::LEFT_MARGIN_TEXT - 10, 201, self::LEFT_MARGIN_TEXT + $widthLecture + 10, 201);
            $this->line(self::LEFT_MARGIN_TEXT - 10, 202, self::LEFT_MARGIN_TEXT + $widthLecture + 10, 202);

            $this->textPixelLeftAligned($this->gdFont10x20normal, self::LEFT_MARGIN_TEXT, 160, 'Veranstaltung fällt aus');
        }
    }

    /**
     * Convert the content of this object to a image file.
     *
     * @param $filePath string path to the file
     */
    public function toFile($filePath)
    {
        //header( "Content-type: image/png" );
        imagefilter($this->im, IMG_FILTER_GRAYSCALE);
        $rotated_image = imagerotate($this->im, 90, 0);
        imagepng($rotated_image, $filePath);
        //imagepng($this->im, $filePath+'_sw');
        //$cmd = 'export DYLD_LIBRARY_PATH=opt/local/lib; /opt/local/bin/convert -colorspace gray -colors 2 -normalize -threshold 99% '
        //    . $filePath . ' ' . '/home/jens/temp/bild_sw.png';
        // $fileCompressed = $filePath + $id + '_sw.png';
        // $cmd = 'export DYLD_LIBRARY_PATH=opt/local/lib; /opt/local/bin/convert -colorspace gray  -colors 2  -normalize ' . $filePath;
        // $results = shell_exec($cmd);
    }


    /**
     * Optimizes PNG file with pngquant 1.8 or later (reduces file size of 24-bit/32-bit PNG images).
     *
     * You need to install pngquant 1.8 on the server (ancient version 1.0 won't work).
     * There's package for Debian/Ubuntu and RPM for other distributions on http://pngquant.org
     *
     * @param $path_to_png_file string - path to any PNG file, e.g. $_FILE['file']['tmp_name']
     * @param $max_quality int - conversion quality, useful values from 60 to 100 (smaller number = smaller file)
     * @return string - content of PNG file after conversion
     */
    public function compress_png($path_to_png_file)
    {
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }

        // guarantee that quality won't be worse than that.
        $max_quality = 90;
        $min_quality = 60;

        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < " . escapeshellarg($path_to_png_file));

        if (!$compressed_png_content) {
            throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }

        return $compressed_png_content;
    }
}