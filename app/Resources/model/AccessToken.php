<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccessToken
 *
 * @author Marios
 */
class AccessToken extends Token {

    private $expiresIn;
    private $type;

    function __construct() {
        parent::__construct();
    }

    public function getExpiresIn() {
        return $this->expiresIn;
    }

    public function setExpiresIn($expiresIn) {
        $this->expiresIn = $expiresIn;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

}

?>
