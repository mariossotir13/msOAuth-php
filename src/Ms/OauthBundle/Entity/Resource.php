<?php

namespace Ms\OauthBundle\Entity;

/**
 * Description of Resource
 *
 * @author Marios
 */
class Resource {

    /**
     *
     * @var string
     */
    private $content;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groups;

    /**
     * @var integer
     */
    private $id;

    /**
     *
     * @var string
     */
    private $mimeType;

    /**
     *
     * @var string
     */
    private $title;

    /**
     * Constructor
     */
    public function __construct() {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add groups
     *
     * @param \Ms\OauthBundle\Entity\ResourceGroup $groups
     * @return Resource
     */
    public function addGroup(\Ms\OauthBundle\Entity\ResourceGroup $groups) {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent() {
        return $this->content;
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
     * @return Resource
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
     * Set mimeType
     *
     * @param string $mimeType
     * @return Resource
     */
    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType() {
        return $this->mimeType;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Resource
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Ms\OauthBundle\Entity\ResourceGroup $groups
     */
    public function removeGroup(\Ms\OauthBundle\Entity\ResourceGroup $groups) {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups() {
        return $this->groups;
    }

}
