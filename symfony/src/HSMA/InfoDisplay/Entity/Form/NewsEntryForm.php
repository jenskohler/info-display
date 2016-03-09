<?php
namespace HSMA\InfoDisplay\Entity\Form;

use HSMA\InfoDisplay\Entity\Domain\Lecturer;
use HSMA\InfoDisplay\Entity\Domain\TimeTableEntry;


/**
 * Class NewsEntry
 * @package HSMA\InfoDisplay\Entity\Form
 */
class NewsEntryForm {

    /**
     * @var string Date until the entry is valid
     */
    public $valid;

    /**
     * @var string Text of the news
     */
    public $text;

    /**
     * @var TimeTableEntry[] lecture short name
     */
    public $lecture = [];

    /**
     * @var Lecturer lecturer short name
     */
    public $lecturer;

}