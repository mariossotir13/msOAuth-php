<?php

namespace Ms\OauthBundle\Component\Authorization;

/**
 * Description of AuthorizationErrorResponse
 *
 * @author Marios
 */
class AuthorizationErrorResponse {
    
    /**#@+
     * @var string
     */
    private static $ERROR = 'error';
    private static $ERROR_DESCRIPTION = 'error_description';
    private static $ERROR_URI = 'error_uri';
    private static $STATE = 'state';
    /**#@-*/
    
    /**
     *
     * @var AuthorizationError
     */
    private $error = '';
    
    /**
     *
     * @var string
     */
    private $errorDescription = '';
    
    /**
     *
     * @var string
     */
    private $errorUri = '';
    
    /**
     *
     * @var boolean
     */
    private $redirected = false;
    
    /**
     *
     * @var string
     */
    private $state = '';
    
    /**
     * 
     */
    function __construct() {
        
    }
    
    /**
     * 
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 
     * @return string
     */
    public function getErrorDescription() {
        return $this->errorDescription;
    }
    
    /**
     * 
     * @return string
     */
    public function getErrorUri() {
        return $this->errorUri;
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
     * @return boolean
     */
    function isRedirected() {
        return $this->redirected;
    }
    
    /**
     * 
     * @param AuthorizationError $error
     */
    public function setError(AuthorizationError $error) {
        if ($error === null) {
            throw new \InvalidArgumentException('No error was specified.');
        }
        $this->error = $error;
    }

    /**
     * 
     * @param string $errorDescription
     * @throws \InvalidArgumentException
     */
    public function setErrorDescription($errorDescription) {
        if ($errorDescription === null) {
            throw new \InvalidArgumentException('No error description was specified.');
        }
        $this->errorDescription = $errorDescription;
    }
    
    public function setErrorUri($errorUri) {
        if ($errorUri === null) {
            throw new \InvalidArgumentException('No error URI was specified.');
        }
        $this->errorUri = $errorUri;
    }

    /**
     * 
     * @param boolean $redirected
     * @throws \InvalidArgumentException
     */
    public function setRedirected($redirected) {
        if (!is_bool($redirected)) {
            throw new \InvalidArgumentException('The value specified is not boolean');
        }
        $this->redirected = $redirected;
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
     * @return string
     */
    public function toUri() {
        $uri = '';
        $uri .= $this->error ? '&' . static::$ERROR . '=' . $this->error : '';
        $uri .= $this->errorDescription ? '&' . static::$ERROR_DESCRIPTION . '=' . $this->errorDescription : '';
        $uri .= $this->errorUri ? '&' . static::$ERROR_URI . '=' . $this->errorUri : '';
        $uri .= $this->state ? '&' . static::$STATE . '=' . $this->state : '';
        
        return urlencode($uri);
    }
}