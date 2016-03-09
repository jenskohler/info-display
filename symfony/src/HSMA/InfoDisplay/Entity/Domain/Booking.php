<?php
namespace HSMA\InfoDisplay\Entity\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Booking
 * @package HSMA\InfoDisplay\Entity\Domain
 *
 * @ORM\Entity
 * @ORM\Table(name="booking")
 */
class Booking {

    /**
     * @var integer id of the entry
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string room
     *
     * @ORM\Column(type="string", name="room")
     */
    public $room;

    /**
     * @var string lecture
     *
     * @ORM\Column(type="string", name="lecture")
     */
    public $lecture;

    /**
     * @var string description
     *
     * @ORM\Column(type="string", name="description")
     */
    public $description;

    /**
     * @var string responsible
     *
     * @ORM\Column(type="string", name="responsible")
     */
    public $responsible;

    /**
     * @var string responsibleLong
     *
     * @ORM\Column(type="string", name="responsible_long")
     */
    public $responsibleLong;

    /**
     * @var string semester
     *
     * @ORM\Column(type="string", name="semester")
     */
    public $semester;

    /**
     * @var string faculty
     *
     * @ORM\Column(type="string", name="faculty")
     */
    public $faculty;

    /**
     * @var integer block
     *
     * @ORM\Column(type="integer", name="block")
     */
    public $block;

    /**
     * @var integer dayofweek
     *
     * @ORM\Column(type="integer", name="dayofweek")
     */
    public $dayofweek;

    /**
     * @var \DateTime date
     *
     * @ORM\Column(type="date", name="date")
     */
    public $date;

    /**
     * @var \DateTime start
     *
     * @ORM\Column(type="time", name="start")
     */
    public $start;

    /**
     * @var \DateTime end
     *
     * @ORM\Column(type="time", name="end")
     */
    public $end;

    /**
     * @var integer type of the booking
     * 1 = normal lecture series using blocks
     * 2 = single booking using blocks
     * 3 = series in a meeting room
     * 4 = single booking
     *
     * @ORM\Column(type="integer", name="type")
     */
    public $type;

    /**
     * @var boolean indicates that the session is cancelled
     */
    public $cancelled;

    /**
     * Booking constructor.
     *
     * @param string $room
     * @param string $lecture
     * @param string $description
     * @param string $responsible
     * @param string $responsibleLong
     * @param string $semester
     * @param string $faculty
     * @param int $block
     * @param int $dayofweek
     * @param \DateTime $date
     * @param \DateTime $start
     * @param \DateTime $end
     * @param int $type
     */
    public function __construct($room, $lecture, $description, $responsible, $responsibleLong, $semester, $faculty, $block, $dayofweek, \DateTime $date, \DateTime $start, \DateTime $end, $type, $cancelled = false) {
        $this->room = $room;
        $this->lecture = $lecture;
        $this->description = $description;
        $this->responsible = $responsible;
        $this->responsibleLong = $responsibleLong;
        $this->semester = $semester;
        $this->faculty = $faculty;
        $this->block = $block;
        $this->dayofweek = $dayofweek;
        $this->date = $date;
        $this->start = $start;
        $this->end = $end;
        $this->type = $type;
        $this->cancelled = $cancelled;
    }
}