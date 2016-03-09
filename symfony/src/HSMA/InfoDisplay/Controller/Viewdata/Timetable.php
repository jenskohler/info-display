<?php
/* (c) 2016 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\Viewdata;


use HSMA\InfoDisplay\Entity\Domain\TimeTableEntry;

class Timetable {

    private $semester = [ ];

    private $blocks = [ 0, 1, 2, 3, 4, 5 ];

    private $data;

    private $news = [ ];


    public function add($news) {
        $this->news[] = $news;
    }

    public function getNews() {
        return $this->news;
    }

    public function getAllSemester() {
        return array_keys($this->semester);
    }

    public function getAllBlocks() {
        return $this->blocks;
    }

    public function addSemester($semester) {
        $this->semester[$semester] = true;
    }

    public function addEntry(TimeTableEntry $entry) {
        $this->data[$entry->block][$entry->semester] = $entry;
        $this->semester[$entry->semester] = true;
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

    public function getEntry($semester, $block) {

        if (!array_key_exists($block, $this->data) ||
            !array_key_exists($semester, $this->data[$block])) {
            return new TimeTableEntry();
        }
        else {
            return $this->data[$block][$semester];
        }
    }

    public function for_each($function) {

        if (isset($this->data)) {
            array_walk_recursive($this->data, $function);
        }
    }
}