<?php
/* (c) 2014 Thomas Smits */

namespace HSMA\InfoDisplay\Security;

use Doctrine\Bundle\DoctrineBundle\Registry;
use HSMA\InfoDisplay\Entity\Security\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class ApiKeyUserProvider
 * @package HSMA\InfoDisplay\Security
 *
 * User provider for API key based authentication.
 */
class ApiKeyUserProvider implements UserProviderInterface {

    /** @var Registry $doctrine */
    private $doctrine;

    /**
     * Create a new instance.
     *
     * @param Registry $doctrine the ORM handler
     */
    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Find the user name for the given key.
     *
     * @param string $key API key provided
     *
     * @return null|string the user name or null if no user was found
     */
    public function getUserNameForKey($key) {

        /** @var User $user */
        $user = $this->doctrine
            ->getRepository('HSMA\InfoDisplay\Entity\Security\User')
            ->findOneBy(array('key' => $key));

        if (isset($user)) {
            return $user->getUsername();
        }
        else {
            return null;
        }
    }

    /**
     * Load the user for a given user name.
     *
     * @param string $username the name of the user
     *
     * @return User|UserInterface the found user.
     */
    public function loadUserByUsername($username) {
        /** @var User $user */
        $user = $this->doctrine
            ->getRepository('HSMA\InfoDisplay\Entity\Security\User')
            ->findOneBy(array('username' => $username));

        return $user;
    }

    /**
     * Refresh user information in the session. Due to the fact that we are stateless,
     * this method always throws an exception. Be sure to set 'stateless: true' in
     * the firewall configuration.
     *
     * @param UserInterface $user the user
     *
     * @return UserInterface|void
     */
    public function refreshUser(UserInterface $user) {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless.
        throw new UnsupportedUserException();
    }

    /**
     * Checks whether a given kind of user class is supported by the provider.
     *
     * @param string $class the class to check
     *
     * @return bool true it the class is supported
     */
    public function supportsClass($class) {
        return 'HSMA\InfoDisplay\Entity\Security\User' === $class;
    }
}
