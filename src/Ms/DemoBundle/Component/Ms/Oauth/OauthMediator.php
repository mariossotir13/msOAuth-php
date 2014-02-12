<?php

namespace Ms\DemoBundle\Component\Ms\Oauth;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\DemoBundle\Component\Ms\Oauth\RequestGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Buzz\Browser;
use Buzz\Message\Response as BuzzResponse;
use Ms\OauthBundle\Component\Authorization\AuthorizationErrorResponse;
use Symfony\Component\HttpFoundation\Request;
use Ms\OauthBundle\Component\Authorization\AccessTokenRequest;
use Ms\OauthBundle\Component\Authorization\AccessTokenResponse;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Description of OauthMediator
 *
 * @author Marios
 */
class OauthMediator {
    
    /**
     *
     * @var string
     */
    private static $ACCESS_TOKEN_FILE_INDEX_TOKEN = 'token';

    /**#@+
     * 
     * @var string
     */
    private static $CLIENT_ID= 'zMuobKhbnvJUTYc+EnXfRwiiHP4/OpmM5CLrdpkIsm4';
    private static $PASSWORD = 'fmodKwVrRQOC2Io7TpWu0VDkTrA=';
    /**#@-*/
    
    /**
     *
     * @var string[]
     */
    private static $RESPONSE_HEADERS = array(
        'Authorization',
        'Content-Type',
        'WWW-Authenticate'
    );
    
    /**
     *
     * @var string
     */
    private static $SESSION_KEY_REFERER = 'ms_demo_referer';
    
    /**
     *
     * @var string
     */
    private $accessToken;
    
    /**
     *
     * @var Controller
     */
    private $controller;
    
    /**
     *
     * @var RequestGenerator
     */
    private $requestGenerator;
    
    /**
     * 
     * @param Controller $controller
     */
    function __construct(Controller $controller) {
        $this->controller = $controller;
        $this->requestGenerator = new RequestGenerator();
    }
    
    /**
     * 
     * @param string $code
     * @return Response
     */
    public function exchangeAuthorizationCodeForAccessToken($code) {
//        $response = $this->sendAccessTokenRequest($code);        
//        $content = json_decode($response->getContent(), true);
//        if (isset($content['access_token'])) {
//            return new Response('Access Token: ' . $content['access_token']);
//        }
//        
//        return new Response('Error: ' . $content['error'] . '<br />'
//            . 'Error Description: ' . $content['error_description']);
        
        $response = $this->sendAccessTokenRequest($code);
        if (!$this->isAccessTokenErrorResponse($response)) {
            $content = json_decode($response->getContent(), true);
            $this->setAccessToken($content['access_token']);
        }
        
        return $response;
    }
    
    /**
     * 
     * @return string
     */
    public function getAccessToken() {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
        }
        
        /* @var $container \Symfony\Component\DependencyInjection\Container */
        $container = $this->controller->get('service_container');
        $filePath = $container->getParameter('access_token_file');
        $fileInfo = new \SplFileInfo($filePath);
        if (!$fileInfo->isFile()) {
            return '';
        }
        
        $file = $fileInfo->openFile();
        $contents = json_decode($file->fgets(), true);
        
        $this->setAccessToken($contents[static::$ACCESS_TOKEN_FILE_INDEX_TOKEN]);
        
        return $this->accessToken;
    }
    
    /**
     * 
     * @param Request $request
     * @return string
     */
    public function getAuthorizationCodeFromResponse(Request $request) {
        return $this->isAuthorizationCodeResponse($request)
            ? $request->query->get('code')
            : '';
    }
    
    /**
     * 
     * @param JsonResponse $response
     * @return return bool
     */
    public function isAccessTokenErrorResponse(JsonResponse $response) {
        $content = json_decode($response->getContent(), true);
        
        return empty($content['access_token']);
    }
    
    /**
     * 
     * @param Request $request
     * @return boolean
     */
    public function isAuthorizationCodeErrorResponse(Request $request) {
        return $request->query->has(AuthorizationErrorResponse::ERROR);
    }
    
    /**
     * 
     * @param Request $request
     * @return bool
     */
    public function isAuthorizationCodeResponse(Request $request) {
        return $request->query->has('code');
    }
    
    /**
     * Επιστρέφει `true` όταν η `$response` δείχνει πως απαιτείται αυθεντικοποίηση.
     * 
     * Π.χ., όταν ο Πελάτης αιτείται *κωδικό εξουσιοδότησης*, ο Εξυπηρετητής
     * Εξουσιοδοτήσεων μπορεί να επιστρέψει μία τέτοια απάντηση, έτσι ώστε να
     * παρακινήσει τον Πελάτη να αυθεντικοποιηθεί.
     * 
     * @param Response $response
     * @return bool
     */
    public function isUnauthenticatedResponse(Response $response) {
        return $response->headers->has('WWW-Authenticate');
    }
    
    /**
     * Επιστρέφει `true` όταν η `$response` δείχνει πως η αίτηση δεν ικανοποιήθηκε
     * γιατί απέτυχε να παρουσιάσει τα απαραίτητα διαπιστευτήρια εξουσιοδότησης.
     * 
     * Π.χ., όταν ο Πελάτης αιτείται έναν Πόρο από έναν Εξυπηρετητή Πόρων ο οποίος
     * υλοποιεί το πρωτόκολλο OAuth και δεν αποστέλλει κάποιο *τεκμήριο πρόσβασης*,
     * τότε η `$response` θα έχει ως αποτέλεσμα αυτή η μέθοδος να επιστρέψει `true`.
     * 
     * @param Response $response
     * @return bool
     */
    public function isUnauthorizedResponse(Response $response) {
        return $response->getStatusCode() === Response::HTTP_UNAUTHORIZED;
    }
    
    /**
     * 
     * @return Response
     */
    public function requestAuthorizationCode() {
        $this->storeReferer();
        $authorizationCodeRequestUrl = $this->requestGenerator->createAuthorizationRequest(true);
        
        return $this->controller->redirect($authorizationCodeRequestUrl);
    }
    
    /**
     * 
     * @return Response
     */
