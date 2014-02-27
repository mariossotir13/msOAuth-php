<?php

namespace Ms\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ms\OauthBundle\Component\Authorization\AuthorizationErrorResponse;
use Ms\OauthBundle\Component\Authorization\AccessTokenErrorResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    
    /**
     *
     * @var string[]
     */
    protected static $RESPONSE_HEADERS = array(
        'Authorization',
        'Cache-Control',
        'Content-Type',
        'Expires',
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
    public function demoAction(Request $request) {
        if ($this->oauthMediator->isAuthorizationCodeErrorResponse($request)) {
            return $this->displayAuthorizationCodeRequestError($request);
        }
        
        if ($this->oauthMediator->isAuthorizationCodeResponse($request)) {
            $response = $this->oauthMediator->exchangeAuthorizationCodeForAccessToken(
                $this->oauthMediator->getAuthorizationCodeFromResponse($request)
            );
            
            if ($this->oauthMediator->isAccessTokenErrorResponse($response)) {
                return $this->displayAccessTokenErrorResponse($response);
            }
            
            return $this->redirect($this->oauthMediator->getReferer());
        }
        
        return $this->buildTemplate();
    }
    
    /**
     * 
     * @param string $name
     * @return Response
     */
    public function imageAction($name) {
        $accessToken = $this->oauthMediator->getAccessToken();
        if (empty($accessToken)) {
            return $this->oauthMediator->requestAuthorizationCode();
        }
        
        $response = $this->sendResourceAccessRequest('image/jpg', $name, $accessToken);
       
        return new Response(
            $response->getContent(),
            $response->getStatusCode(),
            $response->headers->all()
        );
    }
    
    /**
     * 
     * @param Request $request
     * @param string $name
     * @return Response
     */
    public function imageGroupAction(Request $request, $name = 'Van Gogh Paintings') {
        $accessToken = $this->oauthMediator->getAccessToken();
        if (empty($accessToken)) {
            return $this->oauthMediator->requestAuthorizationCode();
        }
        
        $name = $request->request->get('name') ?: $name;
        $response = $this->sendResourceAccessRequest('group/image/jpg', $name, $accessToken);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return $this->displayAccessTokenErrorResponse($response);
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
     * @param array $errors
     * @return void
     */
    protected function buildTemplate(array $errors = array()) {
        return $this->render(
            'MsDemoBundle:Client:demo_1.html.twig', 
            array(
                'errors' => $errors,
                'url_access_token_full' => $this->requestGenerator->createAuthorizationRequest(true)
            )
        );
    }
    
    /**
     * 
     * @param Response $response
     * @return Response
     */
    protected function displayAccessTokenErrorResponse(Response $response) {
        if ($this->oauthMediator->isUnauthenticatedResponse($response)) {
            return $this->buildTemplate(array('Bad client credentials.'));
        }
        
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
    protected function displayAuthorizationCodeRequestError(Request $request) {
        $error = $request->query->get(AuthorizationErrorResponse::ERROR);
        $error .= $request->query->has(AuthorizationErrorResponse::ERROR_DESCRIPTION)
            ? ': ' . $request->query->get(AuthorizationErrorResponse::ERROR_DESCRIPTION)
            : '';
        
        return $this->buildTemplate(array($error));
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
}