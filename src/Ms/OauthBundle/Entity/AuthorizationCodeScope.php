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