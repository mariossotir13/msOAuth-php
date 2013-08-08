<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccessTokenRequest
 *
 * @author Marios
 */
class AccessTokenRequest extends Request {

    private $grantType;
    private $code;
    private $redirectionUri;
    private $clientId;

    function __construct($grantType) {
        parent::__construct();
        $this->grantType = $grantType;
    }

    public function getGrantType() {
        return $this->grantType;
    }

    public function setGrantType($grantType) {
        $this->grantType = $grantType;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;
    }

    public function getClientId() {
        return $this->clientId;
    }

    public function setClientId($clientId) {
        $this->clientId = $clientId;
    }

}

?>
