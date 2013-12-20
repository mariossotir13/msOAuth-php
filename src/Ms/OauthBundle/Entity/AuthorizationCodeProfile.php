<?php

namespace Ms\OauthBundle\Entity;

/**
 * Description of AuthorizationCodeProfile
 *
 * @author Marios
 */
class AuthorizationCodeProfile {
    
    /**
     *
     * @var string 
     */
    private $authorizationCode;
    
    /**
     *
     * @var string 
     */
    private $responseType;
    
    /**
     *
     * @var string 
     */
    private $clientId;
    
    /**
     *
     * @var string 
     */
    private $redirectionUri;
  
    /**
     *
     * @var string 
     */
    private $scopes;
    
    /**
     *
     * @var string 
     */
    private $state;
    public function getAuthorizationCode() {
        return $this->authorizationCode;
    }

    public function setAuthorizationCode($authorizationCode) {
        $this->authorizationCode = $authorizationCode;
    }

    public function getResponseType() {
        return $this->responseType;
    }

    public function setResponseType($responseType) {
        $this->responseType = $responseType;
    }

    public function getClientId() {
        return $this->clientId;
    }

    public function setClientId($clientId) {
        $this->clientId = $clientId;
    }

    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;
    }

    public function getScopes() {
        return $this->scopes;
    }

    public function setScopes($scopes) {
        $this->scopes = $scopes;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }


}


