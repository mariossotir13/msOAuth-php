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
    const ERROR = 'error';
    const ERROR_DESCRIPTION = 'error_description';
    const ERROR_URI = 'error_uri';
    const STATE = 'state';
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
     * @return void
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
     * @return void
     * @throws \InvalidArgumentException εάν το όρισμα `$errorUri` είναι `null`.
     */
    public function setErrorUri($errorUri) {
        if ($errorUri === null) {
            throw new \InvalidArgumentException('No error URI was specified.');
        }
        $this->errorUri = $errorUri;
    }

    /**
     * Θέτει την *Τοπική Κατάσταση* την οποία έστειλε ο *Πελάτης* με την *Αίτηση
     * Εξουσιοδότησης*.
     * 
     * ### Προϋποθέσεις ###
     * 
     * Αξίζει να σημειωθεί ότι το όρισμα `$state` δεν ελέγχεται για `null`. Αυτό
     * συμβαίνει επειδή το όρισμα αυτό αναμένεται να λάβει τιμή από την Αίτηση
     * Εξουσιοδότησης. Η Αίτηση Εξουσιοδότησης επικυρώνεται από την Υπηρεσία
     * Επικύρωσης. Το όρισμα `$state` ελέγχεται κατά τη διάρκεια αυτής της επικύρωσης.
     * 
     * @param string $state
     * @return void
     */
    public function setState($state) {
//        if ($state === null) {
//            throw new \InvalidArgumentException('No state was specified.');
//        }
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function toQueryString() {
        $queryStringArr = array(self::ERROR => $this->error);
        if (!empty($this->errorDescription)) {
            $queryStringArr[self::ERROR_DESCRIPTION] = $this->errorDescription;
        }
        if (!empty($this->errorUri)) {
            $queryStringArr[self::ERROR_URI] = $this->errorUri;
        }
        if (!empty($this->state)) {
            $queryStringArr[self::STATE] = $this->state;
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