<?php
namespace HSMA\InfoDisplay\Entity\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Room
 * @package HSMA\InfoDisplay\Entity\Domain
 *
 * @ORM\Entity
 * @ORM\Table(name="room")
 */
class Room {

    /**
     * @var integer id of the entry
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string name
     *
     * @ORM\Column(type="string", name="name")
     */
    public $name;

    /**
     * @var string description
     *
     * @ORM\Column(type="string", name="description")
     */
    public $description;

    /**
     * @var integer capacity
     *
     * @ORM\Column(type="integer", name="capacity")
     */
    public $capacity;

    /**
     * @var boolean usesBlock
     *
     * @ORM\Column(type="boolean", name="usesblocks")
     */
    public $usesBlock;

    /**
     * @var string link
     *
     * @ORM\Column(type="string", name="link")
     */
    public $link;

    /**
     * Room constructor.
     *
     * @param $id
     * @param $name
     * @param $description
     * @param $capacity
     * @param $usesBlock
     * @param $link
     */
    public function __construct($id, $name, $description, $capacity, $usesBlock, $link) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->usesBlock = $usesBlock;
        $this->link = $link;
    }

    public function __toString() {
        return "[$this->id] Room $this->name ($this->description): " .
                "Capacity = $this->capacity, " .
                "Uses Blocks = $this->usesBlock, " .
                "URL = $this->url";
    }
}