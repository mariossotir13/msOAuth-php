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

    /**
     * Set id
     *
     * @param string $id
     * @return User
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

}
