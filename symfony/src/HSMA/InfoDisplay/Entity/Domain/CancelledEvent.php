<?php
namespace HSMA\InfoDisplay\Entity\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CancelledEvent
 * @package HSMA\InfoDisplay\Entity\Domain
 *
 *
 * @ORM\Entity
 * @ORM\Table(name="cancelled")
 */
class CancelledEvent {

    /**
     * @var integer id of the entry
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var integer id of the corresponding news entry.
     *
     * @ORM\Column(type="integer", name="news_id")
     */
    public $newsId;


    /**
     * @var string Date the entry was posted
     *
     * @ORM\Column(type="date", name="posted")
     */
    public $posted;

    /**
     * @var string Date until the entry is valid
     *
     * @ORM\Column(type="date", name="valid")
     */
    public $valid;

    /**
     * @var integer id of the timetable entry
     *
     * @ORM\Column(type="string", name="timetable_key")
     */
    public $timetableKey;

    /**
     * @var NewsEntry news attached to the cancellation.
     */
    public $news;
}