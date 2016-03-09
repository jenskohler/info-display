<?php
namespace HSMA\InfoDisplay\Entity\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TimeTableEntry
 * @package HSMA\InfoDisplay\Entity\Domain
 *
 * @ORM\Entity
 * @ORM\Table(name="timetable")
 */
class TimeTableEntry {

    /**
     * @var integer id of the entry
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string semester
     *
     * @ORM\Column(type="string", name="semester")
     */
    public $semester;

    /**
     * @var integer day of the week (starting at 0)
     *
     * @ORM\Column(type="integer", name="dayofweek")
     */
    public $dayOfWeek;

    /**
     * @var integer block (starting at 0)
     *
     * @ORM\Column(type="integer", name="block")
     */
    public $block;

    /**
     * @var string lecture short name
     *
     * @ORM\Column(type="string", name="lecture")
     */
    public $lecture;

    /**
     * @var string long name of the lecture
     */
    public $lectureLong;

    /**
     * @var string room short name
     *
     * @ORM\Column(type="string", name="room")
     */
    public $room;

    /**
     * @var string lecturer short name
     *
     * @ORM\Column(type="string", name="lecturer")
     */
    public $lecturer;

    /**
     * @var boolean indicates that the lecture was cancelled
     */
    public $cancelled;

    /**
     * @var NewsEntry news attached to this element
     */
    public $news;

    /**
     * Returns a unique key for this entry.
     *
     * @return string a unique key
     */
    public function getKey() {
        return $this->semester . "_D"
                . $this->dayOfWeek . "_B"
                . $this->block . "_"
                . $this->lecture;
    }

    /**
     * Convert this object into an JSON array
     *
     * @return array a JSON representation of this object
     */
    public function toJSON() {
        return [
            'id' => $this->id,
            'semester' => $this->semester,
            'dayOfWeek' => $this->dayOfWeek,
            'block' => $this->block,
            'lecture' => $this->lecture,
            'lecturer' => $this->lecturer,
            'room' => $this->room
        ];
    }
}