<?php

namespace Ms\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Component\Authorization\AuthorizationRequest;
use Ms\OauthBundle\Entity\AuthorizationCodeScope;
use Ms\OauthBundle\Component\Authorization\AuthorizationResponseType;
use Ms\OauthBundle\Component\Authorization\AuthorizationErrorResponse;
use Ms\OauthBundle\Component\Authorization\AccessTokenErrorResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ms\OauthBundle\Component\Authorization\AccessTokenRequest;
use Buzz\Message\MessageInterface;
use Ms\OauthBundle\Component\Demo\Demo1\RequestGenerator;
use Buzz\Browser;

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
    private static $AUTHORIZATION_CODE = 'some+authorization_code';
    private static $AUTHORIZATION_CODE_EXPIRED = '2DJaB1A7VsbFmr1H3AkV/DLCR9s7rBPtHb5R/wqK9O4';
    private static $AUTHORIZATION_CODE_REUSED = 'ECVkbAobtKSh9IN98WBcpAV4k3s6HXHh/bibF80MKus';
    private static $AUTHORIZATION_CODE_WRONG_CLIENT_ID = 'ECVkbAobtKSh9IN98WBcpAV4k3s6HXHh/bibF80MKus';
    private static $CLIENT_ID= 'zMuobKhbnvJUTYc+EnXfRwiiHP4/OpmM5CLrdpkIsm4';
    private static $REDIRECTION_URI = 'http://msoauthphp.local/app_dev.php/client-app/demo1';
    private static $PASSWORD = 'fmodKwVrRQOC2Io7TpWu0VDkTrA=';
    private static $STATE = 'RdoTKJnaUxdRfE7QBTZX';
    /**@#-*/
    
    /**
     *
     * @var type 
     */
    private $requestGenerator;
    
    /**
     * 
     */
    function __construct() {
       $this->requestGenerator = new RequestGenerator();
    }
    
    /**
     * 
     * @return void
     */
    public function demo1AccessTokenExpiredGrantAction() {
        $request = $this->requestGenerator->createAccessTokenRequest(
            static::$AUTHORIZATION_CODE_EXPIRED
        );
        
        return $this->handleAccessTokenInvalidRequest($request);
    }
    
    /**
     * 
     * @return void
     */
    public function demo1AccessTokenInvalidRedirectionUriAction() {
        $request = $this->requestGenerator->createAccessTokenRequestWithInvalidRedirectionUri(
            static::$AUTHORIZATION_CODE
        );
        
        return $this->handleAccessTokenInvalidRequest($request);
    }
    
    /**
     * 
     * @return void
     */
    public function demo1AccessTokenMissingRedirectionUriAction() {
        $request = $this->requestGenerator->createAccessTokenRequestWithMissingRedirectionUri(
            static::$AUTHORIZATION_CODE
        );
        
        return $this->handleAccessTokenInvalidRequest($request);
    }
    
    /**
     * 
     * @return void
     */
    public function demo1AccessTokenMissingRequiredParameterAction() {
        $request = $this->requestGenerator->createAccessTokenRequestWithMissingRequiredParameter(
            static::$AUTHORIZATION_CODE
        );
        
        return $this->handleAccessTokenInvalidRequest($request);
    }
    
    /**
     * 
     * @return void
     */
    public function demo1AccessTokenReusedGrantAction() {
        $request = $this->requestGenerator->createAccessTokenRequest(static::$AUTHORIZATION_CODE_REUSED);
        
        return $this->handleAccessTokenInvalidRequest($request);
    }
    
    /**
     * 
     * @return void
     */
    public function demo1AccessTokenUnsupportedGrantTypeAction() {
        $request = $this->requestGenerator->createAccessTokenRequestWithUnsupportedGrantType(
            static::$AUTHORIZATION_CODE
        );
        
        return $this->handleAccessTokenInvalidRequest($request);
    }
    
    /**
     * 
     * @return void
     */
    public function demo1AccessTokenWrongClientIdAction() {
        $request = $this->requestGenerator->createAccessTokenRequestWithWrongClientId(
            static::$AUTHORIZATION_CODE_WRONG_CLIENT_ID
        );
        
        return $this->handleAccessTokenInvalidRequest($request);
    }

    /**
     * 
     * @return Response
     */
    public function demo1Action(Request $request) {
        if ($this->isAuthorizationErrorResponse($request)) {
            return $this->displayAuthorizationError($request);
        }
        
            
//        if ($this->isAccessTokenErrorResponse($request)) {
//            $this->displayAccessTokenErrorResponse($request);
//        }
        
        if ($this->isAuthorizationCodeResponse($request)) {
            return $this->exchangeCodeForToken($request->query->get('code'));
        }
        
//        if ($this->isAccessTokenResponse($request)) {
//            return new Response('Access token granted!');
//        }
        
//        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
//        $request->addScope(AuthorizationCodeScope::BASIC);
//        $request->setClientId(static::$CLIENT_ID);
//        $request->setRedirectionUri(static::$REDIRECTION_URI);
//        $request->setResponseType(AuthorizationResponseType::CODE);
//        $request->setState(static::$STATE);
        
        return $this->buildTemplate();
    }
    
    /**
     * 
     * @return Response
     */
    public function image1Action() {
        /* @var $buzz Browser */
       $buzz = $this->get('buzz');
       $response = $buzz->get(
            'http://msoauthphp.local/app_dev.php/resource/image/jpg/1',
            array('Authorization' => 'Bearer 1wRAhqWY+WWy8RhlfIOjP9JCTy3ibrWMhaJ6DzjD9BU')
        );
       
       return new Response(
           $response->getContent(),
           Response::HTTP_OK,
           array('Content-Type' => $response->getHeader('Content-Type'))
        );
    }
    
    /**
     * 
     * @param string $code
     * @return AccessTokenRequest
     */
