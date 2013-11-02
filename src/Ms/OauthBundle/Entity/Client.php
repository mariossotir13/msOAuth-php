<?php

namespace Ms\OauthBundle\Entity;

use Ms\OauthBundle\Entity\User;

/**
 * Μία εφαρμογή η οποία κάνει αιτήσεις για προστατευμένο πόρο εκ μέρους του
 * ιδιοκτήτη του πόρου και με την εξουσιοδότησή του.
 *
 * @author user
 */
class Client extends User {

    /**
     * Ο τίτλος της εφαρμογής.
     * 
     * @var string
     */
    protected $appTitle;

    /**
     * To URI στο οποίο ανακατευθύνεται ο ιδιοκτήτης του πόρου μετά από μια 
     * επιτυχημένη εγγραφή.
     * 
     * Παρέχεται από τον πελάτη κατά την εγγραφή του.
     * 
     * @var string
     */
    protected $redirectionUri;

    /**
     * Ο τύπος του Πελάτη.
     * 
     * @var int
     */
    protected $clientType;

    /**
     * Set appTitle
     *
     * @param string $appTitle
     * @return Client
     */
    public function setAppTitle($appTitle) {
        $this->appTitle = $appTitle;

        return $this;
    }

    /**
     * Get appTitle
     *
     * @return string 
     */
    public function getAppTitle() {
        return $this->appTitle;
    }

    /**
     * Set redirectionUri
     *
     * @param string $redirectionUri
     * @return Client
     */
    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;

        return $this;
    }
    
    /**
     * Get redirectionUri
     *
     * @return string 
     */
    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    /**
     * Set clientType
     *
     * @param integer $clientType
     * @return Client
     */
    public function setClientType($clientType) {
        $this->clientType = $clientType;

        return $this;
    }

    /**
     * Get clientType
     *
     * @return integer 
     */
    public function getClientType() {
        return $this->clientType;
    }

}
