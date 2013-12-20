<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Description of AuthorizationService
 *
 * @author Marios
 */
class AuthorizationService implements AuthorizationServiceInterface {
    
    /**
     * @var SecureRandom
     */
    private $randomStringGenerator;
    
    /**
     * 
     * @param SecureRandom $rsg
     */
    function __construct(SecureRandom $rsg) {
        if ($rsg === null) {
            throw new InvalidArgumentException('No SecureRandom specified.');
        }
        
        $this->randomStringGenerator = $rsg;
    }

    /**
     * @inheritdoc
     */
    public function createAuthorizationCode() {
        $rnd = $this->randomStringGenerator->nextBytes(128);
        $rnd = base64_encode($rnd);
        
        return trim($rnd, '=');
    }   
}

