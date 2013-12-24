<?php

namespace Ms\OauthBundle\Entity;

use Ms\OauthBundle\Entity\User;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;
use Doctrine\Common\Collections\ArrayCollection;

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
     * Τα προφίλ των Κωδικών Εξουσιοδότησης τους οποίους έχει εκδώσει το σύστημα
     * για τον παρόντα Πελάτη.
     *
     * @var AuthorizationCodeProfile[]
     */
    protected $authorizationCodeProfiles;

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
     * Δημιουργεί ένα νέο στιγμιότυπο της κλάσης Client.
     */
    function __construct() {
        $this->authorizationCodeProfiles = new ArrayCollection();
    }

    /**
     * Add authorizationCodeProfiles
     *
     * @param AuthorizationCodeProfile $authorizationCodeProfile
     * @return Client
     */
    public function addAuthorizationCodeProfile(AuthorizationCodeProfile $authorizationCodeProfile) {
        $this->authorizationCodeProfiles[] = $authorizationCodeProfile;

        return $this;
    }

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

    /**
     * Remove authorizationCodeProfiles
     *
     * @param AuthorizationCodeProfile $authorizationCodeProfile
     */
    public function removeAuthorizationCodeProfile(AuthorizationCodeProfile $authorizationCodeProfile) {
        $this->authorizationCodeProfiles->removeElement($authorizationCodeProfile);
    }

    /**
     * Get authorizationCodeProfiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuthorizationCodeProfiles() {
        return $this->authorizationCodeProfiles;
    }

}
