<?php

namespace Ms\OauthBundle\Component\Security;

use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Ms\OauthBundle\Component\Authentication\AuthenticationServiceInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Util\StringUtils;
use Ms\OauthBundle\Entity\User;

/**
 * Description of MsOauthAuthenticator
 *
 * @author Marios
 */
abstract class MsOauthAuthenticator implements SimpleFormAuthenticatorInterface {

    /**
     * @inheritdoc
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        /* @var $user \Ms\OauthBundle\Entity\User */
        $user = null;
        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $ex) {
            throw new AuthenticationException('Invalid username or password.');
        }
        
        $valid = $this->validateUser($user, $token);
        if (!$valid) {
            throw new AuthenticationException('Invalid username or password.');
        }
        
        return new UsernamePasswordToken(
            $user->getId(), 
            $user->getPassword(), 
            $providerKey, 
            $user->getRoles()
        );
    }

    /**
     * @inheritdoc
     */
    public function createToken(Request $request, $username, $password, $providerKey) {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    /**
     * @inheritdoc
     */
    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $providerKey;
    }
    
    /**
     * 
     */
    protected function __construct() {
    }
    
    /**
     * @param User $user
     * @param TokenInterface $token
     * @return bool
     */
    abstract protected function validateUser(User $user, TokenInterface $token);
}
