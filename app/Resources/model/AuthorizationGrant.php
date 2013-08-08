<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizationGrant
 *
 * @author Marios
 */
class AuthorizationGrant {
    private $code;
    private $state;
    private $expiresIn;
    
    function __construct() {
        
    }
    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }
    public function getExpiresIn() {
        return $this->expiresIn;
    }

    public function setExpiresIn($expiresIn) {
        $this->expiresIn = $expiresIn;
    }



}

?>
