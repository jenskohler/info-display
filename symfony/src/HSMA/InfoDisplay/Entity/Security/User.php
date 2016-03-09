<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\Entity\Security;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package HSMA\InfoDisplay\Entity\Security
 *
 * Representation of a user in the data model.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface, EquatableInterface, Serializable {

    /**
     * @var int id in the database.
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string name of the user
     *
     * @ORM\Column(type="string", name="username")
     */
    private $username;

    /**
     * @var ArrayCollection roles the user has
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    /**
     * @var string password
     *
     * @ORM\Column(type="string", name="password")
     */
    private $password;

    /**
     * @var string API key
     *
     * @ORM\Column(type="string", name="key")
     */
    private $key;

    /**
     * @var string salt for password hashing
     */
    private $salt;

    /**
     * @param int $id database id of the user
     * @param string $username name of the user
     * @param string $password user's password
     * @param string $salt salt for hashing
     * @param array $roles roles the user has
     */
    public function __construct($id, $username, $password, $salt, array $roles) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = new ArrayCollection();
    }

    /**
     * Return the database key of the user.
     *
     * @return int database id
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
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles() {
        return $this->roles->toArray();
    }

    /**
     * Set the roles of the user.
     *
     * @param array $roles roles
     */
    public function setRoles(array $roles) {
        $this->roles = $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set the password (will only be used by OR-Mapper)
     *
     * @param string $password encoded password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Set the salt (will only be used by OR-Mapper).
     *
     * @param string $salt the salt
     */
    public function setSalt($salt) {
        $this->salt = $salt;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set the user name (will only be used by OR-Mapper).
     *
     * @param string $username name of the user
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {
        // nothing to to as we don't store credentials
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user) {

        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * Serialize object into a string.
     *
     * @return string the serialized form
     */
    public function serialize() {
        // it is important to serialize the salt and password to avoid the user to
        // be reloaded on every request from the database. Due to the fact that
        // we want to allow authentication against an LDAP server, reloading the
        // user on every request is not efficient
        return serialize(array($this->id, $this->username, $this->salt, $this->password, $this->key));
    }

    /**
     * De-serialize object from string.
     *
     * @param string $data serialied object
     */
    public function unserialize($data) {
        list($this->id, $this->username, $this->salt, $this->password, $this->key) = unserialize($data);
    }

    /**
     * @return string string representation of object
     */
    public function __toString() {
        return $this->username;
    }
}
