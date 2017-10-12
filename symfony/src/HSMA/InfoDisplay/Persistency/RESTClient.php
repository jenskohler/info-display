<?php
namespace HSMA\InfoDisplay\Persistency;
/*
require_once '../../../../vendor/nategood/httpful/src/Httpful/Request.php';
require_once '../../../../vendor/nategood/httpful/src/Httpful/Http.php';
require_once '../../../../vendor/nategood/httpful/src/Httpful/Bootstrap.php';
require_once '../Entity/Domain/TimeTableEntry.php';
*/
use HSMA\InfoDisplay\Controller\Config;
use HSMA\InfoDisplay\Controller\Viewdata\Timetable;
use HSMA\InfoDisplay\Entity\Domain\Room;
use HSMA\InfoDisplay\Entity\Domain\Booking;
use HSMA\InfoDisplay\Entity\Domain\TimeTableEntry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class RESTClient
 * @package HSMA\InfoDisplay\Persistency
 * Client for the REST API provided by the room management solution.
 */
class RESTClient {

    /** Date format used by the REST API */
    const DATE_FORMAT = 'd.m.Y';

    /** Time format used by the REST API */
    const TIME_FORMAT = 'H:i';

    /** Time format used by the REST API */
    const TIME_FORMAT_EXTENDED = 'H:i:s';

    const ALL_SEMESTERS  = [
        '1IB',
        '2IB',
        '3IB',
        '4IB',
        '6IB',
        '7IB',
        '1UIB',
        '2UIB',
        '3UIB',
        '4UIB',
        '6UIB',
        '7UIB',
        '1IMB',
        '2IMB',
        '3IMB',
        '4IMB',
        '6IMB',
        '7IMB',
        '1IM',
        '2IM',
        '1IMM',
        '2IMM',
    ];

    /**
     * Base part of the URL.
     *
     * @var string
     */
    private $baseURL;

    /**
     * @param $baseURL string Base part of the URL.
     */
    public function __construct($baseURL = 'https://intern.informatik.hs-mannheim.de') {
        $this->baseURL = $baseURL;
    }

    /**
     * Read all available rooms.
     *
     * @return Room[] all rooms found
     */
    public function readAllRooms() {

        $url = "$this->baseURL/rooms/api/room";

        $restResponse = \Httpful\Request::get($url)
            ->expectsJson()
            ->send();

        $json = $restResponse->body;

        $result = [ ];

        foreach ($json->rooms as $room) {
            $roomObject = new Room(
                $room->id,
                $room->name,
                $room->description,
                $room->capacity,
                $room->usesblock == 1,
                $room->link);

            $result[] = $roomObject;
        }

        return $result;
    }

    /**
     * Retrieve all room bookings for the given time span. The performance of this method
     * heavily depends on the number of days data is requested for because each day results
     * in a single REST call.
     *
     * @param string $room Room to retrieve data for
     * @param \DateTime $from from date
     * @param \DateTime $to to date
     *
     * @return Booking[] the found bookings
     */
    public function readRoomBookingsForTimeSpan($room, \DateTime $from, \DateTime $to) {

        $interval = $to->diff($from);
        $days = $interval->d;
        $date = new \DateTime();
        $date->setTimestamp($from->getTimestamp());
        $oneDay = new \DateInterval('P1D');

        $result = [];

        for ($i = 0; $i <= $days; $i++) {
            $bookings = $this->readRoomBookings($room, $date);
            $result = array_merge($result, $bookings);
            $date->add($oneDay);
        }

        return $result;
    }

    /**
     * Retrieves the room booking information and puts it into an array with a slot for
     * each block of the day. The element at position 0 corresponds to block 1 etc.
     *
     * @param string $room the Room number
     * @param \DateTime|null $date the date the data is requested for (the time part of the date is ignored). If set
     *      to null, the info is retrieved for the current date.
     *
     * @return Booking[] the bookings mapped to blocks
     */
    public function readRoomBookingsAsBlocks($room, \DateTime $date = null) {

        $bookings = $this->readRoomBookings($room, $date);

        $result = [ null, null, null, null, null, null ];

        foreach ($bookings as $booking) {
            $result[$booking->block] = $booking;
        }

        return $result;
    }

