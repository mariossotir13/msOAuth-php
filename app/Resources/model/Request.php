<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Request
 *
 * @author Marios
 */
class Request {

    private $redirectionUri;
    private $state;
    private $responseType;

    function __construct() {
        
    }

    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getResponseType() {
        return $this->responseType;
    }

    public function setResponseType($responseType) {
        $this->responseType = $responseType;
    }

}

?>
