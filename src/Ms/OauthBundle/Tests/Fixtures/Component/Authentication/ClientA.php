<?php

namespace Ms\OauthBundle\Tests\Fixtures\Component\Authentication;

use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\ClientType;

/**
 * Ένας Πελάτης με προϋπολογισμένες τιμές ιδιοτήτων.
 * 
 * Το Αναγνωριστικό Πελάτη υπολογίζεται έξω από το σύστημα. Ο αλγόριθμος δημιουργίας
 * του ακολουθεί την περιγραφή του Εγγράφου Αρχιτεκτονικής.
 *
 * @author Marios
 */
class ClientA extends Client {

    /**
     * Δημιουργεί ένα νέο ClientA στιγμιότυπο.
     */
    function __construct() {
        parent::__construct();
        
        $this->appTitle = 'AppTitle A';
        $this->clientType = ClientType::TYPE_CONFIDENTIAL;
        $this->email = 'client_a@email.com';
        $this->id = 'ZEE4R451IIT2TRJtB5qPc9LFGI6GnPoC2W62/+kGD2U';
        $this->redirectionUri = 'http://client-a.com';
    }
}