    /**
     * Read the timetable for all semesters.
     *
     * @param \DateTime|null $date date to retrieve data for
     * @return array the timetable with the semester as key
     */
    public function readTimetableForAllSemesters(\DateTime $date = null) {

        $result = [ ];

        foreach (self::ALL_SEMESTERS as $semester) {
            $result[$semester] = $this->readTimetableForSemester($semester, $date);
        }

        return $result;
    }

    /**
     * Read the timetable for the given lecturer.
     *
     * @param String $lecturer short name (ID) of the lecturer
     * @return Timetable timetable for the lecturer
     */
    public function readTimetableForLecturer($lecturer) {

        $plan = $this->readTimetableForAllSemesters();

        $result = [ ];

        // reduce to entries for the given lecturer
        foreach ($plan as $key => $semesterData) {
            foreach ($semesterData as $entry) {
                if ($entry->lecturer == $lecturer) {
                    $result[] = $entry;
                }
            }
        }

        usort($result, function($e1, $e2) {
            return $e1->dayOfWeek - $e2->dayOfWeek;
        });

        return $result;
    }

    /**
     * Internal function to retrieve the timetable from the REST service.
     *
     * @param string $semester the semester
     * @param \DateTime $dayOfWeek day of week
     *
     * @return TimeTableEntry[] the timetable for the semester
     */
    private function retrieveTimetable($semester, \DateTime $date = null) {

        $url = "$this->baseURL/stundenplan/stundenplan_json.php?sem=$semester";

        if ($date != null) {
            $url = $url . "&tag=" . $this->numberToDay($date->format('N'));
        }

        try {
            $restResponse = \Httpful\Request::get($url)
                ->expectsJson()
                ->send();
        }
        catch (\Exception $e) {
            // no data for the given semester
            return [ ];
        }

        $json = $restResponse->body;

        $result = [ ];

        foreach ($json->vorlesungen as $entry) {

            if (isset($entry->Fehler)) {
                continue;
            }

            $timetableEntry = new TimeTableEntry();
            $timetableEntry->block = intval($entry->stunde) - 1;
            $timetableEntry->dayOfWeek = $this->dayToNumber($entry->tag);
            $timetableEntry->lecture = $entry->fach;
            $timetableEntry->lectureLong = $entry->fach_lang;
            $timetableEntry->lecturer = $entry->dozent;
            $timetableEntry->semester = $semester;
            $timetableEntry->room = $entry->raum;

            $result[] = $timetableEntry;
        }

        return $result;

    }

    /**
     * Read the timetable for the given semester.
     *
     * @param string $semester the Semester (e.g. 1IB) to retrieve data for
     * @param \DateTime $date the date to retrieve data for
     *
     * @return \HSMA\InfoDisplay\Entity\Domain\TimeTableEntry[] the timetable for the semester
     */
    public function readTimetableForSemester($semester, \DateTime $date = null) {
        return $this->retrieveTimetable($semester, $date);
    }

    /**
     * Read the timetable for the given semester and a given date.
     *
     * @param string $semester the Semester (e.g. 1IB) to retrieve data for
     * @param \DateTime $date date to retrieve timetable for
     *
     * @return TimeTableEntry[] the timetable for the semester
     */
    public function readTimetableForSemesterAndDate($semester, \DateTime $date) {
        $dayOfWeek = intval($date->format('N'));
        return $this->retrieveTimetable($semester, $dayOfWeek);
    }

