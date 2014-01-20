<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Ms\OauthBundle\Component\Authorization\AccessTokenRequest;

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
    const AUTH_ERROR = 'error';
    
    /**
     * @var string
     */
//    const LAST_ID = '_last_id';

    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function clientLoginAction(Request $request) {
        $session = $request->getSession();
        
        return $this->render(
            'MsOauthBundle:Authentication:login.html.twig', 
            array(
                'last_id' => $session->get(SecurityContext::LAST_USERNAME),
                'error' => $this->validateForm($request),
                'check_path' => $this->generateUrl('ms_oauth_authentication_client_login_check')
            )
        );
    }
    
    /**
     * 
     * @param Request $request
     * @return Response
     */
    public function clientLoginFailAction(Request $request) {
        $providerKey = $this->container->getParameter('security_provider_key_client');
        $session = $request->getSession();
        $targetPath = $session->get('_security.' . $providerKey . '.target_path');
        $error = $this->validateForm($request);
        
        $authzLoginAttempt = preg_match('#oauth2/c/authorization/access_token#', $targetPath);
        if ($authzLoginAttempt) {
            return $this->redirect(
                $this->generateUrl(
                    'ms_oauth_authorization_invalid_client', 
                    array(
                        AccessTokenRequest::QUERY_PARAM => $targetPath,
                        self::AUTH_ERROR => $error
                    )
                )
            );
        }
        
        return $this->clientLoginAction($request);
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
        
        return $this->render(
            'MsOauthBundle:Authentication:login.html.twig', 
            array(
                'last_id' => $session->get(SecurityContext::LAST_USERNAME),
                'error' => $this->validateForm($request),
                'check_path' => $this->generateUrl('ms_oauth_authentication_user_login_check')
            )
        );
    }

    /**
     * 
     * @param Request $request
     * @return string
     */
    protected function validateForm(Request $request) {
        $session = $request->getSession();
        
        $error = null;
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        return $error;
    }
}
