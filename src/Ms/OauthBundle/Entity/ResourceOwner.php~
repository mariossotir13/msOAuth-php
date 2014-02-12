<?php

namespace Ms\OauthBundle\Entity;

use Ms\OauthBundle\Entity\User;
use Ms\OauthBundle\Entity\AuthorizationCodeProfile;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ResourceOwner
 *
 * @author Marios
 */
class ResourceOwner extends User {

    /**
     * @var Collection
     */
    private $authorizationCodeProfiles;

    /**
     * Constructor
     */
    public function __construct() {
        $this->authorizationCodeProfiles = new ArrayCollection();
    }

    /**
     * Add authorizationCodeProfiles
     *
     * @param AuthorizationCodeProfile $authorizationCodeProfiles
     * @return ResourceOwner
     */
    public function addAuthorizationCodeProfile(AuthorizationCodeProfile $authorizationCodeProfiles) {
        $this->authorizationCodeProfiles[] = $authorizationCodeProfiles;

        return $this;
    }

    /**
     * Remove authorizationCodeProfiles
     *
     * @param AuthorizationCodeProfile $authorizationCodeProfiles
     */
    public function removeAuthorizationCodeProfile(AuthorizationCodeProfile $authorizationCodeProfiles) {
        $this->authorizationCodeProfiles->removeElement($authorizationCodeProfiles);
    }

    /**
     * Get authorizationCodeProfiles
     *
     * @return Collection 
     */
    public function getAuthorizationCodeProfiles() {
        return $this->authorizationCodeProfiles;
    }

}
