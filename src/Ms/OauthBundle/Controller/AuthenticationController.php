<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\SecurityContext;

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
     * @var string
     */
//    const LAST_ID = '_last_id';

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
     * Αυθεντικοποιεί ένα χρήστη του συστήματος.
     * 
     * Αυτή η μέθοδος εμφανίζει στο χρήστη τη Φορμα Πρόσβασης. Ο χρήστης
     * χρειάζεται να συμπληρώσει τα εξής στοιχεία:
     * 
     *  - ID
     *  - Κωδικό Πρόσβασης
     * 
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request) {
        $session = $request->getSession();
        
        $error = null;
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        return $this->render(
            'MsOauthBundle:Authentication:login.html.twig', 
            array(
                'last_id' => $session->get(SecurityContext::LAST_USERNAME),
                'error' => $error
            )
        );
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
        
        $authRequest = AuthorizationRequest::fromUri($request->query->get(AuthorizationRequest::QUERY_PARAM));
        $authRequest->setClientRepository($this->getDoctrine()->getRepository('MsOauthBundle:Client'));
        
        return $this->redirect($this->generateUrl(
            'ms_oauth_authorization', 
            $authRequest->toArray()
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
