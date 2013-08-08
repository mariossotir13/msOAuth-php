<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorResponse
 *
 * @author Marios
 */
class ErrorResponse {

    private $error;
    private $description;
    private $uri;
    private $state;

    function __construct() {
        
    }

    public function getError() {
        return $this->error;
    }

    public function setError($error) {
        $this->error = $error;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getUri() {
        return $this->uri;
    }

    public function setUri($uri) {
        $this->uri = $uri;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

}

?>