//    public function requestAccessToken() {
//        return $this->requestAuthorizationCode();
//    }
    
    /**
     * 
     * @return Browser
     */
    protected function getBrowser() {
        /* @var $browser Browser */
        $browser = $this->controller->get('buzz');
        
        /* @var $client \Buzz\Client\Curl */
        $client = $browser->getClient();
        $client->setOption(CURLOPT_CONNECTTIMEOUT, 400);
        $client->setOption(CURLOPT_TIMEOUT, 400);
        $browser->setClient($client);
        
        return $browser;
    }
    
    /**
     * 
     * @return string
     */
    public function getReferer() {
        /* @var $session Session */
        $session = $this->controller->get('session');
        
        return $session->get(static::$SESSION_KEY_REFERER);
    }
    
    /**
     * 
     * @param string $authorizationCode
     * @return JsonResponse
     */
    protected function sendAccessTokenRequest($authorizationCode) {
        $request = $this->requestGenerator->createAccessTokenRequest($authorizationCode);
        $browser = $this->getBrowser();
        $buzzResponse = $browser->submit(
            AccessTokenRequest::SERVER_URI, 
            $request->toArray(),
            'POST'
        );

        $response = $this->transformBuzzToSymfonyResponse($buzzResponse, true);
        if ($this->isUnauthenticatedResponse($response)) {
            return $this->sendAccessTokenRequestWithCredentials($request);
        }
        
        return $response;
    }
    
    /**
     * 
     * @return JsonResponse
     */
    protected function sendAccessTokenRequestWithCredentials(AccessTokenRequest $tokenRequest) {
        $credentials = base64_encode(static::$CLIENT_ID . ':' . static::$PASSWORD);
        $browser = $this->getBrowser();
        $buzzResponse = $browser->submit(
            AccessTokenRequest::SERVER_URI,
            $tokenRequest->toArray(),
            'POST',
            array('Authorization' => 'Basic ' . $credentials)
        );
                
        return $this->transformBuzzToSymfonyResponse($buzzResponse, true);
    }
    
    /**
     * 
     * @param string $token
     * @return void
     */
    protected function setAccessToken($token) {
        /* @var $container \Symfony\Component\DependencyInjection\Container */
        $container = $this->controller->get('service_container');
        $filePath = $container->getParameter('access_token_file');
        $fileInfo = new \SplFileInfo($filePath);
        if (!$fileInfo->isFile()) {
            $basePath = $fileInfo->getPath();
            mkdir($basePath, 0777, true);
        }
        
        $file = $fileInfo->openFile('w');
        $contents = json_encode(array(static::$ACCESS_TOKEN_FILE_INDEX_TOKEN => $token));
        $file->fwrite($contents);
        
        $this->accessToken = $token;
    }
    
    /**
     * 
     * @return void
     */
    protected function storeReferer() {
        /* @var $request Request */
        $request = $this->controller->get('request');
        /* @var $session Session */
        $session = $this->controller->get('session');
        $session->set(static::$SESSION_KEY_REFERER, $request->getRequestUri());
    }
    
    /**
     * 
     * @param BuzzResponse $response
     * @param bool $json
     * @return Response|JsonResponse
     */
    protected function transformBuzzToSymfonyResponse(BuzzResponse $response, $json = false) {
        $headers = array();
        foreach (static::$RESPONSE_HEADERS as $name) {
            if ($response->getHeader($name)) {
                $headers[$name] = $response->getHeader($name);
            }
        }
        
        $content = is_string($response->getContent()) ? $response->getContent() : '';
        if ($json) {
            return new JsonResponse(json_decode($content, true), $response->getStatusCode(), $headers);
        }
        
        return new Response($content, $response->getStatusCode(), $headers);
    }
    
    /**
     * 
     * @param Response $response
     * @return JsonResponse
     */
//    protected function transformBuzzToSymfonyJsonResponse(BuzzResponse $response) {
//        $headers = array();
//        foreach (static::$RESPONSE_HEADERS as $name) {
//            if ($response->getHeader($name)) {
//                $headers[$name] = $response->getHeader($name);
//            }
//        }
//        
//        $content = is_array($response->getContent()) ? $response->getContent() : array();
//        
//        return new JsonResponse($content, $response->getStatusCode(), $headers);
//    }
}
