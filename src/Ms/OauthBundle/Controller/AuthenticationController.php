<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Description of AuthenticationController
 *
 * @author Marios
 */
class AuthenticationController extends Controller {

    /**
     * @var string
     */
    const AUTH_TOKEN = 'ro_auth_token';

    /**
     * Αυτή η μέθοδος θα φύγει όταν ολοκληρωθεί η υλοποίηση της Π.Χ. *Αυθεντικοποίηση
     * Ιδιοκτήτη Πόρου*.
     * 
     * @param Request $request
     * @return bool
     */
    public static function isUserAuthenticated(Request $request) {
        $authToken = $request->cookies->get(static::AUTH_TOKEN);

        return $authToken !== null;
    }

    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function resourceOwnerAction(Request $request) {
        // TODO: Authenticate the Resource Owner.
        $authenticated = static::isUserAuthenticated($request);
        if (!$authenticated) {
            $this->authenticateUser();
            
            return $this->render(
                'MsOauthBundle:Authentication:resource_owner.html.twig', 
                array(AuthorizationRequest::QUERY_PARAM => $request->query->get(AuthorizationRequest::QUERY_PARAM))
            );
        }
        
        $request = AuthorizationRequest::fromUri(
            $request->query->get(AuthorizationRequest::QUERY_PARAM),
            $this->getDoctrine()->getRepository('MsOauthBundle:Client')
        );
        
        return $this->redirect($this->generateUrl(
            'ms_oauth_authorization', 
            $request->toArray()
        ));
    }
    
    /**
     * Παρόμοια με τη μέθοδο *isUserAuthenticated*.
     */
    private function authenticateUser() {
        $authCookie = new Cookie(static::AUTH_TOKEN, 'authenticated');
        $response = new Response();
        $response->headers->setCookie($authCookie);
        $response->sendHeaders();
    }
}
