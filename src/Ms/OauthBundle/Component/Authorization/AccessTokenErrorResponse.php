<?php

namespace Ms\OauthBundle\Component\Authorization;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AccessTokenErrorResponse
 *
 * @author Marios
 */
class AccessTokenErrorResponse extends Response {
    
    /**#@+
     * @var string
     */
    const ERROR = 'error';
    const ERROR_DESCRIPTION = 'error_description';
    const ERROR_URI = 'error_uri';
    /**#@-*/
    
    /**
     *
     * @var string
     */
    protected $error = '';
    
    /**
     *
     * @var string
     */
    protected $errorDescription = '';
    
    /**
     *
     * @var string
     */
    protected $errorUri = '';
    
    /**
     *
     * @var boolean
     */
    private $redirected = false;

    /**
     * 
     * @param string $error
     * @throws \InvalidArgumentException εάν το όρισμα `$error` είναι *κενό*.
     * @inheritdoc
     */
    function __construct($error) {
        if (empty($error)) {
            throw new \InvalidArgumentException('Error cannot be empty.');
        }
        parent::__construct();
        
        $this->setError($error);
        $this->setStatusCode(static::HTTP_BAD_REQUEST, 'Bad Request');
        $this->headers->set('Cache-Control', 'no-store');
        $this->headers->set('Pragma', 'no-cache');
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
     * @return boolean
     */
    function isRedirected() {
        return $this->redirected;
    }

    /**
     * 
     * @param string $errorDescription
     * @return void
     * @throws \InvalidArgumentException εάν το όρισμα `$errorDescription` είναι
     * `null`.
     */
    public function setErrorDescription($errorDescription) {
        if ($errorDescription === null) {
            throw new \InvalidArgumentException('Error description cannot be null.');
        }
        $this->errorDescription = $errorDescription;
    }
    
    /**
     * 
     * @param string $errorUri
     * @return void
     * @throws \InvalidArgumentException εάν το όρισμα `$errorUri` είναι `null`.
     */
    public function setErrorUri($errorUri) {
        if ($errorUri === null) {
            throw new \InvalidArgumentException('No error URI was specified.');
        }
        $this->errorUri = $errorUri;
        $this->headers->set('Location', $errorUri);
    }

    /**
     * @return AccessTokenErrorResponse
     */
    public function setUpAsJson() {
        $this->headers->set('Content-Type', 'application/json');
        
        $jsonArray = array(
            self::ERROR => $this->getError()
        );
        if ($this->errorDescription !== '') {
            $jsonArray[self::ERROR_DESCRIPTION] = $this->getErrorDescription();
        }
        if ($this->errorUri !== '') {
            $jsonArray[self::ERROR_URI] = $this->getErrorUri();
        }
        
        $this->setContent(json_encode($jsonArray));
        
        return $this;
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
     * @return void
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
