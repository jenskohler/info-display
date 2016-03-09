<?php
/* (c) 2015 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\Viewdata;


use HSMA\InfoDisplay\Entity\Domain\Booking;
use HSMA\InfoDisplay\Entity\Domain\TimeTableEntry;

class RoomBookings {

    private $rooms = [ ];

    private $blocks = [ 0, 1, 2, 3, 4, 5 ];

    private $data = [ ];

    public function getAllRooms() {
        return array_keys($this->rooms);
    }

    public function getAllBlocks() {
        return $this->blocks;
    }

    public function addRoom($room) {
        $this->rooms[$room] = true;
    }

    public function addEntry(Booking $entry) {
        $this->data[$entry->block][$entry->room] = $entry;
        $this->addRoom($entry->room);
    }

    public function addEntries($entries) {
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }
    }

    public function getEntries($block) {
        if (!array_key_exists($block, $this->data)) {
            return new TimeTableEntry();
        }
        else {
            return $this->data[$block];
        }
    }

    public function hasEntry($room, $block) {
        return array_key_exists($block, $this->data) && array_key_exists($room, $this->data[$block]);
    }

    public function getEntry($room, $block) {

        if (!$this->hasEntry($room, $block)) {
            return null;
        }
        else {
            return $this->data[$block][$room];
        }
    }

    public function for_each($function) {

        if (isset($this->data)) {
            array_walk_recursive($this->data, $function);
        }
    }
}