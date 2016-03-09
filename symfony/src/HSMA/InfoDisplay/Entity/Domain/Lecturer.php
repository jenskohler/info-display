<?php
/* (c) 2015 Thomas Smits */
namespace HSMA\InfoDisplay\Entity\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Lecturer
 * @package HSMA\FacultyDatabase\Entity\Domain
 *
 * Class representing a lecturer.
 *
 * @ORM\Entity
 * @ORM\Table(name="lecturer")
 */
class Lecturer {

    /**
     * @var int id in the database
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     */
    public $id;

    /**
     * @var int id of the internal database of the HR department
     *
     * @ORM\Column(type="integer", name="cis_id")
     */
    public $cis_id;

    /**
     * @var string short name (e.b. SMI, BEN, ...)
     *
     * @ORM\Column(type="string", name="shortname")
     */
    public $shortName;

    /**
     * @var string first name
     *
     * @ORM\Column(type="string", name="firstname")
     */
    public $firstName;

    /**
     * @var string surname
     *
     *
     * @ORM\Column(type="string", name="surname")
     */
    public $surname;

    /**
     * @var string academic title
     *
     * @ORM\Column(type="string", name="title")
     */
    public $title;

    /**
     * @var string email address
     *
     * @ORM\Column(type="string", name="email")
     */
    public $email;

    /**
     * @var string status (PROF, ...)
     *
     *
     * @ORM\Column(type="string", name="status")
     */
    public $status;

    /**
     * @var string gender indicator
     */
    public $gender;

    /**
     * @var boolean can provide achievements
     *
     * @ORM\Column(type="boolean", name="achievements")
     */
    public $achievements;

    /**
     * Create a new object
     *
     * @param int $id id in the database
     * @param int $cis_id HR id number
     * @param string $shortName abbreviated name (e.g. SMI)
     * @param string $status status (see LecturerStatus for constants)
     * @param string $title academic title, if present
     * @param string $firstName first name
     * @param string $surname surname
     * @param string $email email address
     * @param boolean $achievements allowed to report achievements?
     */
    function __construct($id, $cis_id, $shortName, $status, $title, $firstName, $surname, $email, $achievements) {
        $this->achievements = $achievements;
        $this->shortName = $shortName;
        $this->cis_id = $cis_id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->id = $id;
        $this->status = $status;
        $this->surname = $surname;
        $this->title = $title;
    }

    /**
     * @return string string representation of the object
     */
    public function __toString() {

        if (isset($this->title) && strlen($this->title) > 0) {
            return $this->title . ' ' . $this->surname;
        }
        else {
            return $this->surname;
        }
    }

    /**
     * Convert this object into an JSON array
     *
     * @return array a JSON representation of this object
     */
    public function toJSON() {
        return [
            'id'        => $this->id,
            'shortname' => $this->shortName,
            'surname'   => $this->surname,
            'firstname' => $this->firstName,
            'title'     => $this->title,
            'email'     => $this->email,
        ];
    }

    /**
     * Convert all lecturers into an array with the id as key
     *
     * @param Lecturer[] $lecturers the lecturers
     *
     * @return array the hash with id as key
     */
    public static function lecturersToArray($lecturers) {

        $result = array();

        foreach ($lecturers as $lecturer) {
            $result[$lecturer->id] = $lecturer;
        }

        return $result;
    }
}
