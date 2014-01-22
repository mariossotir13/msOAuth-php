<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Component\Authorization\AuthorizationResponseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Component\Authorization\AccessTokenRequest;

/**
 * Description of ClientController
 *
 * @author Marios
 */
class ClientController extends Controller {
    
    /**@#+
     *
     * @var string
     */
    private static $CLIENT_ID= 'zMuobKhbnvJUTYc+EnXfRwiiHP4/OpmM5CLrdpkIsm4';
    private static $REDIRECTION_URI = 'http://msoauthphp.local/app_dev.php/client-app/demo1';
    private static $STATE = 'RdoTKJnaUxdRfE7QBTZX';
    /**@#-*/

    /**
     * 
     * @return Response
     */
    public function demo1Action(Request $request) {
        if ($this->isAuthorizationCodeResponse($request)) {
            return $this->exchangeCodeForToken($request->query->get('code'));
        }
        if ($this->isAccessTokenResponse($request)) {
            return new Response('Access token granted!');
        }
        
        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
        $request->addScope(AuthorizationCodeScope::BASIC);
        $request->setClientId(static::$CLIENT_ID);
        $request->setRedirectionUri(static::$REDIRECTION_URI);
        $request->setResponseType(AuthorizationResponseType::CODE);
        $request->setState(static::$STATE);
        
        return $this->render(
            'MsOauthBundle:Client:demo_1.html.twig', 
            array('url' => $request->toUri())
        );
    }
    
    /**
     * 
     * @param string $code
     * @return Response
     */
    private function exchangeCodeForToken($code) {
        $tokenRequest = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $tokenRequest->setClientId(static::$CLIENT_ID);
        $tokenRequest->setCode($code);
        $tokenRequest->setGrantType('authorization_code');
        $tokenRequest->setRedirectionUri(static::$REDIRECTION_URI);
        
        /* @var $buzz \Buzz\Browser */
        $buzz = $this->container->get('buzz');
        $response = $buzz->submit(
            AccessTokenRequest::SERVER_URI, 
            $tokenRequest->toArray(),
            'POST'
        );
        
        $content = $response->getContent();
        $token = $content['access_token'] ?: 'no code';
        
        return new Response('access token: ' . $token);
    }
    
    /**
     * 
     * @param Request $request
     * @return boolean
     */
    private function isAccessTokenResponse(Request $request) {
        $jsonContent = $request->getContent();
        $content = json_decode($jsonContent);
        
        return isset($content['access_token']);
    }
    
    /**
     * 
     * @param Request $request
     * @return boolean
     */
    private function isAuthorizationCodeResponse(Request $request) {
        return $request->query->has('code');
    }
}