<?php
/* (c) 2013 Thomas Smits */
namespace HSMA\InfoDisplay\Entity\Domain;

/**
 * Class AccessKey
 * @package HSMA\InfoDisplay\Entity\Domain
 *
 * Class represents an access key, i.e. a key that is necessary
 * to use the application. The key contains different information
 * about the rights the user has.
 */
class AccessKey {

    /**
     * @var string ID and random number of the key
     */
    public $id;

    /**
     * @var string semester the key is valid for
     */
    public $semester;

    /**
     * @var string course the key is valid for
     */
    public $courseId;

    /**
     * @var bool show the student names in the lecture results
     */
    public $showNames;

    /**
     * @var bool administrator if true
     */
    public $admin;

    /**
     * @var bool show all hidden data
     */
    public $showall;

    /**
     * @var string key is only valid for access to the data
     *      of a particular student
     */
    public $studentId;

    /**
     * @var string comment on the key (especially the owner)
     */
    public $comment;

    /**
     * @var string optional email address of key's owner
     */
    public $email;

    /**
     * Create a new key.
     *
     * @param string $id secret key value and key's id
     * @param string $semester semester the key is valid for
     * @param string $courseId course the key is valid for
     * @param bool $showNames show the student names in the lecture results
     * @param bool $admin administrator if true
     * @param bool $showall show all hidden data
     * @param string $studentId key is only valid for access to the data
     *      of a particular student
     * @param        $comment string comment on the key (especially the owner)
     * @param        $email string optional email address of key's owner
     */
    public function __construct(
        $id,
        $semester,
        $courseId,
        $showNames,
        $admin,
        $showall,
        $studentId,
        $comment,
        $email) {
        $this->id = $id;
        $this->semester = $semester;
        $this->courseId = $courseId;
        $this->showNames = $showNames;
        $this->admin = $admin;
        $this->showall = $showall;
        $this->studentId = $studentId;
        $this->comment = $comment;
        $this->email = $email;
    }
}
