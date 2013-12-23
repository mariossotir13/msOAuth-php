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
    public function createPassword() {
        return base64_encode($this->rng->nextBytes(20));
    }

    /**
     * @inheritdoc
     */
    public function createSalt() {
        return base64_encode($this->rng->nextBytes(10));
    }

    /**
     * @inheritdoc
     */
    public function hashPassword($password, $salt) {
        return $this->encoder->encodePassword($password, $salt);
    }
}
