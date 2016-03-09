<?php
/* (c) 2016 Thomas Smits */

namespace HSMA\InfoDisplay\Controller;

use HSMA\InfoDisplay\Controller\Viewdata\Timetable;
use HSMA\InfoDisplay\Persistency\RESTClient;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class TimetableCache
 * @package HSMA\InfoDisplay\Controller
 *
 * Cache and retrieval of timetable information.
 */
class TimetableCache extends Cache {

    /**
     * Create a new instance, using the session for storage.
     *
     * @param SessionInterface $session the current session
     */
    public function __construct(SessionInterface $session) {
        parent::__construct('timetable', Config::TTL_CACHE_TIMETABLE, $session);
    }

    /**
     * Retrieve actual data..
     *
     * @param \DateTime $date date to retrieve data for
     * @return mixed the data retrieved
     */
    protected function retrieveData($date) {

        $client = new RESTClient();
        $allLectures = $client->readTimetableForAllSemesters();

        return $allLectures;
    }

    /**
     * Filter the data for the given date.
     *
     * @param mixed $data data to be filtered
     * @param \DateTime $date Date to filter data for
     *
     * @return mixed the data
     */
    protected function filterData($data, \DateTime $date) {
        $timetable = new Timetable();
        $dayOfWeek = $date->format('N');

        if ($data == null) {
            return $timetable;
        }

        foreach ($data as $semester => $lectures) {
            foreach ($lectures as $lecture) {
                if ($lecture->dayOfWeek == $dayOfWeek) {
                    $timetable->addEntry($lecture);
                }
            }
        }

        return $timetable;
    }

    public function getTimetableForLecturer($id) {

        $plan = $this->getData();

        $result = [ ];

        // reduce to entries for the given lecturer
        foreach ($plan as $key => $semesterData) {
            foreach ($semesterData as $entry) {
                if ($entry->lecturer == $id) {
                    $result[] = $entry;
                }
            }
        }

        usort($result, function($e1, $e2) {
            return $e1->dayOfWeek - $e2->dayOfWeek;
        });

        return $result;
    }
}