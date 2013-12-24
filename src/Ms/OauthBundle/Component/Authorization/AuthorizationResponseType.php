<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 * Description of AuthorizationResponseType
 *
 * @author Marios
 */
class AuthorizationResponseType {
    
    /**
     * @var string
     */
    const CODE = 'code';
    
    /**
     * 
     * @return string[]
     */
    public static function getValues() {
        return array(static::CODE);
    }
}