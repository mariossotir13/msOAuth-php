<?php

namespace Ms\OauthBundle\Controller;

use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// TODO: Να αφαιρεθεί μόλις υλοποιηθεί η Π.Χ. *Αυθεντικοποίηση Ιδιοκτήτη Πόρου*.
use Ms\OauthBundle\Controller\AuthenticationController;

/**
 * Description of AuthorizationController
 *
 * @author Marios
 */
class AuthorizationController extends Controller {
    
    /**
     * @var string
     */
    const AUTHORIZATION_REQUEST_QUERY_PARAM = 'authz_rq';

    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function authorizationCodeAction(Request $request) {
        $authRequest = AuthorizationRequest::fromRequest($request);

        if (!AuthenticationController::isUserAuthenticated($request)) {
            return $this->redirect($this->generateUrl(
                'ms_oauth_authentication_resource_owner', 
                array(AuthorizationRequest::QUERY_PARAM => $authRequest->toUri())
            ));
        }
//        $responseContent = array(
//            $authRequest->getClientId(),
//            $authRequest->getRedirectionUri(),
//            $authRequest->getResponseType(),
//            implode(', ', $authRequest->getScopes()),
//            $authRequest->getState()
//        );
//        $responseContent = implode('<br />', $responseContent);

        return new Response('hello');
    }
}
