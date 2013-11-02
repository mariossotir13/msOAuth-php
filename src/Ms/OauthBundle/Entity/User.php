<?php

namespace Ms\OauthBundle\Entity;

/**
 * Περιέχει κοινά στοιχεία και συμπεριφορές των χρηστών του συστήματος.
 * 
 * Οι χρήστες του συστήματος είναι είτε ιδιοκτήτες κάποιου πόρου είτε κάποιοι πελάτες.
 *
 * @package Ms\OauthBundle\Entity
 * @author user
 */
class User {
    
    /**
     * To id του χρήστη.
     * 
     * @var string
     */
   protected $id;
   /**
     * To email του χρήστη.
     * 
     * @var string
     */
   protected $email;
   /**
    * To password του χρήστη.
    * 
    * @var string
    */
   protected $password;
   
}
