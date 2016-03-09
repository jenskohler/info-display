<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class LdapAuthenticator
 * @package HSMA\InfoDisplay\Security
 *
 * Class to allow authentication with LDAP and against the database.
 * To authenticate a user against an LDAP server, the user must exist also in the
 * database because the roles and permissions are read from the DB. Without a
 * DB entry, the user cannot authenticate.
 *
 * If the user is not present in the LDAP, the class tries to authenticate the user
 * with the encoded password stored in the database. If this succeeds, the user
 * is authenticated.
 */
class LdapAuthenticator implements SimpleFormAuthenticatorInterface {

    /** @var EncoderFactoryInterface password encoder */
    private $encoderFactory;

    /** @var string URL auf the LDAP server */
    private $ldapUrl;

    /** @var string base DSN of the user */
    private $ldapBaseDSN;

    /** @var string the name of the LDAP field containing the username */
    private $ldapUserPrefix;

    /**
     * Error message in case of authentication failure.
     * TODO: Externalize the string
     */
    const ERROR_MESSAGE = 'Ungültiger Benutzername oder ungültiges Passwort.';

    /**
     * Create a new instance.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param string $ldapUrl URL auf the LDAP server
     * @param string $ldapBaseDSN base DSN of the user
     * @param string $ldapUserPrefix name of the LDAP field containing the username
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, $ldapUrl, $ldapBaseDSN, $ldapUserPrefix) {
        $this->encoderFactory = $encoderFactory;
        $this->ldapUrl = $ldapUrl;
        $this->ldapBaseDSN = $ldapBaseDSN;
        $this->ldapUserPrefix = $ldapUserPrefix;
    }

    /**
     * Authenticate the user.
     *
     * @param TokenInterface $token the credentials coming from the form
     * @param UserProviderInterface $userProvider the class providing users (here from the database)
     * @param string $providerKey Key of the provider used to read users
     *
     * @return TokenInterface|UsernamePasswordToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {

        try {
            // Load the user from the database. If the user does not exist, it cannot be
            // authenticated via LDAP
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException(self::ERROR_MESSAGE);
        }

        // check for LDAP based password
        $passwordValid = $this->checkLdapPassword($user->getUsername(), $token->getCredentials());

        if (!$passwordValid) {
            // Not an LDAP password, check for local password
            // We have to check the password on our own because symfony delivers the
            // password in plain text. The plain text is needed to authenticate against the
            // LDAP server. But in the database the passwords are encoded with bcrypt.
            // The password from the database is found in the user's password field, the
            // input in the credentials of the token.
            $passwordValid = password_verify($token->getCredentials(), $user->getPassword());
        }

        if ($passwordValid) {
            // user was successfully authenticated, either via LDAP or by the database
            // create a token
            return new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $providerKey,
                $user->getRoles()
            );
        }

        // Throw an error, authentication failed
        throw new AuthenticationException(self::ERROR_MESSAGE);
    }

    /**
     * Indicate whether this class is able to support tokens of the given type.
     *
     * @param TokenInterface $token the token to check support for
     * @param string $providerKey the provider
     *
     * @return bool true if tokens are supported, otherwise false
     */
    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof UsernamePasswordToken
        && $token->getProviderKey() === $providerKey;
    }

    /**
     * Create a new token.
     *
     * @param Request $request the Request
     * @param string $username the name of the user
     * @param string $password the password of the user
     * @param string $providerKey the provider used
     *
     * @return UsernamePasswordToken a new token
     */
    public function createToken(Request $request, $username, $password, $providerKey) {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    /**
     * Check a user against the LDAP.
     *
     * @param string $userName the user's name
     * @param string $clearTextPassword the user's password in clear text
     *
     * @return bool true if the user could be authenticated against the LDAP server
     */
    private function checkLdapPassword($userName, $clearTextPassword) {

        // connect to server and set options
        $con = ldap_connect($this->ldapUrl);
        ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!@ldap_bind($con, "$this->ldapUserPrefix=$userName,$this->ldapBaseDSN", $clearTextPassword)) {
            // could not bind with the user against the server. This indicated that either the
            // username or the password is wrong
            // LDAP errors, i.e. not being able to reach the LDAP server will also cause this
            // path to be taken
            $correct = false;
        }
        else {
            // was able to bind against the LDAP, user provided a valid username and password
            // combination
            $correct = true;
        }

        ldap_close($con);

        return $correct;
    }
}
