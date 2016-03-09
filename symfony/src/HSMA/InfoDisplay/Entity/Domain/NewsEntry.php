<?php
namespace HSMA\InfoDisplay\Entity\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class NewsEntry
 * @package HSMA\InfoDisplay\Entity\Domain
 *
 *
 * @ORM\Entity
 * @ORM\Table(name="news")
 */
class NewsEntry {

    /**
     * @var integer id of the entry
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string Date the entry was posted
     *
     * @ORM\Column(type="datetime", name="posted")
     */
    public $posted;

    /**
     * @var string Date until the entry is valid
     *
     * @ORM\Column(type="datetime", name="valid")
     */
    public $valid;

    /**
     * @var string Text of the news
     *
     * @ORM\Column(type="string", name="text")
     */
    public $text;

    /**
     * @var string semester
     *
     * @ORM\Column(type="string", name="semester")
     */
    public $semester;

    public function __construct($id = null) {
        $this->id = $id;
    }
}