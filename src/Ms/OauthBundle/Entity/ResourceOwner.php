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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $resources;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $resourceGroups;

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


    /**
     * Add resources
     *
     * @param \Ms\OauthBundle\Entity\Resource $resources
     * @return ResourceOwner
     */
    public function addResource(\Ms\OauthBundle\Entity\Resource $resources)
    {
        $this->resources[] = $resources;
    
        return $this;
    }

    /**
     * Remove resources
     *
     * @param \Ms\OauthBundle\Entity\Resource $resources
     */
    public function removeResource(\Ms\OauthBundle\Entity\Resource $resources)
    {
        $this->resources->removeElement($resources);
    }

    /**
     * Get resources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Add resourceGroups
     *
     * @param \Ms\OauthBundle\Entity\ResourceGroup $resourceGroups
     * @return ResourceOwner
     */
    public function addResourceGroup(\Ms\OauthBundle\Entity\ResourceGroup $resourceGroups)
    {
        $this->resourceGroups[] = $resourceGroups;
    
        return $this;
    }

    /**
     * Remove resourceGroups
     *
     * @param \Ms\OauthBundle\Entity\ResourceGroup $resourceGroups
     */
    public function removeResourceGroup(\Ms\OauthBundle\Entity\ResourceGroup $resourceGroups)
    {
        $this->resourceGroups->removeElement($resourceGroups);
    }

    /**
     * Get resourceGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResourceGroups()
    {
        return $this->resourceGroups;
    }
}