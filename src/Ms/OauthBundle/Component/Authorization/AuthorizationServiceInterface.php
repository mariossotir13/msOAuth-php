<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 *
 * @author Marios
 */
interface AuthorizationServiceInterface {

    /**
     * @return string
     */
    public function createAccessToken();
    
    /**
     * @return string
     */
    public function createAuthorizationCode();
}

