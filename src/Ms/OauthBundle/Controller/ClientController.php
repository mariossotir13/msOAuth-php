<?php

namespace Ms\OauthBundle\Controller;

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
     * @return void
     */
    public function demo1AccessTokenMissingRequiredParameterAction() {
        $request = $this->requestGenerator->createAccessTokenRequestWithMissingRequiredParameter(
            static::$AUTHORIZATION_CODE
        );
        
        $response = $this->submitAccessTokenRequest($request);
        if ($this->isAccessTokenErrorResponse($response)) {
            return $this->displayAccessTokenErrorResponse($response);
        }
        
        return $this->buildTemplate();
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
            'MsOauthBundle:Client:demo_1.html.twig', 
//            array('url' => $request->toUri())
            array(
                'errors' => $errors,
                'url_access_token_full' => $this->requestGenerator->createAuthorizationRequest(true),
                'url_access_token_missing_required_parameter' => 
                    $this->generateUrl('ms_oauth_client_demo1_token_request_mrp'),
                'url_authorization_code_invalid_client_id' =>
                    $this->requestGenerator->createAuthorizationRequestWithInvalidClientId(true),
                'url_authorization_code_invalid_redirection_uri' =>
                    $this->requestGenerator->createAuthorizationRequestWithInvalidRedirectionUri(true),
                'url_authorization_code_invalid_scope' =>
                    $this->requestGenerator->createAuthorizationRequestWithInvalidScope(true),
                'url_authorization_code_missing_redirection_uri' =>
                    $this->requestGenerator->createAuthorizationRequestWithMissingRedirectionUri(true),
                'url_authorization_code_missing_required_parameter' =>
                    $this->requestGenerator->createAuthorizationRequestWithMissingRequiredParameter(true),
                'url_authorization_code_unsupported_response_type' =>
                    $this->requestGenerator->createAuthorizationRequestWithUnsupportedResponseType(true)
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
        $content = json_decode($jsonContent);
        
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
        $content = json_decode($jsonContent);
        
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