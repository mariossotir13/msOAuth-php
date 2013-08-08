<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizationRequest
 *
 * @author Marios
 */
class AuthorizationRequest extends Request {

    private $clientId;
    private $scope;

    function __construct() {
        parent::__construct();
        $this->redirectionUri = $redirectionUri;
    }

}

?>
