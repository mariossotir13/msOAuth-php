<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AuthenticationController
 *
 * @author Marios
 */
class AuthenticationController extends Controller {
    
    /**
     * @var string
     */
    const PARAM_ACCEPT_ANSWER = 'answer';

    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function resourceOwnerAction(Request $request) {
        // TODO
        return new Response(static::PARAM_ACCEPT_ANSWER);
    }
}