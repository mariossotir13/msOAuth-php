<?php

namespace Ms\OauthBundle\Entity;

use Ms\OauthBundle\Component\Authorization\AuthorizationResponseType;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\AccessTokenProfile;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of AuthorizationCodeProfile
 *
 * @author Marios
 */
class AuthorizationCodeProfile {

    /**
     *
     * @var int 
     */
    private static $EXPIRATION_TIME = 600;
    
    /**
     * @var AccessTokenProfile
     */
    private $accessTokenProfile;

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
     * @var \DateTime
     */
    private $expirationDate;

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
     * @var \Ms\OauthBundle\Entity\ResourceOwner
     */
    private $resourceOwner;

    /**
     *
     * @var string 
     */
    private $responseType;

    /**
     * @var Collection
     */
    private $scopes;

    /**
     *
     * @var string 
     */
    private $state;

    /**
     * Constructor
     */
    public function __construct() {
        $this->scopes = new ArrayCollection();
//        $this->setExpirationDate();
    }

    /**
     * Add scopes
     *
     * @param AuthorizationCodeScope $scopes
     * @return AuthorizationCodeProfile
     */
    public function addScope(AuthorizationCodeScope $scopes) {
        $this->scopes[] = $scopes;

        return $this;
    }

    /**
     * Get accessTokenProfile
     *
     * @return AccessTokenProfile 
     */
    public function getAccessTokenProfile() {
        return $this->accessTokenProfile;
    }

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
     * Get expirationDate
     *
     * @return \DateTime 
     */
    public function getExpirationDate() {
        return $this->expirationDate;
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
     * Get resourceOwner
     *
     * @return \Ms\OauthBundle\Entity\ResourceOwner 
     */
    public function getResourceOwner() {
        return $this->resourceOwner;
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
     * Get scopes
     *
     * @return Collection 
     */
    public function getScopes() {
        return $this->scopes;
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
     * Remove scopes
     *
     * @param AuthorizationCodeScope $scopes
     * @return void
     */
    public function removeScope(AuthorizationCodeScope $scopes) {
        $this->scopes->removeElement($scopes);
    }

    /**
     * Set accessTokenProfile
     *
     * @param AccessTokenProfile $accessTokenProfile
     * @return AuthorizationCodeProfile
     */
    public function setAccessTokenProfile(AccessTokenProfile $accessTokenProfile = null) {
        $this->accessTokenProfile = $accessTokenProfile;
    
        return $this;
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
     * 
     * @param \DateTime $expirationDate
     * @return AuthorizationCodeProfile
     * @throws \InvalidArgumentException εάν το όρισμα `$expirationDate` καταδεικνύει
     * μία ημερομηνία στο παρελθόν
     */
    public function setExpirationDate(\DateTime $expirationDate) {
        $now = new \DateTime('now', new \DateTimeZone("UTC"));
//        $this->expirationDate = $now->add(new \DateInterval('PT' . static::$EXPIRATION_TIME . 'S'));
        if ($now > $expirationDate) {
            throw new \InvalidArgumentException('Expiration date cannot be a time into the past.');
        }
        $this->expirationDate = $expirationDate;
        
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
     * Set resourceOwner
     *
     * @param \Ms\OauthBundle\Entity\ResourceOwner $resourceOwner
     * @return AuthorizationCodeProfile
     */
    public function setResourceOwner(\Ms\OauthBundle\Entity\ResourceOwner $resourceOwner = null) {
        $this->resourceOwner = $resourceOwner;
    
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