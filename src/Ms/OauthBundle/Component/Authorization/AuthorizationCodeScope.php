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
     * @return array
     */
    public static function getScopes() {
        return array(
            BASIC,
            FULL
        );
    }
}