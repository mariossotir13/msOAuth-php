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
     * @ var string.
     */
    protected $appTitle;
    /**
     * To URI στο οποίο ανακατευθύνεται ο ιδιοκτήτης του πόρου μετά από μια 
     * επιτυχημένη εγγραφή.
     * 
     * Παρέχεται από τον πελάτη κατά την εγγραφή του.
     * 
     * @ var string.
     */
    protected $redirectionUri;
    /**
     * Ο τύπος του Πελάτη.
     * 
     * @ var int.
     */
    protected $clientType;
}
