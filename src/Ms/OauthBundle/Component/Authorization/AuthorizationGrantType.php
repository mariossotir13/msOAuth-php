<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 * Description of AuthorizationGrantType
 *
 * @author Marios
 */
class AuthorizationGrantType {
    
    /**
     * @var string
     */
    const AUTHORIZATION_CODE = 'authorization_code';
    
    /**
     * 
     * @return string[]
     */
    public static function getValues() {
        return array(static::AUTHORIZATION_CODE);
    }
}
