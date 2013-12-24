<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 * Description of AuthorizationCodeScope
 *
 * @author Marios
 */
class AuthorizationCodeScope {
    
    /**
     * @var string
     */
    const BASIC = 'basic';
    
    /**
     * @var string
     */
    const FULL = 'full';
    
    /**
     * 
     * @return string[]
     */
    public static function getValues() {
        return array(
            static::BASIC,
            static::FULL
        );
    }
}