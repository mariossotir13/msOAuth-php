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
use Ms\DemoBundle\Component\Ms\Oauth\RequestGenerator;
use Buzz\Browser;
use Buzz\Exception\ClientException;
use Buzz\Message\Response as BuzzResponse;
use Ms\DemoBundle\Component\Ms\Oauth\OauthMediator;

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
    private static $ACCESS_TOKEN = '1wRAhqWY+WWy8RhlfIOjP9JCTy3ibrWMhaJ6DzjD9BU';
    private static $ACCESS_TOKEN_INVALID = '1wRAhq';
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
     * @var string[]
     */
    protected static $RESPONSE_HEADERS = array(
        'Authorization',
        'Content-Type',
        'WWW-Authenticate'
    );
    
    /**
     *
     * @var OauthMediator
     */
    private $oauthMediator;
    
    /**
     *
     * @var type 
     */
    private $requestGenerator;
    
    /**
     * 
     */
    function __construct() {
        set_time_limit(0);
        $this->requestGenerator = new RequestGenerator();
        $this->oauthMediator = new OauthMediator($this);
    }

    /**
     * 
     * @return Response
     */
    public function demo1Action(Request $request) {
        if ($this->isAuthorizationErrorResponse($request)) {
            return $this->displayAuthorizationError($request);
        }
        
        if ($this->isAuthorizationCodeResponse($request)) {
            return $this->exchangeCodeForToken($request->query->get('code'));
        }
        
        return $this->buildTemplate();
    }
    
    /**
     * 
     * @param string $name
     * @return Response
     */
    public function imageAction($name) {
        $response = $this->sendResourceAccessRequest('image/jpg', $name);
        if ($this->oauthMediator->isUnauthorizedResponse($response)) {
            return $this->oauthMediator->requestAccessToken();
        }
       
        return new Response(
           $response->getContent(),
           $response->getStatusCode(),
           array('Content-Type' => $response->headers->get('Content-Type'))
        );
    }
    
    /**
     * 
     * @param string $name
     * @return Response
     */
    public function imageGroupAction($name) {
        $response = $this->sendResourceAccessRequest('group/image/jpg', $name, static::$ACCESS_TOKEN);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return new Response($response->getContent(), $response->getStatusCode());
        }
        
        $responseContent = json_decode($response->getContent(), true);
        
        return $this->render(
            'MsDemoBundle:Client:slideshow.html.twig',
            array(
                'group_title' => $name,
                'image_titles' => $responseContent['image_titles']
            )
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
     * @return Browser
     */
    protected function getBrowser() {
        /* @var $browser Browser */
        $browser = $this->get('buzz');
        
        /* @var $client \Buzz\Client\Curl */
        $client = $browser->getClient();
        $client->setOption(CURLOPT_CONNECTTIMEOUT, 400);
        $client->setOption(CURLOPT_TIMEOUT, 400);
        $browser->setClient($client);
        
        return $browser;
    }
    
    /**
     * 
     * @param Response $response
     * @return bool
     */
    protected function isUnauthorizedResponse(Response $response) {
        return $response->headers->has('WWW-Authenticate');
    }
    
    /**
     * 
     * @param string $path
     * @param string $name
     * @param string $token
     * @return Response
     */
    protected function sendResourceAccessRequest($path, $name, $token = '') {
        $path = trim($path, '/');
        
        $headers = array();
        if (!empty($token)) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        
        try {
            /* @var $response BuzzResponse */
            $response = $this->getBrowser()->get(
                'http://msoauthphp.local/app_dev.php/resource/' . $path . '/' . rawurlencode($name),
                $headers
            );
        } catch (ClientException $ex) {
            return new Response(
               $ex->getMessage() . ' in file "' . $ex->getFile() . '" at line ' . $ex->getLine(), 
               Response::HTTP_BAD_REQUEST
            );
        }
        
        return $this->transformBuzzToSymfonyResponse($response);
    }
    
    /**
     * 
     * @param BuzzResponse $response
     * @return Response
     */
    protected function transformBuzzToSymfonyResponse(BuzzResponse $response) {
        $headers = array();
        foreach (static::$RESPONSE_HEADERS as $name) {
            if ($response->getHeader($name)) {
                $headers[$name] = $response->getHeader($name);
            }
        }
        
        return new Response($response->getContent(), $response->getStatusCode(), $headers);
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
     * @deprecated
     */
//    private function isUnauthorizedResponse(MessageInterface $response) {
//        return $response->getHeader('WWW-Authenticate') !== '';
//    }
    
    /**
     * 
     * @param string $url
     * @param string $token
     * @return Response
     */
    private function requestResource($url, $token) {
        /* @var $buzz Browser */
        $buzz = $this->get('buzz');
        $response = $buzz->get($url, array('Authorization' => 'Bearer ' . $token));
        
        return new Response(
           $response->getContent(),
           Response::HTTP_OK,
           array('Content-Type' => $response->getHeader('Content-Type'))
        );
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