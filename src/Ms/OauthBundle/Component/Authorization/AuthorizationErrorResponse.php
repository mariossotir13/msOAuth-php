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
     * @var string
     */
    private $error;
    
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
     * @param string $error
     * @throws \InvalidArgumentException εάν το όρισμα `$error` είτε είναι `null`
     * είτε έχει τιμή η οποία δεν έχει δηλωθεί στην κλάση `AuthorizationError`.
     */
    function __construct($error) {
        $this->setError($error);
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
     * @param string $errorDescription
     * @throws \InvalidArgumentException εάν το όρισμα `$errorDescription` είναι
     * `null`.
     */
    public function setErrorDescription($errorDescription) {
        if ($errorDescription === null) {
            throw new \InvalidArgumentException('No error description was specified.');
        }
        $this->errorDescription = $errorDescription;
    }
    
    /**
     * 
     * @param string $errorUri
     * @throws \InvalidArgumentException εάν το όρισμα `$errorUri` είναι `null`.
     */
    public function setErrorUri($errorUri) {
        if ($errorUri === null) {
            throw new \InvalidArgumentException('No error URI was specified.');
        }
        $this->errorUri = $errorUri;
    }

    /**
     * 
     * @param string $state
     * @throws \InvalidArgumentException εάν το όρισμα `$state` είναι `null`.
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
    public function toQueryString() {
        $queryStringArr = array(static::$ERROR => $this->error);
        if (!empty($this->errorDescription)) {
            $queryStringArr[static::$ERROR_DESCRIPTION] = $this->errorDescription;
        }
        if (!empty($this->errorUri)) {
            $queryStringArr[static::$ERROR_URI] = $this->errorUri;
        }
        if (!empty($this->state)) {
            $queryStringArr[static::$STATE] = $this->state;
        }
        
        return http_build_query($queryStringArr, '', '&');
    }
    
    /**
     * 
     * @param string $error
     * @return void
     * @throws \InvalidArgumentException εάν το όρισμα `$error` είτε είναι `null`
     * είτε έχει τιμή η οποία δεν έχει δηλωθεί στην κλάση `AuthorizationError`.
     */
    protected function setError($error) {
        if ($error === null) {
            throw new \InvalidArgumentException('No error was specified.');
        }
        if (!in_array($error, AuthorizationError::getValues())) {
            throw new \InvalidArgumentException('Invalid error: ' . $error);
        }
        $this->error = $error;
        
        $this->setRedirected($this->error !== AuthorizationError::REDIRECTION_URI);
    }

    /**
     * 
     * @param boolean $redirected
     * @throws \InvalidArgumentException εάν το όρισμα `$redirected` δεν είναι τύπου
     * `boolean`.
     */
    protected function setRedirected($redirected) {
        if (!is_bool($redirected)) {
            throw new \InvalidArgumentException('The value specified is not boolean');
        }
        $this->redirected = $redirected;
    }
}