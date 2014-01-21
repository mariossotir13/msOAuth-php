<?php

namespace Ms\OauthBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;

/**
 * Description of AccessTokenProfile
 *
 * @author Marios
 */
class AccessTokenProfile {
    
    /**
     * @var string
     */
    const ACCESS_TOKEN_TYPE_BEARER = 'bearer';

    /**
     *
     * @var int 
     */
    private static $EXPIRATION_TIME = 600;

    /**
     *
     * @var string 
     */
    private $accessToken;

    /**
     *
     * @var string 
     */
    private $accessTokenType;

    /**
     * @var AuthorizationCodeProfile
     */
    private $authorizationCodeProfile;

    /**
     * @var string
     */
    private $expirationDate;

    /**
     *
     * @var string 
     */
    private $grantType;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Collection
     */
    private $scopes;

    /**
     * 
     */
    function __construct() {
        $this->scopes = new ArrayCollection();
        $this->setExpirationDate();
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
     * Set accessToken
     *
     * @param string $accessToken
     * @return AccessTokenProfile
     */
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string 
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * Set accessTokenType
     *
     * @param string $accessTokenType
     * @return AccessTokenProfile
     */
    public function setAccessTokenType($accessTokenType) {
        $this->accessTokenType = $accessTokenType;

        return $this;
    }

    /**
     * Get accessTokenType
     *
     * @return string 
     */
    public function getAccessTokenType() {
        return $this->accessTokenType;
    }

    /**
     * Set grantType
     *
     * @param string $grantType
     * @return AccessTokenProfile
     */
    public function setGrantType($grantType) {
        $this->grantType = $grantType;

        return $this;
    }

    /**
     * Get grantType
     *
     * @return string 
     */
    public function getGrantType() {
        return $this->grantType;
    }

    /**
     * Set authorizatonCodeProfile
     *
     * @param \Ms\OauthBundle\Entity\AuthorizationCodeProfile $authorizationCodeProfile
     * @return AccessTokenProfile
     */
    public function setAuthorizationCodeProfile(\Ms\OauthBundle\Entity\AuthorizationCodeProfile $authorizationCodeProfile = null) {
        $this->authorizationCodeProfile = $authorizationCodeProfile;

        return $this;
    }

    /**
     * Get authorizatonCodeProfile
     *
     * @return \Ms\OauthBundle\Entity\AuthorizationCodeProfile 
     */
    public function getAuthorizationCodeProfile() {
        return $this->authorizationCodeProfile;
    }

    /**
     * Add scopes
     *
     * @param \Ms\OauthBundle\Entity\AuthorizationCodeScope $scopes
     * @return AccessTokenProfile
     */
    public function addScope(\Ms\OauthBundle\Entity\AuthorizationCodeScope $scopes) {
        $this->scopes[] = $scopes;

        return $this;
    }

    /**
     * Remove scopes
     *
     * @param \Ms\OauthBundle\Entity\AuthorizationCodeScope $scopes
     */
    public function removeScope(\Ms\OauthBundle\Entity\AuthorizationCodeScope $scopes) {
        $this->scopes->removeElement($scopes);
    }

    /**
     * Get scopes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getScopes() {
        return $this->scopes;
    }

    /**
     * @return void
     */
    protected function setExpirationDate() {
        $now = new \DateTime('now', new \DateTimeZone("UTC"));
        $this->expirationDate = $now->add(new \DateInterval('PT' . static::$EXPIRATION_TIME . 'S'));
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime 
     */
    public function getExpirationDate() {
        return $this->expirationDate;
    }

}