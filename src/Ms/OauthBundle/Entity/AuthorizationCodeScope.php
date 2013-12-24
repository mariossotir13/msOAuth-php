<?php

namespace Ms\OauthBundle\Entity;

/**
 * Description of AuthorizationCodeScope
 *
 * @author Marios
 */
class AuthorizationCodeScope {

    /**
     * @var string
     */
    const BASIC = 'basic';

    /**
     * @var string
     */
    const FULL = 'full';

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * 
     * @return string[]
     */
    public static function getValues() {
        return array(
            static::BASIC,
            static::FULL
        );
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Get id
     *
     * @return int 
     */
    public function getId() {
        return $this->id;
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
     * Set description
     *
     * @param string $description
     * @return AuthorizationCodeScope
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return AuthorizationCodeScope
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

}
