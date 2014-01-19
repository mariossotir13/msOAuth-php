<?php

namespace Ms\OauthBundle\Component\Security;

use Ms\OauthBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Util\StringUtils;
use Ms\OauthBundle\Component\Authentication\AuthenticationServiceInterface;

/**
 * Description of MsOauthClientAuthentication
 *
 * @author Marios
 */
class MsOauthClientAuthenticator extends MsOauthAuthenticator {
    
    /**
     *
     * @var AuthenticationServiceInterface
     */
    protected $authenticationService;
    
    /**
     *
     * @var string
     */
    protected $encryptionKey;
    
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
     * 
     * @inheritdoc
     */
    protected function validateUser(User $user, TokenInterface $token) {
        $decryptedPassword = $this->authenticationService->decryptPassword(
            $user->getPassword(),
            $this->encryptionKey
        );
        
        return StringUtils::equals($decryptedPassword, $token->getCredentials());
    }
}
