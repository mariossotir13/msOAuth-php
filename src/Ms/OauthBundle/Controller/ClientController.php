<?php

namespace Ms\OauthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Component\Authorization\AuthorizationResponseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Component\Authorization\AccessTokenRequest;
use Buzz\Message\MessageInterface;

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
    private static $PASSWORD = 'fmodKwVrRQOC2Io7TpWu0VDkTrA=';
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
//        if ($this->isAccessTokenResponse($request)) {
//            return new Response('Access token granted!');
//        }
        
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
     * @return AccessTokenRequest
     */
    private function createAccessTokenRequest($code) {
        $tokenRequest = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
        $tokenRequest->setClientId(static::$CLIENT_ID);
        $tokenRequest->setCode($code);
        $tokenRequest->setGrantType('authorization_code');
        $tokenRequest->setRedirectionUri(static::$REDIRECTION_URI);
        
        return $tokenRequest;
    }
    
    /**
     * 
     * @param string $code
     * @return Response
     */
    private function exchangeCodeForToken($code) {
        $response = $this->sendAccessTokenRequest($code);        
        $content = json_decode($response->getContent(), true);
        if (isset($content['access_token'])) {
            return new Response('Access Token: ' . $content['access_token']);
        }
        
        return new Response('Error: ' . $content['error'] . '<br />'
            . 'Error Description: ' . $content['error_description']);
    }
    
    /**
     * 
     * @param MessageInterface $response
     * @return string
     * @throws \BadMethodCallException
     */
//    private function extractRealmFromResponse(MessageInterface $response) {
//        $authHeader = $response->getHeader('WWW-Authenticate');
//        $realmPattern = '/realm="([^"]+)"/';
//        $matches = array();
//        $realm = '';
//        $found = preg_match($realmPattern, $authHeader, $matches);
//        if (!$found) {
//            throw new \BadMethodCallException('No authentication header found in response.');
//        }
//        
//        return $matches[1];
//    }
    
    /**
     * 
     * @param Request $request
     * @return boolean
     */
//    private function isAccessTokenResponse(Request $request) {
//        $content = json_decode($request->getContent());
//        
//        return isset($content['access_token']);
//    }
    
    /**
     * 
     * @param Request $request
     * @return boolean
     */
    private function isAuthorizationCodeResponse(Request $request) {
        return $request->query->has('code');
    }
    
    /**
     * 
     * @param MessageInterface $response
     * @return boolean
     */
    private function isUnauthorizedResponse(MessageInterface $response) {
        return $response->getHeader('WWW-Authenticate') !== '';
    }
    
    /**
     * 
     * @param string $code
     * @return MessageInterface
     */
    private function sendAccessTokenRequest($code) {
        $tokenRequest = $this->createAccessTokenRequest($code);
        /* @var $buzz \Buzz\Browser */
        $buzz = $this->container->get('buzz');
        $response = $buzz->submit(
            AccessTokenRequest::SERVER_URI, 
            $tokenRequest->toArray(),
            'POST'
        );
        
        if ($this->isUnauthorizedResponse($response)) {
            return $this->sendAccessTokenRequestWithCredentials($tokenRequest);
        }
        
        return $response;
    }
    
    /**
     * 
     * @return MessageInterface
     */
    private function sendAccessTokenRequestWithCredentials(AccessTokenRequest $tokenRequest) {
        $credentials = base64_encode(static::$CLIENT_ID . ':' . static::$PASSWORD);
        
        /* @var $buzz \Buzz\Browser */
        $buzz = $this->container->get('buzz');
        /* @var $response \Buzz\Message\Response */
        $response = $buzz->submit(
            AccessTokenRequest::SERVER_URI,
            $tokenRequest->toArray(),
            'POST',
            array('Authorization' => 'Basic ' . $credentials)
        );
        
        return $response;
    }
}