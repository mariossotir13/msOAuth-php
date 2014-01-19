<?php

namespace Ms\OauthBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Περιέχει κοινά στοιχεία και συμπεριφορές των χρηστών του συστήματος.
 * 
 * Οι χρήστες του συστήματος είναι είτε ιδιοκτήτες κάποιου πόρου είτε κάποιοι πελάτες.
 *
 * @package Ms\OauthBundle\Entity
 * @author user
 */
class User implements UserInterface {
    
    /**
     * @var string
     */
    const ROLE_USER = 'ROLE_MS_OAUTH_BUNDLE_USER';

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
     * Το αλάτι του password του χρήστη.
     * 
     * @var string
     */
    protected $salt;

    /**
     * @inheritdoc
     */
    public function eraseCredentials() {
        // TODO: Implement eraseCredentials.
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
     * Get id
     *
     * @return string 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getRoles() {
        return array(new Role(self::ROLE_USER));
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * 
     * @return string
     * @see #getId()
     */
    public function getUsername() {
        return $this->getId();
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
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt) {
        $this->salt = $salt;

        return $this;
    }
}
