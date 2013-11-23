<?php

namespace Ms\OauthBundle\Component\Authentication;

use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;

/**
 * Description of MsOauthPasswordGenerator
 *
 * @author user
 */
class MsOauthPasswordGenerator implements PasswordGeneratorInterface {
    
    /**
     *
     * @var SecureRandom 
     */
    private $rng;
    
    /**
     *
     * @var PasswordEncoderInterface 
     */
    private $encoder;
    
    /**
     * 
     */
    public function __construct() {
        $this->rng = new SecureRandom();
        $this->encoder = new Pbkdf2PasswordEncoder('sha512', true, 2000, 20);
    }

    /**
     * @inheritdoc
     */
    public function createPassword($salt) {
        $random = $this->rng->nextBytes(20);
        
        return $this->encoder->encodePassword($random, $salt);
    }

    /**
     * @inheritdoc
     */
    public function createSalt() {
        return base64_encode($this->rng->nextBytes(10));
    }

}
