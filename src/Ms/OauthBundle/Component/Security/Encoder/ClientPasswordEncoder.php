<?php

namespace Ms\OauthBundle\Component\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Ms\OauthBundle\Component\Authentication\AuthenticationServiceInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Description of ClientPasswordEncoder
 *
 * @author Marios
 */
class ClientPasswordEncoder extends BasePasswordEncoder {
    
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
    public function encodePassword($raw, $salt) {
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }
        
        return $this->authenticationService->encryptPassword($raw, $this->encryptionKey);
    }

    /**
     * 
     * @inheritdoc
     */
    public function isPasswordValid($encoded, $raw, $salt) {
        return !$this->isPasswordTooLong($raw)
            && $this->comparePasswords(
                $raw, 
                $this->authenticationService->decryptPassword($encoded, $this->encryptionKey)
            );
    }
}
