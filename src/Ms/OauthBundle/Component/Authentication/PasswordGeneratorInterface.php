<?php

namespace Ms\OauthBundle\Component\Authentication;

/**
 * Δημιουργεί τον κωδικό και το [αλάτι][1] του κωδικού του χρήστη.
 * 
 * 
 * [1]: http://en.wikipedia.org/wiki/Salt_(cryptography)
 *      "Salt (cryptography) in Wikipedia"
 * 
 * @author user
 */
interface PasswordGeneratorInterface {
    
    /**
     * Δημιουργεί το αλάτι του κωδικού του χρήστη.
     * 
     * @return string. To αλάτι του κωδικού του χρήστη.
     */
    public function createSalt(); 
    
    /**
     * Δημιουργεί τον κωδικό του χρήστη.
     * 
     * @return string. Τον κωδικό του χρήστη.
     */
    public function hashPassword($salt);
    
}
