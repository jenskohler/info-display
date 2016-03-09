<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Class ApiKeyAuthenticator
 * @package HSMA\InfoDisplay\Security
 *
 * Authentication handler that allows to access the application using API-Keys instead of username
 * and password. This kind of authentication is useful for programmatic access where providing
 * credentials to a form and storing session cookies would be quiet cumbersome.
 */
class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface {

    /**
     * Error message issued in case of problems.
     */
    const ERROR_MESSAGE = 'Ungültiger oder fehlender API-Key.';

    /** @var ApiKeyUserProvider the User provider */
    protected $userProvider;

    /**
     * Create a new instance.
     *
     * @param ApiKeyUserProvider $userProvider the User provider
     */
    public function __construct(ApiKeyUserProvider $userProvider) {
        $this->userProvider = $userProvider;
    }

    /**
     * Authenticate a given token.
     *
     * @param TokenInterface $token the token
     * @param UserProviderInterface $userProvider the user provider
     * @param string $providerKey the key for this provider
     *
     * @return PreAuthenticatedToken the created token
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {

        // get the key and retrieve the user name for the key from the provider
        $key = $token->getCredentials();
        $userName = $this->userProvider->getUserNameForKey($key);

        if (!$userName) {
            // no user for the API key found, stop authentication here
            throw new AuthenticationException(self::ERROR_MESSAGE);
        }

        // retrieve the user and create a token
        $user = $this->userProvider->loadUserByUsername($userName);

        return new PreAuthenticatedToken(
            $user,
            $key,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * Indicate whether this class supports the given token.
     *
     * @param TokenInterface $token the token to check
     * @param string $providerKey name of the provider
     *
     * @return bool true if token is supported
     */
    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * Create a token based on the key. The token is not checked but just created
     * with the user name "anon.". Checking the token ist the task of the authenticateToken
     * method.
     *
     * @param Request $request the current request
     * @param string $providerKey the name of this provider
     *
     * @return PreAuthenticatedToken the created token
     */
    public function createToken(Request $request, $providerKey) {

        // read key from the query parameter key
        $key = $request->query->get('key');

        if (!$key) {
            throw new BadCredentialsException('No API key found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $key,
            $providerKey
        );
    }

    /**
     * Handle authentication failures.
     *
     * @param Request $request the request
     * @param AuthenticationException $exception the exception
     *
     * @return Response the response to be sent back to the browser
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return new Response("Authentication Failed.", 403);
    }
}

