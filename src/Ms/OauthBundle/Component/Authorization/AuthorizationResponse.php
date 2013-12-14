<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 * Description of AuthorizationResponse
 *
 * @author Marios
 */
class AuthorizationResponse {
    
    /**#@+
     * @var string
     */
    private static $OAUTH_CODE = 'code';
    private static $STATE = 'state';
    /**#@-*/
    
    /**
     *
     * @var string
     */
    private $oauthCode = '';
    
    /**
     *
     * @var string
     */
    private $redirectionUri = '';
    
    /**
     *
     * @var string
     */
    private $state = '';
    
    /**
     * 
     * @param string $authorizationCode Ο κωδικός εξουσιοδότησης.
     * @throws \InvalidArgumentException εάν η παράμετρος `$authorizationCode`
     * είναι *null*.
     */
    public function __construct($authorizationCode) {
        if ($authorizationCode === null) {
            throw new \InvalidArgumentException('No authorization code was specified.');
        }
        $this->oauthCode = $authorizationCode;
    }
    
    /**
     * 
     * @return string
     */
    public function getOauthCode() {
        return $this->oauthCode;
    }

    /**
     * 
     * @return string
     */
    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    /**
     * 
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    /**
     * 
     * @param string $redirectionUri
     * @throws \InvalidArgumentException
     */
    public function setRedirectionUri($redirectionUri) {
        if ($redirectionUri === null) {
            throw new \InvalidArgumentException('No redirection URI was specified.');
        }
        $this->redirectionUri = $redirectionUri;
    }

    /**
     * 
     * @param string $state
     * @throws \InvalidArgumentException
     */
    public function setState($state) {
        if ($state === null) {
            throw new \InvalidArgumentException('No state was specified.');
        }
        $this->state = $state;
    }

    /**
     * 
     * @return string
     */
    public function toUri() {
        $uri = static::$OAUTH_CODE . '=' . $this->oauthCode;
        $uri .= $this->state ? '&' . static::$STATE . '=' . $this->state : '';
        
        return urlencode($uri);
    }
}