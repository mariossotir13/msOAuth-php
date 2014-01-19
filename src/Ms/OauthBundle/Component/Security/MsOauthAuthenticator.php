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

/**
 * Description of MsOauthAuthenticator
 *
 * @author Marios
 */
class MsOauthAuthenticator implements SimpleFormAuthenticatorInterface {

    /**
     *
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;
    
    /**
     *
     * @var string
     */
    private $encryptionKey;

    /**
     * 
     * @param AuthenticationServiceInterface $authenticationService
     * @param string $encryptionKey
     * @throws \InvalidArgumentException εάν κάποιο από τα ορίσματα είναι `null`.
     */
    function __construct(AuthenticationServiceInterface $authenticationService, $encryptionKey) {
        if ($authenticationService === null) {
            throw new \InvalidArgumentException('Authentication service cannot be null.');
        }
        if ($encryptionKey === null) {
            throw new \InvalidArgumentException('Encryption key cannot be null.');
        }
        $this->authenticationService = $authenticationService;
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * @inheritdoc
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        /* @var $user \Ms\OauthBundle\Entity\Client */
        $user = null;
        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $ex) {
            throw new AuthenticationException('Invalid username or password.');
        }
        
        $decryptedPassword = $this->authenticationService->decryptPassword(
            $user->getPassword(),
            $this->encryptionKey
        );
        $passwordValid = StringUtils::equals($decryptedPassword, $token->getCredentials());
        if (!$passwordValid) {
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
}