    /**
     * Read the bookings for the room at the given date. If the date is omitted, the bookings
     * for the current week are read.
     *
     * @param string $room the Room id, e.g. 'A108'
     * @param \DateTime $date the date the data is requested for (the time part of the date is ignored). If set
     *      to null, the info is retrieved for the current date.
     *
     * @return mixed
     */
    public function readRoomBookings($room, \DateTime $date = null) {

        if ($date != null) {
            $dateString = $this->dateToString($date);
            $url = "$this->baseURL/rooms/api/booking/$room?date=$dateString";
        }
        else {
            $url = "$this->baseURL/rooms/api/booking/$room";
        }

        $restResponse = \Httpful\Request::get($url)
//            ->expectsJson()
            ->expects("text/plain")
            ->send();

        $body = "{" . $restResponse->body ."}";
        $json = json_decode($body);
        //$json = $restResponse->body;

        $result = [];

        foreach ($json->events as $bookings) {

            $bookingDate = $this->stringToDate($bookings->date);

            if ($bookingDate === false) {
                $bookingDate = $date;
            }

            $start = $this->stringToTime($bookings->start);
            $end   = $this->stringToTime($bookings->end);
            $day   = isset($bookings->weekday)
                ? $this->dayToNumber($bookings->weekday)
                : date( "w", $bookingDate->getTimestamp());

            $bookingObject = new Booking(
                $bookings->room,
                $bookings->name,
                $bookings->description,
                $bookings->responsible,
                $bookings->responsible_long,
                isset($bookings->semester) ? $bookings->semester : '',
                isset($bookings->faculty) ? $bookings->faculty : '',
                $bookings->block - 1,
                $day,
                $bookingDate,
                $start,
                $end,
                isset($bookings->type) ? $bookings->type : 0
            );

            $result[] = $bookingObject;
        }

        uasort($result, function (Booking $a, Booking $b) { return $a->start > $b->start; });

        return $result;
    }

    /**
     * Convert the given string (as delivered by the REST API) containing a date
     * to a date object.
     *
     * @param  string $string the date as a string, e.g. '1.1.2015'
     *
     * @return \DateTime the Date as object. If no data is given or the sting is empty,
     *         method returns 0.0.0 as date.
     */
    private function stringToDate($string) {

        if (strlen($string) > 0) {
            return \DateTime::createFromFormat(self::DATE_FORMAT, $string);
        }
        else {
            return \DateTime::createFromFormat(self::DATE_FORMAT, '0.0.0');
        }
    }

    /**
     * Converts the given date to the string format required by the API.
     *
     * @param \DateTime $date the date to be converted
     *
     * @return string the date as string
     */
    private function dateToString(\DateTime $date) {
       return $date->format(self::DATE_FORMAT);
    }

    /**
     * Converts the given time to the string format required by the API.
     *
     * @param \DateTime $date the time to be converted
     *
     * @return string the time as string
     */
    private function timeToString(\DateTime $date) {
        return $date->format(self::TIME_FORMAT);
    }

    /**
     * Convert the given string (as delivered by the REST API) containing a time
     * to a date object.
     *
     * @param string $string the time as a string, e.g. '9:35'
     *
     * @return \DateTime the Date as object. If no data is given or the sting is empty,
     *         method returns 0:0 as date.
     */
    private function stringToTime($string) {

        $time = null;

        if (strlen($string) > 0) {
            $time = \DateTime::createFromFormat(self::TIME_FORMAT, $string);

            if ($time === false) {
                $time = \DateTime::createFromFormat(self::TIME_FORMAT_EXTENDED, $string);
            }
        }
        else {
            $time = \DateTime::createFromFormat(self::TIME_FORMAT, '00:00');
        }

        return $time;
    }

    /**
     * Convert the day of week, given as string, into a number.
     *
     * @param string $string the day of week as a string
     *
     * @return int 1 for monday, 2 for tuesday ...
     */
    private function dayToNumber($string) {

        switch (strtoupper($string)) {
            case 'MO': return 1;
            case 'DI': return 2;
            case 'MI': return 3;
            case 'DO': return 4;
            case 'FR': return 5;
            case 'SA': return 6;
            case 'SO': return 7;
            default: return 0;
        }
    }

    /**
     * Convert the day of week, given as integer, into a string.
     *
     * @param integer $day day as number
     *
     * @return string the day of week as a string
     */
    private function numberToDay($day) {

        switch ($day) {
            case 1: return 'MO';
            case 2: return 'DI';
            case 3: return 'MI';
            case 4: return 'DO';
            case 5: return 'FR';
            case 6: return 'SA';
            case 7: return 'SO';
            default: return null;
        }
    }
}
/*
$x = new RESTClient();
$r = $x->readTimetableForAllSemesters();
print_r($r);
*/