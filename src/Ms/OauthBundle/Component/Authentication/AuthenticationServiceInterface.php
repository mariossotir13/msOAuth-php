<?php

namespace Ms\OauthBundle\Component\Authentication;

use Ms\OauthBundle\Entity\Client;

/**
 *
 * @author user
 */
interface AuthenticationServiceInterface {
    
    /**
     * Δημιουργεί ένα νέο Αναγνωριστικό Πελάτη.
     * 
     * @param Client $client Ο Πελάτης του οποίου το Αναγνωριστικό δημιουργεί
     * αυτή η μέθοδος.
     * @return string Το Αναγνωριστικό του `$client`.
     */
    public function createClientId(Client $client); 
    
    /**
     * Δημιουργεί ένα νέο Μυστικό Πελάτη.
     * 
     * @return string Το Μυστικό Πελάτη.
     */
    public function createPassword();
    
    /**
     * Δημιουργεί ένα κρυπτογραφικό *αλάτι*.
     * 
     * Το *αλάτι* χρησιμοποιείται για την ενίσχυση της ασφάλειας αποθήκευσης των
     * Μυστικών Πελάτη.
     * 
     * @return string Το *αλάτι*.
     */
    public function createPasswordSalt();
    
    /**
     * Αποκρυπτογραφεί ένα Μυστικό Πελάτη.
     * 
     * @param string $password Το Μυστικό Πελάτη το οποίο πρόκειται να αποκρυπτογραφηθεί.
     * @param string $key Το κλειδί αποκρυπτογράφησης.
     * @return string Το αποκρυπτογραφημένο Μυστικό Πελάτη.
     */
    public function decryptPassword($password, $key);
    
    /**
     * Κρυπτογραφεί ένα Μυστικό Πελάτη.
     * 
     * @param string $password Το Μυστικό Πελάτη το οποίο πρόκειται να κρυπτογραφηθεί.
     * @param string $key Το κλειδί κρυπτογράφησης.
     * @return string Το κρυπτογραφημένο Μυστικό Πελάτη.
     */
    public function encryptPassword($password, $key);
    
    /**
     * Κατακερματίζει ένα Μυστικό Πελάτη χρησιμοποιώντας ένα κρυπτογραφικό *αλάτι*.
     * 
     * @param string $password Το Μυστικό Πελάτη.
     * @param string $salt To *αλάτι*.
     * @return string Το `$password` συνδυασμένο με το `$salt` μέσω μίας συνάρτησης
     * κατακερματισμού.
     */
    public function hashPassword($password, $salt);
}
