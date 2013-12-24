<?php

namespace Ms\OauthBundle\Entity;

use Ms\OauthBundle\Component\Authorization\AuthorizationResponseType;
use Ms\OauthBundle\Entity\Client;

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
     * @var Client 
     */
    private $client;

    /**
     * @var integer
     */
    private $id;

    /**
     *
     * @var string 
     */
    private $redirectionUri;

    /**
     *
     * @var string 
     */
    private $responseType;

    /**
     *
     * @var string 
     */
    private $state;

    /**
     * Get authorizationCode
     *
     * @return string 
     */
    public function getAuthorizationCode() {
        return $this->authorizationCode;
    }

    /**
     * Get client
     *
     * @return Client 
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get redirectionUri
     *
     * @return string 
     */
    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    /**
     * Get responseType
     *
     * @return string 
     */
    public function getResponseType() {
        return $this->responseType;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set authorizationCode
     *
     * @param string $authorizationCode
     * @return AuthorizationCodeProfile
     */
    public function setAuthorizationCode($authorizationCode) {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * Set client
     *
     * @param Client $client
     * @return AuthorizationCodeProfile
     */
    public function setClient(Client $client = null) {
        $this->client = $client;

        return $this;
    }

    /**
     * Set redirectionUri
     *
     * @param string $redirectionUri
     * @return AuthorizationCodeProfile
     */
    public function setRedirectionUri($redirectionUri) {
        $this->redirectionUri = $redirectionUri;

        return $this;
    }

    /**
     * Set responseType
     *
     * @param string $responseType
     * @return AuthorizationCodeProfile
     * @throws \InvalidArgumentException if `$responseType` is not declared in the
     * `AuthorizationResponseType` class.
     */
    public function setResponseType($responseType) {
        if (!in_array($responseType, AuthorizationResponseType::getValues())) {
            throw new \InvalidArgumentException('Invalid response type: ' . $responseType);
        }
        $this->responseType = $responseType;

        return $this;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return AuthorizationCodeProfile
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }
}