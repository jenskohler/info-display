<?php
/* (c) 2016 Thomas Smits */

namespace HSMA\InfoDisplay\Controller;

use HSMA\InfoDisplay\Controller\Viewdata\RoomBookings;
use HSMA\InfoDisplay\Controller\Viewdata\Timetable;
use HSMA\InfoDisplay\Persistency\RESTClient;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class TimetableCache
 * @package HSMA\InfoDisplay\Controller
 *
 * Cache and retrieval of room information.
 */
class RoomCache extends Cache {

    /**
     * Create a new instance, using the session for storage.
     *
     * @param SessionInterface $session the current session
     */
    public function __construct(SessionInterface $session) {
        parent::__construct('rooms', Config::TTL_CACHE_ROOMS, $session);
    }

    /**
     * Retrieve actual data.
     *
     * @param \DateTime $date the date
     * @return mixed the data retrieved
     */
    protected function retrieveData($date) {

        $client = new RESTClient();

        $plan = [ ];

        foreach (Config::ALL_ROOMS as $room) {
            $bookings = $client->readRoomBookings($room);
            $plan[$room] = $bookings;
        }

        return $plan;
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
        $bookings = new RoomBookings();

        if (!isset($data)) {
            return $bookings;
        }

        foreach ($data as $room => $entries) {
            foreach ($entries as $entry) {
                if ($date->format('Y-m-d') == $entry->date->format('Y-m-d')) {
                    $bookings->addEntry($entry);
                }
            }
        }

        return $bookings;
    }
}