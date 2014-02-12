<?php

namespace Ms\OauthBundle\Entity;

/**
 * Description of ResourceGroup
 *
 * @author Marios
 */
class ResourceGroup {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Ms\OauthBundle\Entity\ResourceOwner
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $resources;

    /**
     * Constructor
     */
    public function __construct() {
        $this->resources = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return ResourceGroup
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Add resources
     *
     * @param \Ms\OauthBundle\Entity\Resource $resources
     * @return ResourceGroup
     */
    public function addResource(\Ms\OauthBundle\Entity\Resource $resources) {
        $this->resources[] = $resources;

        return $this;
    }

    /**
     * Remove resources
     *
     * @param \Ms\OauthBundle\Entity\Resource $resources
     */
    public function removeResource(\Ms\OauthBundle\Entity\Resource $resources) {
        $this->resources->removeElement($resources);
    }

    /**
     * Get resources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResources() {
        return $this->resources;
    }


    /**
     * Set owner
     *
     * @param \Ms\OauthBundle\Entity\ResourceOwner $owner
     * @return ResourceGroup
     */
    public function setOwner(\Ms\OauthBundle\Entity\ResourceOwner $owner = null)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Get owner
     *
     * @return \Ms\OauthBundle\Entity\ResourceOwner 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}