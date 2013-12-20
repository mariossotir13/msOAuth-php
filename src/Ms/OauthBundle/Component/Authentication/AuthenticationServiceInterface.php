<?php

namespace Ms\OauthBundle\Component\Authentication;

use Ms\OauthBundle\Entity\Client;

/**
 *
 * @author user
 */
interface AuthenticationServiceInterface {
    
    /**
     * Creates an id for a new client.
     * 
     * @param Client $client The Client whose id this method generates.
     * @return string The id of the `$client`.
     */
    public function createClientId(Client $client); 
    
    /**
     * Δημιουργεί το αλάτι του κωδικού του χρήστη.
     * 
     * @return string Το αλάτι του κωδικού του χρήστη.
     */
    public function createPasswordSalt();
    
    /**
     * Δημιουργεί το hash του κωδικού του χρήστη.
     * 
     * @param string $salt To αλάτι του κωδικού του χρήστη.
     * @return string Τον κωδικό του χρήστη.
     */
    public function hashPassword($salt);
}