//    private function createAccessTokenRequest($code) {
//        $tokenRequest = new AccessTokenRequest(AccessTokenRequest::SERVER_URI);
//        $tokenRequest->setClientId(static::$CLIENT_ID);
//        $tokenRequest->setCode($code);
//        $tokenRequest->setGrantType('authorization_code');
//        $tokenRequest->setRedirectionUri(static::$REDIRECTION_URI);
//        
//        return $tokenRequest;
//    }
    
    /**
     * 
     * @return string
     */
//    private function createUrlForFullAccessTokenScenario() {
//        $request = new AuthorizationRequest(AuthorizationRequest::SERVER_URI);
//        $request->addScope(AuthorizationCodeScope::BASIC);
//        $request->setClientId(static::$CLIENT_ID);
//        $request->setRedirectionUri(static::$REDIRECTION_URI);
//        $request->setResponseType(AuthorizationResponseType::CODE);
//        $request->setState(static::$STATE);
//        
//        return $request->toUri();
//    }
    
    /**
     * 
     * @param array $errors
     * @return void
     */
    private function buildTemplate(array $errors = array()) {
        return $this->render(
            'MsDemoBundle:Client:demo_1.html.twig', 
//            array('url' => $request->toUri())
            array(
                'url_access_token_full' => $this->requestGenerator->createAuthorizationRequest(true)
            )
        );
    }
    
    /**
     * 
     * @param MessageInterface $response
     * @return Response
     */
    private function displayAccessTokenErrorResponse(MessageInterface $response) {
        $jsonContent = $response->getContent();
        $content = json_decode($jsonContent, true);
        
        $error = $content[AccessTokenErrorResponse::ERROR];
        $error .= isset($content[AccessTokenErrorResponse::ERROR_DESCRIPTION])
            ? ': ' . $content[AccessTokenErrorResponse::ERROR_DESCRIPTION]
            : '';
        
        return $this->buildTemplate(array($error));
    }
    
    /**
     * 
     * @param Request $request
     * @return Response
     */
    private function displayAuthorizationError(Request $request) {
        $error = $request->query->get(AuthorizationErrorResponse::ERROR);
        $error .= $request->query->has(AuthorizationErrorResponse::ERROR_DESCRIPTION)
            ? ': ' . $request->query->get(AuthorizationErrorResponse::ERROR_DESCRIPTION)
            : '';
        
        return $this->buildTemplate(array($error));
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
     * @param AccessTokenRequest $request
     * @return Response
     */
    private function handleAccessTokenInvalidRequest(AccessTokenRequest $request) {
        $response = $this->submitAccessTokenRequest($request);
        if ($this->isUnauthorizedResponse($response)) {
            $response = $this->sendAccessTokenRequestWithCredentials($request);
        }
        if ($this->isAccessTokenErrorResponse($response)) {
            return $this->displayAccessTokenErrorResponse($response);
        }
        
        return $this->buildTemplate();
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
     * @param MessageInterface $response
     * @return boolean
     */
    private function isAccessTokenErrorResponse(MessageInterface $response) {
        $jsonContent = $response->getContent();
        $content = json_decode($jsonContent, true);
        
        return isset($content[AccessTokenErrorResponse::ERROR]);
    }
    
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
     * @param Request $request
     * @return boolean
     */
    private function isAuthorizationErrorResponse(Request $request) {
        return $request->query->has(AuthorizationErrorResponse::ERROR);
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
        $request = $this->requestGenerator->createAccessTokenRequest($code);
        /* @var $buzz \Buzz\Browser */
        $buzz = $this->container->get('buzz');
        $response = $buzz->submit(
            AccessTokenRequest::SERVER_URI, 
            $request->toArray(),
            'POST'
        );
        
        if ($this->isUnauthorizedResponse($response)) {
            return $this->sendAccessTokenRequestWithCredentials($request);
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
    
    /**
     * 
     * @param AccessTokenRequest $request
     * @return MessageInterface
     */
    private function submitAccessTokenRequest(AccessTokenRequest $request) {
        /* @var $buzz \Buzz\Browser */
        $buzz = $this->container->get('buzz');
        
        return $buzz->submit(
            AccessTokenRequest::SERVER_URI,
            $request->toArray(),
            'POST'
        );
    }
}