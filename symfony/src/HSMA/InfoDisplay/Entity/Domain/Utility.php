<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\Entity\Domain;

use HSMA\InfoDisplay\Controller\Config;

/**
 * Class Utility
 * @package HSMA\InfoDisplay\Entity\Domain
 *
 * Generic helper functions.
 */
class Utility {

    /**
     * Return a greeting corresponding to the gender of the student. If the
     * gender abbreviation is unknown, the method returns an empty string.
     *
     * @param string $gender gender as single character info ('M', 'F')
     *
     * @return string the german greeting matching the gender ('Herr', 'Frau', '')
     */
    public static function decodeGender($gender) {
        if ($gender == 'M') {
            return 'Herr';
        }
        elseif ($gender == 'F') {
            return 'Frau';
        }
        else {
            return '';
        }
    }

    /**
     * Convert the semester number (e.g. 101) into the correct string
     * (e.g. SS 2010).
     *
     * @param string $semester semester number
     *
     * @return string resulting name of the semester
     */
    public static function decodeSemester($semester) {

        if (!isset($semester)) {
            return "";
        }

        $year = substr($semester, 0, 2);
        $part = substr($semester, 2, 1);

        $year = 2000 + intval($year);

        if ($part == '1') {
            $semesterText = "SS " . $year;

        }
        else {
            $semesterText = "WS " . $year . "/" . ($year + 1);
        }

        return $semesterText;
    }

    /**
     * Checks whether a given date is locate in the winter term.
     *
     * @param \DateTime $date date to check
     *
     * @return bool true if the given date is located in the winter term
     */
    public static function winterTerm(\DateTime $date) {
        return !self::summerTerm($date);
    }

    /**
     * Checks whether a given date is locate in the summer term.
     *
     * @param \DateTime $date date to check
     *
     * @return bool true if the given date is located in the summer term
     */
    public static function summerTerm(\DateTime $date) {
        $month = $date->format('n');

        switch ($month) {
            case 1:
            case 2:
                return false;

            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
                return true;

            case 9:
            case 10:
            case 11:
            case 12:
                return false;

            default:
                return false;
        }
    }
    /**
     * @param $block
     * @param bool|false $summer
     *
     * @return string
     */
    public static function decodeBlock($block, $summer = false) {
        // TODO: Some logic in DisplayController
        switch ($block) {
            case 1: return "8:00-9:30 Uhr";
            case 2: return "9:45-11:15 Uhr";
            case 3: return $summer ? "12:00-13:30 Uhr" : "11:30-13:00 Uhr";
            case 4: return "13:40-15:10 Uhr";
            case 5: return "15:20-16:50 Uhr";
            case 6: return "17:00-18:30 Uhr";
            case 7: return "ab 18:30 Uhr";
        }
    }

    // TODO: Some logic in DisplayController
    public static function timeToBlock(\DateTime $time) {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $diff = $time->diff($today);
        $h = $diff->h;
        $m = $diff->m;

        switch ($h) {
            case  8: return 1;
            case  9: return ($m <= 45) ? 1 : 2;
            case 10: return 2;
            case 11: return ($m <= 30) ? 2 : 3;
            case 12: return 3;
            case 13: return ($m <= 40) ? 3 : 4;
            case 14: return 4;
            case 15: return ($m <= 20) ? 4 : 5;
            case 16: return 5;
            case 17: return 6;
            case 18: return 6;
            default: return 7;
        }
    }

    /**
     * Convert the result number (0 for failed, 1 for passed) into a
     * string.
     *
     * @param integer $result the result as a number
     *
     * @return string the result as a string
     */
    public static function decodeResult($result) {
        return (!$result || ($result == Achievement::FAILED)) ? "nicht bestanden" : "bestanden";
    }

    /**
     * Convert a bool into a green check mark (true) of a red cross (false).
     *
     * @param bool $value the value to be converted
     *
     * @return string the HTML code for the sign
     */
    public static function decodeBoolean($value) {
        return ($value) ?
            "<span style='color: green;'>&#x2714;</span>" :
            "<span style='color: red;'>&#x2718;</span>";
    }

    /**
     * Convert an UTF-8 or ISO-8859-1 string into Windows-1252 code page.
     * This is needed for CSV files.
     *
     * @param string $string input in UTF-8 oder ISO-8859-1.
     *
     * @return string output in Windows-1252
     */
    public static function convertToWindowsCharset($string) {
        $charset = mb_detect_encoding(
            $string,
            "UTF-8, ISO-8859-1, ISO-8859-15",
            true
        );

        $string = mb_convert_encoding($string, "Windows-1252", $charset);

        return $string;
    }

    /**
     * Creates a JavaScript array from a PHP array.
     *
     * @param array $input PHP array
     *
     * @return string JavaScript array as JSON
     */
    public static function asArray($input) {

        $result = "[ ";

        for ($i = 0; $i < count($input); $i++) {
            $result = $result . "'" . str_replace("'", "\'", $input[$i]) . "'";

            if ($i < count($input) - 1) {
                $result = $result . ", ";
            }
        }

        $result = $result . " ]";

        return $result;
    }

    /**
     * Replace german umlauts with replacement characters.
     *
     * @param string $input the string to be cleaned
     *
     * @return string the result of the replacement
     */
    public static function replaceUmlauts($input) {
        $result = str_replace("ä", "ae", $input);
        $result = str_replace("ö", "oe", $result);
        $result = str_replace("ü", "ue", $result);
        $result = str_replace("ß", "ss", $result);
        $result = str_replace("Ä", "AE", $result);
        $result = str_replace("Ö", "OE", $result);
        $result = str_replace("Ü", "UE", $result);
        $result = str_replace("ß", "ss", $result);

        return $result;
    }

    /**
     * Replacement for the http_response_code function introduced with
     * PHP 5.4 to allow usage on PHP 5.3.
     *
     * @param int $statusCode status code to be set
     */
    public static function send_response_code($statusCode) {
        header(':', true, $statusCode);
    }

    /**
     * Convert a time into a block.
     *
     * @param \DateTime $date convert the given date to block
     * @return integer the block as a number between 0 and 5
     */
    public static function hourToBlock(\DateTime $date) {

        $hour = $date->format('G');
        $minute = $date->format('i');

        $block = -1;

        foreach (Config::TIMES_FOR_BLOCKS as $entry) {
            if (($hour == $entry[0]) && ($minute >= $entry[1]) && ($minute <= $entry[2])) {
                $block = $entry[3];
                break;
            }
        }

        return $block;
    }

    public static function dayInGerman($day) {
        switch ($day) {
            case  1: return 'Montag';
            case  2: return 'Dienstag';
            case  3: return 'Mittwoch';
            case  4: return 'Donnerstag';
            case  5: return 'Freitag';
            case  6: return 'Samstag';
            case  7: return 'Sonntag';
            default: return '';
        }
    }

    /**
     * Get the current date and time.
     *
     * @param \DateTime $date the time and date to convert
     * @return string Date and time as a string.
     */
    public static function getGermanDateAndTime(\DateTime $date = null) {

        if ($date == null) {
            $date = new \DateTime();
        }

        return $date->format('d.m.Y H:i') . ' Uhr';
    }

}

