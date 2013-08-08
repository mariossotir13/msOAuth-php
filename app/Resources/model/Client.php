<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Client
 *
 * @author Marios
 */
class Client extends User {

    private $appTitle;
    private $redirectionUri;
    private $type;
    private $id;

    function __construct() {
        parent::__construct();
    }

    public function getAppTitle() {
        return $this->appTitle;
    }

    public function setAppTitle($appTitle) {
        $this->appTitle = $appTitle;
    }

    public function getRedirectionUri() {
        return $this->redirectionUri;
    }

    public function setRedirectionUri(Uri $redirectionUri) {
        $this->redirectionUri = $redirectionUri;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

}

?>
