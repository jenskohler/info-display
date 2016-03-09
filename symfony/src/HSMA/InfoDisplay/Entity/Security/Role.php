<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class Role represents a role in the application.
 *
 * @package HSMA\InfoDisplay\Entity\Security
 *
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role implements RoleInterface {

    /**
     * @var int id in the database.
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string name of the role
     *
     * @ORM\Column(type="string", name="rolename")
     */
    private $name;

    /**
     * Create a new object.
     *
     * @param int $id database id
     * @param string $name name of the role
     */
    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int id in the database
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set the id (will only be used by OR-Mapper)
     *
     * @param int $id id to be set
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string name of the role
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the id (will only be used by OR-Mapper)
     *
     * @param string $name name of the role as used in security.yml
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the role.
     *
     * This method returns a string representation whenever possible.
     *
     * When the role cannot be represented with sufficient precision by a
     * string, it should return null.
     *
     * @return string|null A string representation of the role, or null
     */
    public function getRole() {
        return $this->name;
    }

    /**
     * Return a string representation of the role.
     *
     * @return string String representation of the role.
     */
    public function __toString() {
        return $this->name;
    }
}
