<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccessTokenResponse
 *
 * @author Marios
 */
class AccessTokenResponse extends Response{
    private $accessToken;
    private $refreshToken;
    
    function __construct() {
        parent::__construct();
    }

}

?>
