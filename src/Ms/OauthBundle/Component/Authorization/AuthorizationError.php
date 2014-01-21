<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 * Description of AuthorizationError
 *
 * @author Marios
 */
class AuthorizationError {
    
    /**#@+
     * @var string
     */
    const ACCESS_DENIED = 'access_denied';
    const INVALID_CLIENT = 'invalid_client';
    const INVALID_GRANT = 'invalid_grant';
    const INVALID_REQUEST = 'invalid_request';
    const INVALID_SCOPE = 'invalid_scope';
    const REDIRECTION_URI = 'redirect_uri';
    const UNSUPPORTED_GRANT_TYPE = 'unsupported_grant_type';
    const UNSUPPORTED_RESPONSE_TYPE = 'unsupported_response_type';
    /**#@-*/
    
    /**
     * 
     * @return array
     */
    public static function getValues() {
        return array(
            static::ACCESS_DENIED,
            static::INVALID_CLIENT,
            static::INVALID_GRANT,
            static::INVALID_REQUEST,
            static::INVALID_SCOPE,
            static::REDIRECTION_URI,
            static::UNSUPPORTED_GRANT_TYPE,
            static::UNSUPPORTED_RESPONSE_TYPE
        );
    }
}