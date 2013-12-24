<?php

namespace Ms\OauthBundle\Controller;

use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Controller\AuthenticationController;
use Ms\OauthBundle\Entity\Client;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Component\Authorization\AuthorizationErrorResponse;
use Ms\OauthBundle\Component\Authorization\AuthorizationError;

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
    public function accessDeniedAction(Request $request) {
        $authResponse = new AuthorizationErrorResponse();
        $authResponse->setError(AuthorizationError::ACCESS_DENIED);
        $authResponse->setErrorDescription('The owner denied access to her resources.');
        
        $authRequest = AuthorizationRequest::fromUri($request->query->get(AuthorizationRequest::QUERY_PARAM));
        $authResponse->setState($authRequest->getState());
        
        $redirectionUri = $authRequest->getRedirectionUri();
        $url = $redirectionUri . '?' . $authResponse->toUri();
        
        return $this->redirect($url);
    }

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
        
        return $this->redirect($this->generateUrl(
            'ms_oauth_authorization_acceptance', 
            array(AuthorizationRequest::QUERY_PARAM => $authRequest->toUri())
        ));

        return new Response('Code granted!');
    }
    
    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function resourceOwnerAcceptanceAction(Request $request) {
        $authRequestQueryParam = $request->query->get(AuthorizationRequest::QUERY_PARAM);
        $authRequest = AuthorizationRequest::fromUri($authRequestQueryParam);
        
        return $this->render(
            'MsOauthBundle:Authorization:request_acceptance_page.html.twig',
            array(
                AuthorizationRequest::QUERY_PARAM => $authRequestQueryParam,
                'client' => $this->getClientFromRequest($authRequest),
                'scopes' => $this->getScopesFromRequest($authRequest)
            )
        );
    }
    
    /**
     * 
     * @param AuthorizationRequest $request
     * @return Client
     */
    private function getClientFromRequest(AuthorizationRequest $request) {
        $clientId = $request->getClientId();
        $clientRepo = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\Client');

        return $clientRepo->find($clientId);
    }
    
    /**
     * 
     * @param AuthorizationRequest $request
     * @return AuthorizationCodeScope
     */
    private function getScopesFromRequest(AuthorizationRequest $request) {
        $scopeRepo = $this->getDoctrine()->getRepository('Ms\OauthBundle\Entity\AuthorizationCodeScope');
        $scopesTitles = $request->getScopes();
        $scopes = array();
        foreach ($scopesTitles as $title) {
            $scopes[] = $scopeRepo->findOneByTitle($title);
        }
        
        return $scopes;
    }
}
