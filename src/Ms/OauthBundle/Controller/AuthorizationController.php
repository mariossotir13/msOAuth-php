<?php

namespace Ms\OauthBundle\Controller;

use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AuthorizationController
 *
 * @author Marios
 */
class AuthorizationController extends Controller {
    
    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function authorizationCodeAction(Request $request) {
        
        return new Response('hello');
    }
}

